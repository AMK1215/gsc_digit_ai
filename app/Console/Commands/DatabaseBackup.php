<?php

namespace App\Console\Commands;

use App\Services\GoogleDriveService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:db-backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(GoogleDriveService $googleDriveService)
    {
        $dbHost = env('DB_HOST');
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');

        $backupPath = storage_path('app/backups');
        $backupFile = "{$backupPath}/backup_".date('Y-m-d_H-i-s').'.sql';

        if (! file_exists($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $command = "mysqldump -h$dbHost -u$dbUser --password=$dbPass $dbName > $backupFile";
        $output = null;
        $resultCode = null;
        exec($command, $output, $resultCode);

        if ($resultCode === 0) {
            $this->info("Database backup successfully created at $backupFile");

            $fileId = $googleDriveService->uploadToDrive($backupFile, basename($backupFile));
            $this->info("Backup uploaded to Google Drive with file ID: $fileId");

            $this->cleanUpOldBackups($backupPath);
        } else {
            $this->error("Failed to back up the database. Error code: $resultCode");
            $this->error('Command output: '.implode("\n", $output));
        }
    }

    /**
     * @param  string  $backupPath
     */
    protected function cleanUpOldBackups($backupPath)
    {
        $files = Storage::disk('local')->files('backups');
        $currentDate = Carbon::now();

        foreach ($files as $file) {
            $filePath = storage_path("app/$file");

            if (filemtime($filePath) < $currentDate->subDays(30)->timestamp) {
                unlink($filePath);
                $this->info("Deleted old backup: $file");
            }
        }
    }
}

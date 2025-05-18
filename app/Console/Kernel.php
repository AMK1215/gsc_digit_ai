<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\App;
use App\Console\Commands\TriggerGameSpin; // Ensure this import is present

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Commands\PullReport::class,
        // Commands\ArchiveOldWagers::class,
        // Commands\DeleteOldWagerBackups::class,
        Commands\TriggerGameSpin::class // <-- Uncommented this line
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->command('app:pull-report-update-version')->everyMinute();

        //$schedule->command('make:pull-report')->everyFiveSeconds();
        // $schedule->command('archive:old-wagers')->everyThirtyMinutes();
        // $schedule->command('wagers:delete-old-backups')->cron('*/45 * * * *');

        // Schedule the TriggerGameSpin command for each duration.
        // Correct syntax: command name first, then array of arguments.

        $schedule->command('game:trigger-spin', ['duration' => 1])->everyMinute();
        $schedule->command('game:trigger-spin', ['duration' => 3])->everyMinute();
        $schedule->command('game:trigger-spin', ['duration' => 5])->everyMinute();
        $schedule->command('game:trigger-spin', ['duration' => 10])->everyMinute();

        // If you have separate commands per duration and need precise timing:
        // This requires careful logic to ensure they only run when their specific period ends.
        // $schedule->command(TriggerGameSpin::class, ['duration' => 1])->everyMinute();
        // $schedule->command(TriggerGameSpin::class, ['duration' => 3])->cron('*/3 * * * *'); // Every 3 minutes
        // $schedule->command(TriggerGameSpin::class, ['duration' => 5])->cron('*/5 * * * *'); // Every 5 minutes
        // $schedule->command(TriggerGameSpin::class, ['duration' => 10])->cron('*/10 * * * *'); // Every 10 minutes
         // Note: Cron scheduling requires careful consideration of exact start times.
         // Running every minute and checking the current period is often simpler.

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

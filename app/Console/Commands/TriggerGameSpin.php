<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GameApiService;
use App\Models\AiGameResult;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TriggerGameSpin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Accepts a {duration} argument: 1, 3, 5, or 10
     */
    protected $signature = 'game:trigger-spin {duration}';

    /**
     * The console command description.
     */
    protected $description = 'Triggers a game spin for a specific duration and processes results.';

    protected $gameApiService;

    /**
     * Create a new command instance.
     */
    public function __construct(GameApiService $gameApiService)
    {
        parent::__construct();
        $this->gameApiService = $gameApiService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $duration = (int) $this->argument('duration');

        // Validate duration
        if (!in_array($duration, [1, 3, 5, 10])) {
            $this->error("Invalid duration specified: {$duration}. Must be 1, 3, 5, or 10.");
            return Command::FAILURE;
        }

        $this->info("Checking for completed {$duration}-minute game period...");

        $currentTime = Carbon::now();
        $minute = $currentTime->minute;
        $trigger = false;

        if ($duration === 1 ||
            ($duration === 3 && $minute % 3 === 0) ||
            ($duration === 5 && $minute % 5 === 0) ||
            ($duration === 10 && $minute % 10 === 0)) {
            $trigger = true;
        }

        if (!$trigger) {
            $this->info("Not the right time to trigger a {$duration}-minute spin.");
            return Command::SUCCESS;
        }

        $history = AiGameResult::orderBy('period', 'desc')->limit(50)->pluck('winning_digit')->toArray();

        $this->info("Fetching spin result from Python API...");

        $spinResult = $this->gameApiService->spinGame($history);

        if ($spinResult && isset($spinResult['digit'], $spinResult['color'])) {
            $winningDigit = $spinResult['digit'];
            $winningColor = $spinResult['color'];
            $winningSize  = $spinResult['size'] ?? null;

            $periodNumber = Carbon::now()->format('YmdHi') . str_pad($duration, 2, '0', STR_PAD_LEFT);

            $existingResult = AiGameResult::where('period', $periodNumber)->first();
            if ($existingResult) {
                $this->warn("Result for period {$periodNumber} already exists. Skipping.");
                return Command::SUCCESS;
            }

            try {
                AiGameResult::create([
                    'period' => $periodNumber,
                    'duration' => $duration,
                    'winning_digit' => $winningDigit,
                    'winning_color' => $winningColor,
                    'winning_size' => $winningSize,
                ]);

                $this->info("Game result saved for period {$periodNumber}.");
                $this->processBets($periodNumber, $winningDigit, $winningColor, $winningSize);

            } catch (\Exception $e) {
                Log::error("Failed to save result or process bets: {$e->getMessage()}");
                return Command::FAILURE;
            }

            return Command::SUCCESS;
        } else {
            $this->error("Failed to get a valid spin result from Python API.");
            return Command::FAILURE;
        }
    }

    protected function processBets(string $period, int $winningDigit, string $winningColor, ?string $winningSize)
    {
        $this->info("Processing bets for period {$period}...");
        // Add your betting logic here
        $this->info("Bet processing complete.");
    }
}
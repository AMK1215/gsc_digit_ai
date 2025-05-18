<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GameApiService; // Import your GameApiService
use App\Models\AiGameResult; // Import the GameResult model
use Illuminate\Support\Facades\Log;
use Carbon\Carbon; // For date/time calculations

class TriggerGameSpin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * The {duration} argument will specify the game duration (1, 3, 5, 10).
     */
    protected $signature = 'game:trigger-spin {duration : The game duration in minutes (1, 3, 5, 10)}';

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

        // --- Logic to determine if a period for this duration has just ended ---
        // This is a crucial part and depends on how your periods are defined.
        // A common way is to use timestamps and period numbers.
        // Example logic (assuming period numbers are sequential and tied to time):
        $currentTime = Carbon::now();
        // Calculate the period number that should have just ended
        // This calculation depends heavily on your period numbering scheme.
        // Example: If periods change exactly on the minute, every minute.
        // If periods are based on a fixed start time (e.g., midnight UTC) and duration.
        // Let's assume a simple scheme where period numbers are like YYYYMMDDHHMM + a sequence number for sub-minute games
        // Or just a simple incrementing number per duration type.

        // A robust approach: Find the last completed period for this duration from the database.
        $lastResult = AiGameResult::where('duration', $duration)
                                ->orderBy('period', 'desc') // Assuming period is sortable to find the latest
                                ->first();

        $lastPeriodNumber = $lastResult ? $lastResult->period : null;

        // Determine the *expected* next period number that should be spun now.
        // This logic is complex and depends on your exact game rules and period generation.
        // For a simple example, let's assume period numbers are just sequential strings per duration.
        // In reality, you might need a more sophisticated period tracking mechanism.

        // --- Simplified Period Check (Needs refinement based on your game rules) ---
        // This simple check assumes the command runs every minute and triggers if the last result
        // for this duration was more than $duration minutes ago. This is NOT precise.
        // A better way is to calculate the expected period number based on current time
        // and compare it to the last spun period number.

        // Example: If periods end exactly on the minute for 1-min game, every 3rd minute for 3-min, etc.
        $minute = $currentTime->minute;
        $trigger = false;
        if ($duration === 1 && true) { // 1-minute game always triggers every minute
             $trigger = true;
        } elseif ($duration === 3 && $minute % 3 === 0) { // 3-minute game triggers on minutes 0, 3, 6, ...
             $trigger = true;
        } elseif ($duration === 5 && $minute % 5 === 0) { // 5-minute game triggers on minutes 0, 5, 10, ...
             $trigger = true;
        } elseif ($duration === 10 && $minute % 10 === 0) { // 10-minute game triggers on minutes 0, 10, 20, ...
             $trigger = true;
        }

        // Add a check to prevent double-spinning the same period if the cron runs slightly off
        // This requires knowing the *exact* period number that should end now.
        // Let's skip the complex period number calculation here and focus on the API call and saving.
        // You MUST implement robust period number calculation and checking in a real app.


        // --- Assuming it's time to trigger a spin for this duration ---
        // In a real app, you'd only proceed if $trigger is true AND the calculated period number
        // is different from the last spun period number.

        // Fetch historical data for the AI (e.g., last 50 winning digits)
        $history = AiGameResult::orderBy('period', 'desc') // Order by period to get latest
                             ->limit(50) // Get the last 50 results
                             ->pluck('winning_digit') // Get only the winning_digit column
                             ->toArray(); // Convert to array

        $this->info("Fetching spin result from Python API...");

        // Call the Python API to get the spin result
        $spinResult = $this->gameApiService->spinGame($history); // Pass history to Python

        if ($spinResult && isset($spinResult['digit'], $spinResult['color'])) {
            // --- Successfully got result from Python ---
            $winningDigit = $spinResult['digit'];
            $winningColor = $spinResult['color'];
            $winningSize = $spinResult['size'] ?? null; // Assuming size might be in the response

            $this->info("Spin Result: Digit={$winningDigit}, Color={$winningColor}, Size={$winningSize}");

            // --- Generate the period number for this result ---
            // This needs to be consistent with your frontend and game rules.
            // Example: Using a timestamp-based period number
            $periodNumber = Carbon::now()->format('YmdHi') . str_pad($duration, 2, '0', STR_PAD_LEFT); // Example: 20250518094801 (for 1 min game)
            // This is a simplified example. A real game might use a global sequence number or a more complex scheme.
            // Ensure this period number is unique and matches what your frontend expects.

             // Check if this period number already exists to prevent duplicates
             $existingResult = AiGameResult::where('period', $periodNumber)->first();

             if ($existingResult) {
                  $this->warn("Result for period {$periodNumber} already exists. Skipping.");
                  return Command::SUCCESS; // Already processed this period
             }


            // --- Save the game result to the database ---
            try {
                $gameResult = AiGameResult::create([
                    'period' => $periodNumber,
                    'duration' => $duration,
                    'winning_digit' => $winningDigit,
                    'winning_color' => $winningColor,
                    'winning_size' => $winningSize,
                ]);
                $this->info("Game result saved for period {$periodNumber}.");

                // --- Process Bets for this period ---
                // This is where you would query the 'bets' table for bets placed on this 'periodNumber'.
                // Then iterate through the bets, calculate wins/losses based on the $winningDigit, $winningColor, $winningSize.
                // Update user balances and record transactions.
                $this->processBets($periodNumber, $winningDigit, $winningColor, $winningSize); // Call a separate method

            } catch (\Exception $e) {
                Log::error("Failed to save game result or process bets for period {$periodNumber}: {$e->getMessage()}");
                $this->error("Failed to save game result or process bets for period {$periodNumber}.");
                return Command::FAILURE;
            }


            return Command::SUCCESS;
        } else {
            // --- Failed to get result from Python ---
            $this->error("Failed to get a valid spin result from the Python API.");
            // Log the error in the service or here
            return Command::FAILURE;
        }
    }

    /**
     * Process bets placed on a specific game period.
     * This method needs to be implemented based on your Bet model and logic.
     */
    protected function processBets(string $period, int $winningDigit, string $winningColor, ?string $winningSize)
    {
        $this->info("Processing bets for period {$period}...");

        // --- Your Bet Processing Logic Here ---
        // 1. Query the 'bets' table for all bets where 'period' matches $period.
        //    Example: $bets = \App\Models\Bet::where('period', $period)->get();
        // 2. Iterate through each bet.
        // 3. For each bet, check if the user's prediction matches the winning result ($winningDigit, $winningColor, $winningSize).
        // 4. If the bet wins:
        //    - Calculate the payout based on the bet amount and multiplier.
        //    - Add the payout to the user's wallet balance.
        //    - Record a transaction for the win.
        // 5. If the bet loses:
        //    - The bet amount is already deducted when the bet was placed (presumably).
        //    - Record a transaction for the loss (optional, but good for history).
        // 6. Update the status of the bet in the database (e.g., 'won', 'lost').

        // Example placeholder:
        // $bets = \App\Models\Bet::where('period', $period)->get();
        // foreach ($bets as $bet) {
        //     $isWinner = false;
        //     $payout = 0;

        //     if ($bet->bet_type === 'digit' && (int) $bet->prediction === $winningDigit) {
        //         $isWinner = true;
        //         $payout = $bet->amount * 9; // Assuming 9x payout for digit
        //     } elseif ($bet->bet_type === 'color' && $bet->prediction === $winningColor) {
        //         $isWinner = true;
        //         $payout = $bet->amount * 2; // Assuming 2x payout for color
        //     } elseif ($bet->bet_type === 'size' && $bet->prediction === $winningSize) {
        //          $isWinner = true;
        //          $payout = $bet->amount * 2; // Assuming 2x payout for size
        //     }
             // Add logic for Violet (if it pays differently or is tied to specific digits)

        //     if ($isWinner) {
        //         // Update user balance and record transaction
        //         // Example: $bet->user->wallet->increment('balance', $payout);
        //         // Example: \App\Models\Transaction::create([... payout details ...]);
        //         // Example: $bet->update(['status' => 'won', 'payout' => $payout]);
        //         $this->info("Bet {$bet->id} won! Payout: {$payout}");
        //     } else {
        //         // Example: $bet->update(['status' => 'lost']);
        //         $this->info("Bet {$bet->id} lost.");
        //     }
        // }

        $this->info("Bet processing for period {$period} completed.");
    }
}
<?php

namespace App\Services; // Example service class

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GameApiService
{
    protected $pythonApiUrl;

    public function __construct()
    {
        // Get the Python API URL from your .env file
        $this->pythonApiUrl = env('PYTHON_API_URL', 'http://127.0.0.1:8000');
    }

    /**
     * Call the Python API to trigger a game spin and get the result.
     * @param array $history Optional historical data to send to the AI
     * @return array|null The game result (digit, color) or null on failure
     */
    public function spinGame(array $history = []): ?array
    {
        try {
            $response = Http::timeout(10)->post("{$this->pythonApiUrl}/spin", [
                'history' => $history, // Send history if your Python API expects it
            ]);

            // Check if the request was successful (status code 2xx)
            if ($response->successful()) {
                return $response->json(); // Return the JSON response as an array
            } else {
                // Log the error if the request failed
                Log::error("Python API Spin failed: Status {$response->status()}", ['body' => $response->body()]);
                return null;
            }
        } catch (\Exception $e) {
            // Log any exceptions (e.g., connection errors)
            Log::error("Python API Spin Exception: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Call the Python API to get an AI prediction.
     * @param array $history Historical data to send to the AI for prediction
     * @return array|null The AI suggestion or null on failure
     */
    public function getAiSuggestion(array $history): ?array
    {
         try {
            // Assuming the /predict endpoint expects a POST with history
            $response = Http::timeout(10)->post("{$this->pythonApiUrl}/predict", [
                'history' => $history,
            ]);

            if ($response->successful()) {
                return $response->json();
            } else {
                 Log::error("Python API Predict failed: Status {$response->status()}", ['body' => $response->body()]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error("Python API Predict Exception: {$e->getMessage()}");
            return null;
        }
    }

     /**
     * Call the Python API to get the current balance (if your Python API tracks it, though Laravel should manage this)
     * @return array|null The balance or null on failure
     */
    public function getBalance(): ?array
    {
         try {
            $response = Http::timeout(10)->get("{$this->pythonApiUrl}/balance");

            if ($response->successful()) {
                return $response->json();
            } else {
                 Log::error("Python API Balance failed: Status {$response->status()}", ['body' => $response->body()]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error("Python API Balance Exception: {$e->getMessage()}");
            return null;
        }
    }

    // Add other methods for interacting with your Python API as needed
}

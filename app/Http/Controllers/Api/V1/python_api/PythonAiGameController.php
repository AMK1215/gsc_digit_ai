<?php

namespace App\Http\Controllers\Api\V1\python_api;

use App\Http\Controllers\Controller;
use App\Services\GameApiService; // Import the service
use Illuminate\Http\Request;

class PythonAiGameController extends Controller
{
    protected $gameApiService;

    public function __construct(GameApiService $gameApiService)
    {
        $this->gameApiService = $gameApiService;
    }

    // Example endpoint to get AI suggestion (called by React)
    public function getAiSuggestion(Request $request)
    {
        // Fetch historical data from your database to send to Python
        $history = \App\Models\AiGameResult::latest()->limit(50)->pluck('winning_digit')->toArray(); // Example

        $suggestion = $this->gameApiService->getAiSuggestion($history);

        if ($suggestion) {
            return response()->json($suggestion);
        } else {
            return response()->json(['message' => 'Could not get AI suggestion.'], 500);
        }
    }

    // ... other game related methods (placing bets, etc.)
}
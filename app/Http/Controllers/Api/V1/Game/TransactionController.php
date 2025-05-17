<?php

namespace App\Http\Controllers\Api\V1\Game;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function createTransaction(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'MemberID' => 'required|numeric',
            'OperatorID' => 'required|numeric',
            'ProductID' => 'required|numeric',
            'ProviderID' => 'required|numeric',
            'CurrencyID' => 'required|numeric',
            'GameType' => 'required|numeric',
            'BetAmount' => 'required|numeric',
            'TransactionAmount' => 'required|numeric',
            // Add validation for other required fields based on your model
        ]);

        // Prepare the data payload
        $data = [
            'MemberID' => $validated['MemberID'],
            'OperatorID' => $validated['OperatorID'],
            'ProductID' => $validated['ProductID'],
            'ProviderID' => $validated['ProviderID'],
            'CurrencyID' => $validated['CurrencyID'],
            'GameType' => $validated['GameType'],
            'BetAmount' => $validated['BetAmount'],
            'TransactionAmount' => $validated['TransactionAmount'],
            // Include other fields based on the model shown in the image
            // Assuming GameID, GameRoundID, and other nullable fields are not required
        ];

        // Generate the signature if necessary
        $operatorCode = Config::get('game.api.operator_code');
        $requestTime = now()->format('YmdHis');
        $secretKey = Config::get('game.api.secret_key');
        $signature = md5($operatorCode.$requestTime.$secretKey);

        $data['Sign'] = $signature;
        $data['RequestTime'] = $requestTime;

        // API endpoint from the config
        $apiUrl = Config::get('game.api.url').'/Seamless/Transaction';

        try {
            // Send the POST request to the API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($apiUrl, $data);

            // Correct logging statements with associative array as context
            Log::info('Transaction request sent.', ['data' => $data]);
            Log::info('Transaction response received.', ['response' => $response->json(), 'status' => $response->status()]);

            if ($response->successful()) {
                // Return the successful response
                return $response->json();
            } else {
                // Handle errors from the API
                return response()->json(['error' => 'API request failed', 'details' => $response->body()], $response->status());
            }
        } catch (\Throwable $e) {
            // Correct logging for exceptions with associative array as context
            Log::error('An unexpected error occurred.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(), // Consider logging the stack trace only if necessary
            ]);

            return response()->json(['error' => 'An unexpected error occurred', 'exception' => $e->getMessage()], 500);
        }
    }

    public function getTransactionDetails($tranId)
    {
        $operatorId = 'delightMMK';

        $url = 'https://api.sm-sspi-prod.com/api/opgateway/v1/op/GetTransactionDetails';

        // Generate the RequestDateTime in UTC
        $requestDateTime = Carbon::now('UTC')->format('Y-m-d H:i:s');

        // Generate the signature using MD5 hashing
        $secretKey = '1OMJXOf88RHKpcuT';
        $functionName = 'GetTransactionDetails';
        $signatureString = $functionName.$requestDateTime.$operatorId.$secretKey;
        $signature = md5($signatureString);

        // Prepare request payload
        $payload = [
            'OperatorId' => $operatorId,
            'RequestDateTime' => $requestDateTime,
            'Signature' => $signature,
            'TranId' => $tranId,
        ];

        try {
            // Make the POST request to the API endpoint
            $response = Http::post($url, $payload);

            // Check if the response is successful
            if ($response->successful()) {
                return $response->json(); // Return the response data as JSON
            } else {
                Log::error('Failed to get transaction details', ['response' => $response->body()]);

                return response()->json(['error' => 'Failed to get transaction details'], 500);
            }
        } catch (\Exception $e) {
            Log::error('API request error', ['message' => $e->getMessage()]);

            return response()->json(['error' => 'API request error'], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api\V1\Slot;

use App\Enums\StatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\Slot\GetDaySummaryRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GetDaySummaryController extends Controller
{
    public function getDaySummary(GetDaySummaryRequest $request): JsonResponse
    {
        $transactionData = $request->getTransactionData();

        // Validate the signature
        $generatedSignature = $this->generateSignature($transactionData);
        Log::info('Generated Signature', ['GeneratedSignature' => $generatedSignature]);

        if ($generatedSignature !== $transactionData['Signature']) {
            Log::warning('Signature validation failed', [
                'transaction' => $transactionData,
                'generated_signature' => $generatedSignature,
            ]);

            return $this->buildErrorResponse(StatusCode::InvalidSignature);
        }

        // Prepare data for the API provider
        $payload = [
            'OperatorId' => $transactionData['OperatorId'],
            'RequestDateTime' => $transactionData['RequestDateTime'],
            'Signature' => $transactionData['Signature'],
            'Date' => $transactionData['Date'],
        ];

        // Post data to the provider's API and handle the response
        $providerApiUrl = 'https://api.sm-sspi-uat.com/api/opgateway/v1/op/GetDaySummary';
        //$response = Http::post($providerApiUrl, $payload);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($providerApiUrl, $payload);

        if ($response->successful()) {
            $providerData = $response->json();

            return $this->buildSuccessResponse($providerData['Trans']);
        }

        Log::error('Failed to retrieve data from provider', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return $this->buildErrorResponse(StatusCode::InternalServerError);
    }

    private function generateSignature(array $transactionData): string
    {
        $method = 'GetDaySummary';
        $operatorId = $transactionData['OperatorId'];
        $requestDateTime = $transactionData['RequestDateTime'];
        $secretKey = config('game.api.secret_key'); // Fetch secret key from config

        return md5($method.$requestDateTime.$operatorId.$secretKey);
    }

    private function buildSuccessResponse(array $data): JsonResponse
    {
        return response()->json([
            'Status' => StatusCode::OK->value,
            'Description' => 'Success',
            'ResponseDateTime' => now()->format('Y-m-d H:i:s'),
            'Trans' => $data,
        ]);
    }

    private function buildErrorResponse(StatusCode $statusCode): JsonResponse
    {
        return response()->json([
            'Status' => $statusCode->value,
            'Description' => $statusCode->name,
            'ResponseDateTime' => now()->format('Y-m-d H:i:s'),
        ]);
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

// class GetDaySummaryController extends Controller
// {
//     public function getDaySummary(GetDaySummaryRequest $request): JsonResponse
//     {
//         $transactionData = $request->getTransactionData();

//         // Validate the signature
//         $generatedSignature = $this->generateSignature($transactionData);
//         Log::info('Result Signature', ['GeneratedResultSignature' => $generatedSignature]);

//         if ($generatedSignature !== $transactionData['Signature']) {
//             Log::warning('Signature validation failed', [
//                 'transaction' => $transactionData,
//                 'generated_signature' => $generatedSignature,
//             ]);

//             return $this->buildErrorResponse(StatusCode::InvalidSignature);
//         }

//         // Prepare data for the API provider
//         $payload = [
//             'OperatorId' => $transactionData['OperatorId'],
//             'RequestDateTime' => $transactionData['RequestDateTime'],
//             'Signature' => $transactionData['Signature'],
//             'Date' => $transactionData['Date'],
//         ];

//         // Post data to the provider's API and handle the response
//         $providerApiUrl = config('game.api.url'); // Make sure to set this in config/services.php or .env
//         $response = Http::post($providerApiUrl, $payload);

//         if ($response->successful()) {
//             $providerData = $response->json();

//             return $this->buildSuccessResponse($providerData['Trans']);
//         }

//         Log::error('Failed to retrieve data from provider', [
//             'status' => $response->status(),
//             'body' => $response->body(),
//         ]);

//         return $this->buildErrorResponse(StatusCode::InternalServerError);
//     }

//     private function generateSignature(array $transactionData): string
//     {
//         $method = 'GetDaySummary';
//         $operatorId = $transactionData['OperatorId'];
//         $requestDateTime = $transactionData['RequestDateTime'];
//         $secretKey = config('game.api.secret_key'); // Fetch secret key from config

//         return md5($method . $requestDateTime . $operatorId . $secretKey);
//     }

//     private function buildSuccessResponse(array $data): JsonResponse
//     {
//         return response()->json([
//             'Status' => StatusCode::OK->value,
//             'Description' => 'Success',
//             'ResponseDateTime' => now()->format('Y-m-d H:i:s'),
//             'Trans' => $data,
//         ]);
//     }

//     private function buildErrorResponse(StatusCode $statusCode): JsonResponse
//     {
//         return response()->json([
//             'Status' => $statusCode->value,
//             'Description' => $statusCode->name,
//             'ResponseDateTime' => now()->format('Y-m-d H:i:s'),
//         ]);
//     }
// }

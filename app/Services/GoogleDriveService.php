<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    protected $client;

    protected $service;

    public function __construct()
    {
        $this->client = new Client;

        $this->client->setAccessToken($this->getAccessToken());

        $this->service = new Drive($this->client);
    }

    /**
     * Get the access token from the session or fetch a new one.
     */
    public function getAccessToken()
    {
        $token = session('google_access_token');

        if (! is_array($token) || ! isset($token['access_token']) || $this->client->isAccessTokenExpired()) {
            $token = $this->fetchNewAccessToken();
            session(['google_access_token' => $token]);
        }

        return $token;
    }

    /**
     * Fetch a new access token using the refresh token.
     */
    public function fetchNewAccessToken()
    {
        // Set the client credentials
        $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));

        $accessToken = $this->client->fetchAccessTokenWithRefreshToken(env('GOOGLE_REFRESH_TOKEN'));

        if (isset($accessToken['error'])) {
            throw new \Exception('Failed to fetch new access token: '.$accessToken['error_description']);
        }

        // Return the access token
        return $accessToken;
    }

    /**
     * Upload a file to Google Drive.
     */
    public function uploadToDrive(string $filePath, string $fileName)
    {
        try {
            $file = new DriveFile;
            $file->setName($fileName);
            $file->setMimeType('application/sql'); // Adjust MIME type as needed

            $fileData = file_get_contents($filePath);

            $uploadedFile = $this->service->files->create(
                $file,
                [
                    'data' => $fileData,
                    'mimeType' => 'application/sql',
                    'uploadType' => 'multipart',
                ]
            );

            return $uploadedFile->id;
        } catch (\Exception $e) {
            Log::error('Error uploading file to Google Drive: '.$e->getMessage());

            return;
        }
    }
}

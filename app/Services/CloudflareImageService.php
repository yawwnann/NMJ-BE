<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CloudflareImageService
{
    private string $accountId;
    private string $apiToken;
    private string $baseUrl;

    public function __construct()
    {
        $this->accountId = config('services.cloudflare.account_id');
        $this->apiToken = config('services.cloudflare.api_token');
        $this->baseUrl = "https://api.cloudflare.com/client/v4/accounts/{$this->accountId}/images/v1";
    }

    /**
     * Upload image to Cloudflare Images
     */
    public function uploadImage(UploadedFile $file, array $options = []): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiToken}",
            ])->attach(
                    'file',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )->post($this->baseUrl, $options);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Image uploaded successfully to Cloudflare', [
                    'image_id' => $data['result']['id'],
                    'filename' => $file->getClientOriginalName()
                ]);

                return [
                    'id' => $data['result']['id'],
                    'url' => $data['result']['variants'][0] ?? null,
                    'filename' => $data['result']['filename'],
                    'uploaded' => $data['result']['uploaded'],
                    'requireSignedURLs' => $data['result']['requireSignedURLs'] ?? false,
                    'variants' => $data['result']['variants'] ?? []
                ];
            }

            Log::error('Failed to upload image to Cloudflare', [
                'response' => $response->json(),
                'status' => $response->status()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exception while uploading image to Cloudflare', [
                'message' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);
            return null;
        }
    }

    /**
     * Delete image from Cloudflare Images
     */
    public function deleteImage(string $imageId): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiToken}",
            ])->delete("{$this->baseUrl}/{$imageId}");

            if ($response->successful()) {
                Log::info('Image deleted successfully from Cloudflare', [
                    'image_id' => $imageId
                ]);
                return true;
            }

            Log::error('Failed to delete image from Cloudflare', [
                'image_id' => $imageId,
                'response' => $response->json(),
                'status' => $response->status()
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Exception while deleting image from Cloudflare', [
                'message' => $e->getMessage(),
                'image_id' => $imageId
            ]);
            return false;
        }
    }

    /**
     * Get image details from Cloudflare Images
     */
    public function getImage(string $imageId): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiToken}",
            ])->get("{$this->baseUrl}/{$imageId}");

            if ($response->successful()) {
                return $response->json()['result'];
            }

            Log::error('Failed to get image from Cloudflare', [
                'image_id' => $imageId,
                'response' => $response->json(),
                'status' => $response->status()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exception while getting image from Cloudflare', [
                'message' => $e->getMessage(),
                'image_id' => $imageId
            ]);
            return null;
        }
    }

    /**
     * List images from Cloudflare Images
     */
    public function listImages(int $perPage = 20, int $page = 1): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiToken}",
            ])->get($this->baseUrl, [
                        'per_page' => $perPage,
                        'page' => $page
                    ]);

            if ($response->successful()) {
                return $response->json()['result'];
            }

            Log::error('Failed to list images from Cloudflare', [
                'response' => $response->json(),
                'status' => $response->status()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exception while listing images from Cloudflare', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Update image metadata
     */
    public function updateImage(string $imageId, array $metadata = []): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiToken}",
                'Content-Type' => 'application/json',
            ])->patch("{$this->baseUrl}/{$imageId}", $metadata);

            if ($response->successful()) {
                Log::info('Image updated successfully in Cloudflare', [
                    'image_id' => $imageId,
                    'metadata' => $metadata
                ]);
                return true;
            }

            Log::error('Failed to update image in Cloudflare', [
                'image_id' => $imageId,
                'response' => $response->json(),
                'status' => $response->status()
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Exception while updating image in Cloudflare', [
                'message' => $e->getMessage(),
                'image_id' => $imageId
            ]);
            return false;
        }
    }

    /**
     * Generate signed URL for private images
     */
    public function generateSignedUrl(string $imageId, int $expiresIn = 3600): ?string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiToken}",
            ])->post("{$this->baseUrl}/{$imageId}/token", [
                        'expires_in' => $expiresIn
                    ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['result']['token'];
            }

            Log::error('Failed to generate signed URL from Cloudflare', [
                'image_id' => $imageId,
                'response' => $response->json(),
                'status' => $response->status()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exception while generating signed URL from Cloudflare', [
                'message' => $e->getMessage(),
                'image_id' => $imageId
            ]);
            return null;
        }
    }

    /**
     * Validate file before upload
     */
    public function validateFile(UploadedFile $file): bool
    {
        $allowedMimes = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml'
        ];

        $maxSize = 100 * 1024 * 1024; // 100MB

        return in_array($file->getMimeType(), $allowedMimes) &&
            $file->getSize() <= $maxSize;
    }
}
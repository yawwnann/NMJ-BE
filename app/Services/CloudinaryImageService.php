<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log;
class CloudinaryImageService
{
    /**
     * Upload image to Cloudinary
     */
    public function uploadImage(UploadedFile $file, array $options = []): ?array
    {
        try {
            $result = Cloudinary::upload($file->getRealPath(), $options);

            // Debug: log response dan tipe data
            Log::info('Cloudinary upload response', [
                'file' => $file->getClientOriginalName(),
                'result' => $result,
                'result_type' => is_object($result) ? get_class($result) : gettype($result)
            ]);

            if ($result && method_exists($result, 'getPublicId') && method_exists($result, 'getSecurePath')) {
                return [
                    'public_id' => $result->getPublicId(),
                    'url' => $result->getSecurePath(),
                    'format' => $result->getExtension(),
                ];
            } else {
                Log::error('Cloudinary upload failed or returned incomplete result', [
                    'file' => $file->getClientOriginalName(),
                    'result' => $result
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Exception while uploading image to Cloudinary', [
                'message' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);
            return null;
        }
    }

    /**
     * Delete image from Cloudinary
     */
    public function deleteImage(string $publicId): bool
    {
        try {
            $result = Cloudinary::destroy($publicId);
            return $result['result'] === 'ok';
        } catch (\Exception $e) {
            Log::error('Exception while deleting image from Cloudinary', [
                'message' => $e->getMessage(),
                'public_id' => $publicId
            ]);
            return false;
        }
    }
}
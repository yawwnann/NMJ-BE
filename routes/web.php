<?php

// routes/web.php

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File; // Tambahkan ini

Route::get('/test-cloudinary-upload', function () {
    // Pastikan konfigurasi Cloudinary sudah diinisialisasi di AppServiceProvider
    // Jika tidak, Anda bisa menginisialisasi di sini:
    // \Cloudinary\Configuration\Configuration::instance([
    //     'cloud' => [
    //         'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
    //         'api_key' => env('CLOUDINARY_API_KEY'),
    //         'api_secret' => env('CLOUDINARY_API_SECRET'),
    //     ],
    //     'url' => [
    //         'secure' => env('CLOUDINARY_SECURE', true),
    //     ],
    // ]);

    $testImagePath = public_path('test_image.png'); // Pastikan ada file ini di folder public Anda

    // Buat file dummy jika belum ada (opsional)
    if (!File::exists($testImagePath)) {
        // Ini contoh membuat file dummy
        $img = imagecreatetruecolor(100, 100);
        imagefill($img, 0, 0, imagecolorallocate($img, 255, 0, 0)); // Merah
        imagepng($img, $testImagePath);
        imagedestroy($img);
        Log::info('Created dummy test_image.png');
    }

    try {
        Log::info('Attempting direct Cloudinary upload from: ' . $testImagePath);
        $uploadedFile = Cloudinary::upload($testImagePath, [
            'folder' => 'company-profile-test',
            'public_id' => 'test_upload_' . uniqid(),
        ]);

        if ($uploadedFile && $uploadedFile->getSecurePath()) {
            Log::info('Direct Cloudinary upload SUCCESS! URL: ' . $uploadedFile->getSecurePath());
            return 'Upload to Cloudinary successful! URL: ' . $uploadedFile->getSecurePath() .
                '<br>Check your Cloudinary dashboard under "company-profile-test" folder.';
        } else {
            Log::error('Direct Cloudinary upload failed: returned null or no secure path.');
            return 'Upload to Cloudinary failed: Check Laravel logs for details.';
        }
    } catch (\Cloudinary\Api\Exception\ApiError $e) {
        Log::error('Direct Cloudinary API Error: ' . $e->getMessage());
        return 'Cloudinary API Error: ' . $e->getMessage() . ' Check Laravel logs.';
    } catch (\Exception $e) {
        Log::error('Direct Cloudinary General Error: ' . $e->getMessage());
        return 'General Upload Error: ' . $e->getMessage() . ' Check Laravel logs.';
    }
});
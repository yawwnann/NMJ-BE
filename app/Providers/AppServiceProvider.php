<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Cloudinary\Configuration\Configuration as CloudinaryConfiguration;
use Illuminate\Support\Facades\Log; // Tambahkan ini

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Logging untuk memeriksa nilai dari .env
        Log::info('Checking Cloudinary ENV values:');
        Log::info('CLOUD_NAME: ' . env('CLOUDINARY_CLOUD_NAME'));
        Log::info('API_KEY: ' . env('CLOUDINARY_API_KEY'));
        Log::info('API_SECRET: ' . env('CLOUDINARY_API_SECRET') ? 'SET' : 'NOT SET'); // Jangan log secret-nya langsung
        Log::info('SECURE: ' . env('CLOUDINARY_SECURE', true));

        CloudinaryConfiguration::instance([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => [
                'secure' => env('CLOUDINARY_SECURE', true),
            ],
        ]);
    }
}
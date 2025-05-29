<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Cloudinary\Configuration\Configuration as CloudinaryConfiguration;
use Cloudinary\Configuration\ConfigUtils; // <-- PENTING: Tambahkan ini
use Illuminate\Support\Facades\Log; // <-- PENTING: Tambahkan ini

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // --- Logging nilai ENV Cloudinary (untuk diagnosis awal) ---
        // Anda bisa mengkomentari/menghapus bagian ini setelah terverifikasi
        Log::info('Checking Cloudinary ENV values from AppServiceProvider:');
        Log::info('CLOUD_NAME: ' . env('CLOUDINARY_CLOUD_NAME'));
        Log::info('API_KEY: ' . env('CLOUDINARY_API_KEY'));
        Log::info('API_SECRET: ' . (env('CLOUDINARY_API_SECRET') ? 'SET' : 'NOT SET')); // Hindari logging secret
        Log::info('SECURE: ' . env('CLOUDINARY_SECURE', true));
        // --- Akhir logging ENV values ---

        // Konfigurasi Cloudinary SDK
        CloudinaryConfiguration::instance([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => [
                'secure' => env('CLOUDINARY_SECURE', true),
            ],
            // --- AKTIFKAN DEBUGGING DI SINI ---
            'api' => [
                'debug' => true, // Ini akan mengaktifkan cURL verbose output ke log
            ]
        ]);

        // Opsional: Mengarahkan log debug Cloudinary ke log Laravel.
        // Jika tidak diset, output debug cURL mungkin masuk ke error_log PHP default.
        // Aktifkan ini jika Anda ingin melihat log debug Cloudinary di storage/logs/laravel.log
        // Pastikan level log di config/logging.php cukup rendah (misal: 'debug')
        /*
        ConfigUtils::setLogger(function ($message) {
            Log::debug('[Cloudinary SDK Debug]: ' . $message);
        });
        */
    }
}
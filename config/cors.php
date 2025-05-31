<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'], // Terapkan CORS ke semua rute API

    'allowed_methods' => ['*'], // Izinkan semua metode HTTP (GET, POST, PUT, DELETE, dll.)

    'allowed_origins' => ['*'], // PENTING: Untuk DEVELOPMENT ONLY
    // Untuk PRODUKSI, ganti dengan domain spesifik frontend Anda, contoh:
    // 'allowed_origins' => ['https://yourfrontend.com', 'http://localhost:3000'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // Izinkan semua header

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false, // Set 'true' jika frontend Anda mengirim cookie (misal: dengan Laravel Sanctum SPA)

];
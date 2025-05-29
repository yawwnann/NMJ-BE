<?php

require __DIR__ . '/vendor/autoload.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

// Ganti dengan kredensial Anda
$cloudName = 'dm3icigfr'; // Dari .env CLOUDINARY_CLOUD_NAME
$apiKey = '932445379271325'; // Dari .env CLOUDINARY_API_KEY
$apiSecret = '3lHG7ZkrWwm_gpH1USLtkIVBLMk'; // Dari .env CLOUDINARY_API_SECRET
$secure = true; // Dari .env CLOUDINARY_SECURE

Configuration::instance([
    'cloud' => [
        'cloud_name' => $cloudName,
        'api_key' => $apiKey,
        'api_secret' => $apiSecret
    ],
    'url' => [
        'secure' => $secure
    ]
]);

$imagePath = __DIR__ . '/public/test_image.png'; // Pastikan test_image.png ada di public folder

if (!file_exists($imagePath)) {
    echo "Error: test_image.png not found at $imagePath\n";
    exit();
}

echo "Attempting to upload $imagePath to Cloudinary...\n";

try {
    $uploadApi = new UploadApi();
    $result = $uploadApi->upload($imagePath, [
        'folder' => 'test-direct-php',
        'public_id' => 'direct_test_' . uniqid()
    ]);

    if ($result && isset($result['secure_url'])) {
        echo "Upload successful! URL: " . $result['secure_url'] . "\n";
        echo "Public ID: " . $result['public_id'] . "\n";
    } else {
        echo "Upload failed. Result: " . print_r($result, true) . "\n";
    }
} catch (\Cloudinary\Api\Exception\ApiError $e) {
    echo "Cloudinary API Error: " . $e->getMessage() . "\n";
    // Optionally, print the whole exception object for debugging
    // echo "Exception details: " . print_r($e, true) . "\n";
} catch (\Exception $e) {
    echo "General Error: " . $e->getMessage() . "\n";
}

?>
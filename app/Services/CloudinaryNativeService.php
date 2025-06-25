<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;

class CloudinaryNativeService
{
    protected $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => 'dbcqki5id',
                'api_key' => '441729569425797',
                'api_secret' => 't2xm-qRNjFjo4S92jViNYNi0ZVw',
            ],
        ]);
    }

    public function uploadImage(UploadedFile $file)
    {
        $result = $this->cloudinary->uploadApi()->upload($file->getRealPath());
        return [
            'public_id' => $result['public_id'] ?? null,
            'url' => $result['secure_url'] ?? null,
            'format' => $result['format'] ?? null,
        ];
    }
}
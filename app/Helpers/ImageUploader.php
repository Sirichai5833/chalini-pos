<?php

namespace App\Helpers;

use Cloudinary\Api\Upload\UploadApi as UploadUploadApi;
use Cloudinary\UploadApi;
use Illuminate\Support\Facades\Log;

class ImageUploader
{
    public static function upload($file, $folder = 'uploads')
    {
        if (!$file || !method_exists($file, 'getRealPath')) {
            return null;
        }

        try {
            $uploader = new UploadUploadApi();

            $result = $uploader->upload(
                $file->getRealPath(),
                ['folder' => $folder]
            );

            return $result['secure_url'] ?? null;

        } catch (\Throwable $e) {
            Log::error('Cloudinary upload error', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }
}

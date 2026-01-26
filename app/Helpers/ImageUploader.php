<?php

namespace App\Helpers;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ImageUploader
{
    public static function upload($file, $folder = 'uploads')
    {
        if (!$file || !method_exists($file, 'getRealPath')) {
            return null;
        }

        $result = Cloudinary::upload(
            $file->getRealPath(),
            [
                'folder' => $folder,
            ]
        );

        return $result['secure_url'] ?? null;
    }
}

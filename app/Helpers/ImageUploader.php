<?php


namespace App\Helpers;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ImageUploader
{
    public static function upload($file, $folder = 'uploads')
    {
        // กัน null
        if (!$file || !method_exists($file, 'getRealPath')) {
            return null;
        }

        $result = Cloudinary::upload(
            $file->getRealPath(),
            [
                'folder' => $folder,
                'upload_preset' => config('cloudinary.upload_preset'),
            ]
        );

        // ✅ Cloudinary คืนค่าเป็น array
        return $result['secure_url'] ?? null;
    }
}

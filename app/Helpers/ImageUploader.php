<?php

namespace App\Helpers;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ImageUploader
{
    public static function upload($file, $folder = 'uploads')
    {
        // 🔥 กัน null ตรงนี้ก่อนเลย
        if (!$file || !method_exists($file, 'getRealPath')) {
            return null;
        }

        $result = Cloudinary::upload(
            $file->getRealPath(),
            [
                'folder' => $folder
            ]
        );

        return $result->getSecurePath();
    }
}

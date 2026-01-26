<?php

namespace App\Helpers;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ImageUploader
{
    public static function upload($file, $folder = 'uploads')
    {
        // อัปโหลดไป Cloudinary
        $result = Cloudinary::upload(
            $file->getRealPath(),
            [
                'folder' => $folder
            ]
        );

        // ส่งกลับเป็น URL
        return $result->getSecurePath();
    }
}

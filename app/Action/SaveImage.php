<?php

namespace App\Action;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Drivers\Gd\Encoders\JpegEncoder;
use Intervention\Image\Drivers\Gd\Encoders\PngEncoder;
use Intervention\Image\Drivers\Gd\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class SaveImage
{
    public static function execute($id, $name, $binary, $extension)
    {
        $manager = new ImageManager(new Driver());
        $img = $manager->read($binary);

        if ($img->width() > 1024) {
            $img->scale(width: 1024);
        }

        $encoder = match ($extension) {
            'jpg', 'jpeg' => new JpegEncoder(quality: 75),
            'png' => new PngEncoder(),
            'webp' => new WebpEncoder(quality: 75),
            default => new JpegEncoder(quality: 75),
        };

        $encoded = $img->encode($encoder);

        $finalExtension = match ($extension) {
            'jpg', 'jpeg', 'png', 'webp' => $extension,
            default => 'jpg'
        };

        $filename = "questions/images/{$id}/{$name}.{$finalExtension}";

        Storage::disk('public')->put($filename, (string) $encoded);

        return $filename;
    }
}

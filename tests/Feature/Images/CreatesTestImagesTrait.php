<?php

namespace Antriver\LaravelSiteUtilsTests\Feature\Images;

use Illuminate\Support\Facades\Storage;

trait CreatesTestImagesTrait
{
    /**
     * Points the temp disk at a fresh directory and fakes the cloud disk, which ImageFileRepository needs.
     * Returns the name of the faked cloud disk.
     */
    private function setUpImageStorage(): string
    {
        $tempRoot = sys_get_temp_dir().'/lsu-images-'.uniqid();
        mkdir($tempRoot);
        config(
            [
                'filesystems.disks.temp' => ['driver' => 'local', 'root' => $tempRoot],
                'app.jpegtran_bin' => '',
            ]
        );

        $cloudDisk = Storage::getDefaultCloudDriver();
        Storage::fake($cloudDisk);

        return $cloudDisk;
    }

    /**
     * Writes a PNG of the given size to a temp file and returns its path.
     */
    private function createPng(int $width, int $height): string
    {
        $path = tempnam(sys_get_temp_dir(), 'lsu').'.png';
        $image = imagecreatetruecolor($width, $height);
        imagefill($image, 0, 0, imagecolorallocate($image, 200, 50, 50));
        imagepng($image, $path);

        return $path;
    }
}

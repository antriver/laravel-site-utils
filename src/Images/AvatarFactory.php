<?php

namespace Antriver\LaravelSiteScaffolding\Images;

use Antriver\LaravelSiteScaffolding\Models\Image;
use Antriver\LaravelSiteScaffolding\Models\User;
use Antriver\LaravelSiteScaffolding\Repositories\ImageRepository;

class AvatarFactory
{
    /**
     * @var ImageRepository
     */
    private $imageRepository;

    /**
     * @var ImageFileRepository
     */
    private $imageFileRepository;

    public function __construct(
        ImageRepository $imageRepository,
        ImageFileRepository $imageFileRepository
    ) {
        $this->imageRepository = $imageRepository;
        $this->imageFileRepository = $imageFileRepository;
    }

    public function generateUrl($hash = null, $size = 100)
    {
        if (!$hash) {
            $hash = md5(uniqid());
        }

        return 'https://api.adorable.io/avatars/'.$size.'/'.$hash;
    }

    public function generateImage(User $user, string $directory): Image
    {
        $url = $this->generateUrl();

        $imageOnDisk = $this->imageFileRepository->makeImageFromUrl($url);
        $image = $this->imageFileRepository->persist($imageOnDisk, $directory);

        $image->userId = $user->id;
        $this->imageRepository->persist($image);

        return $image;
    }
}

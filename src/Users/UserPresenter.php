<?php

namespace Antriver\LaravelSiteUtils\Users;

use Antriver\LaravelModelPresenters\ModelPresenterInterface;
use Antriver\LaravelModelPresenters\PresentArrayTrait;
use Antriver\LaravelSiteUtils\Images\ImageSize;
use Antriver\LaravelSiteUtils\Models\Base\AbstractModel;
use Antriver\LaravelSiteUtils\Repositories\ImageRepository;
use Illuminate\Database\Eloquent\Model;

class UserPresenter implements ModelPresenterInterface, UserPresenterInterface
{
    use PresentArrayTrait;

    /**
     * @var ImageRepository
     */
    private $imageRepository;

    public function __construct(
        ImageRepository $imageRepository
    ) {
        $this->imageRepository = $imageRepository;
    }

    /**
     * @param Model|User $user
     *
     * @return mixed
     */
    public function present(Model $user, bool $withLargeUrl = true): array
    {
        $array = $user->toArray();

        if ($user->avatarImageId && $avatar = $this->imageRepository->find($user->avatarImageId)) {
            $array['avatarUrl'] = image_thumb($avatar->getUrl(), ...ImageSize::AVATAR_SM);
            if ($withLargeUrl) {
                $array['avatarUrlLarge'] = image_thumb($avatar->getUrl(), ...ImageSize::AVATAR_LG);
            }
        }

        $array['url'] = $user->getUrl();

        return $array;
    }

    public function presentUser(UserInterface $user): array
    {
        /** @var UserInterface|AbstractModel $user */
        return $this->present($user);
    }
}

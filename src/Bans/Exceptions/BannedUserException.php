<?php

namespace Antriver\LaravelSiteScaffolding\Bans\Exceptions;

use Amirite\Libraries\Enums\ContentHiddenReason;
use Amirite\ModelPresenters\BanPresenter;
use Amirite\ModelPresenters\UserPresenter;
use Amirite\Models\Ban;
use Amirite\Models\User;
use Antriver\LaravelSiteScaffolding\Exceptions\ForbiddenHttpException;

class BannedUserException extends ForbiddenHttpException
{
    private $ban;

    private $user;

    public function __construct(Ban $ban, User $user)
    {
        $this->ban = $ban;
        $this->user = $user;

        if (empty($this->hiddenReason)) {
            // Check for emptiness because subclass may have set this.
            $this->setHiddenReason(ContentHiddenReason::POSTER_BANNED);
        }

        $message = BanPresenter::getMessage($ban, $user, false);

        parent::__construct($message);
    }

    /**
     * @return Ban
     */
    public function getBan(): Ban
    {
        return $this->ban;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    public function getData(): array
    {
        $presentedBan = $this->ban ? app(BanPresenter::class)->present($this->ban) : null;
        return [
            'additionalHtml' => $presentedBan ? $presentedBan['reasonHtml'] : null,
            'ban' => $presentedBan,
            'user' => $this->user ? app(UserPresenter::class)->present($this->user) : null,
        ];
    }
}

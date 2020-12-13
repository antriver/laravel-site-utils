<?php

namespace Antriver\LaravelSiteUtils\Bans\Exceptions;

use Antriver\LaravelSiteUtils\Bans\Ban;
use Antriver\LaravelSiteUtils\Bans\BanPresenter;
use Antriver\LaravelSiteUtils\Exceptions\ForbiddenHttpException;
use Antriver\LaravelSiteUtils\Exceptions\Traits\HasUserTrait;
use Antriver\LaravelSiteUtils\Users\UserInterface;
use Antriver\LaravelSiteUtils\Users\UserPresenter;

class BannedUserException extends ForbiddenHttpException
{
    use HasUserTrait;

    private $ban;

    public function __construct(Ban $ban, UserInterface $user)
    {
        $this->ban = $ban;
        $this->setUser($user);

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

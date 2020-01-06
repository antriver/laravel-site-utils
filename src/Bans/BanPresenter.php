<?php

namespace Antriver\LaravelSiteScaffolding\Bans;

use Antriver\LaravelModelPresenters\ModelPresenterInterface;
use Antriver\LaravelModelPresenters\PresentArrayTrait;
use Antriver\LaravelSiteScaffolding\Date\DateFormat;
use Antriver\LaravelSiteScaffolding\Lang\TextPresenter;
use Antriver\LaravelSiteScaffolding\Users\UserInterface;
use Antriver\LaravelSiteScaffolding\Users\UserRepository;
use Illuminate\Database\Eloquent\Model;

class BanPresenter implements ModelPresenterInterface
{
    use PresentArrayTrait;

    /**
     * @var TextPresenter
     */
    private $textPresenter;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        TextPresenter $textPresenter,
        UserRepository $userRepository
    ) {
        $this->textPresenter = $textPresenter;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Model|Ban $ban
     *
     * @return array|null
     */
    public function present(Model $ban): ?array
    {
        $array = $ban->toArray();

        $user = $ban->userId ? $this->userRepository->find($ban->userId) : null;
        $array['message'] = self::getMessage($ban, $user, false);
        $array['reasonHtml'] = $this->textPresenter->format($ban->reason, true, true);

        return $array;
    }

    public static function getMessage(Ban $ban, ?UserInterface $user, bool $withReason)
    {
        $who = null;
        if ($user) {
            $who = 'This user';
        } elseif ($ban->ip) {
            $who = 'IP '.$ban->ip;
        } elseif ($ban->userId) {
            $who = 'User ID '.$ban->userId;
        }

        $str = $who.' is '.self::getExpiresText($ban);

        if ($withReason && $ban->reason) {
            $str .= ' because: '.$ban->reason;
        } else {
            $str .= '.';
        }

        return $str;
    }

    public static function getExpiresText(Ban $ban)
    {
        if ($ban->expiresAt) {
            return 'suspended until '.$ban->expiresAt->format(DateFormat::DATE_TIME.' (e)');
        } else {
            return 'banned';
        }
    }
}

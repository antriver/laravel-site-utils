<?php

namespace Antriver\LaravelSiteUtils\EmailVerification\Events;

use Antriver\LaravelSiteUtils\Events\Base\AbstractEvent;
use Antriver\LaravelSiteUtils\Traits\HasUserTrait;
use Antriver\LaravelSiteUtils\Users\UserInterface;

class EmailBouncedEvent extends AbstractEvent
{
    use HasUserTrait;

    /**
     * @var string
     */
    public $email;

    public function __construct(UserInterface $user, string $email)
    {
        $this->setUser($user);
        $this->email = $email;
    }
}

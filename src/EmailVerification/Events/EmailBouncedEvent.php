<?php

namespace Antriver\LaravelSiteScaffolding\EmailVerification\Events;

use Antriver\LaravelSiteScaffolding\Events\Base\AbstractEvent;
use Antriver\LaravelSiteScaffolding\Traits\HasUserTrait;
use Antriver\LaravelSiteScaffolding\Users\UserInterface;

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

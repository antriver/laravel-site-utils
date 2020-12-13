<?php

namespace Antriver\LaravelSiteUtils\EmailVerification\Events;

use Antriver\LaravelSiteUtils\Events\AbstractEvent;
use Antriver\LaravelSiteUtils\Traits\HasUserTrait;
use Antriver\LaravelSiteUtils\Users\UserInterface;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class EmailVerifiedEvent extends AbstractEvent implements ShouldBroadcast
{
    use HasUserTrait;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string|null
     */
    public $oldEmail;

    public function __construct(UserInterface $user, string $email, string $oldEmail = null)
    {
        $this->setUser($user);
        $this->email = $email;
        $this->oldEmail = $oldEmail;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        // Broadcast on a public channel so users waiting to login can receive it.
        return [
            new Channel('email-verifications'),
            //new PrivateChannel('user.'.$this->user->id),
        ];
    }

    /**
     * @return array
     */
    public function broadcastWith()
    {
        // This is broadcast on a public channel so limit data exposed.
        return [
            'userId' => $this->user->id,
        ];
    }
}

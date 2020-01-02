<?php

namespace Antriver\LaravelSiteUtils\Mail\Base;

use Antriver\LaravelSimpleMessageTrait\SimpleMessageTrait;
use Antriver\LaravelSiteUtils\Models\User;
use Illuminate\Mail\Mailable;

abstract class ExtendedMailable extends Mailable
{
    use SimpleMessageTrait;

    /**
     * @var array
     */
    public $boxes = [];

    /**
     * @var User
     */
    public $recipient;

    public function box($content, $href = null, $title = null)
    {
        $this->boxes[] = [
            'content' => $content,
            'href' => $href,
            'title' => $title,
        ];

        return $this;
    }

    public function userBox(User $user, $title = null)
    {
        $this->boxes[] = [
            'preformattedContent' =>
                '<img src="'.$user->getAvatarUrl().'" style="width:60px; height:60px; border:0; border-radius:50%;" />
                <br/><strong>'.e($user->username).'</strong>',
            'href' => $user->getUrl(),
            'title' => $title,
            'style' => 'user-box',
        ];

        return $this;
    }

    /**
     * @return User
     */
    public function getRecipient(): User
    {
        return $this->recipient;
    }

    /**
     * IMPORTANT: This does not set the email address being sent to. It only sets the recipient property,
     * which is used in the template to display the user's name.
     *
     * @param User $recipient
     */
    public function setRecipient(User $recipient)
    {
        $this->recipient = $recipient;
    }

    /**
     * Build the view data for the message.
     *
     * @return array
     */
    public function buildViewData()
    {
        $data = parent::buildViewData();

        $data['recipient'] = $this->getRecipient();

        return $data;
    }
}

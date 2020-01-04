<?php

namespace Antriver\LaravelSiteScaffolding\Exceptions;

use Antriver\LaravelSiteScaffolding\Models\Ban\Ban;

class BannedUserException extends ForbiddenHttpException
{
    public function __construct(Ban $ban)
    {
        $message = 'This account is disabled';
        if ($ban->expiresAt) {
            $message .= ' until <strong>'.display_datetime($ban->expiresAt).'</strong>';
        }
        if ($ban->reason) {
            $message .= ' due to: <strong>'.$ban->reason.'</strong>';
        } else {
            $message .= '.';
        }

        parent::__construct($message);
    }
}

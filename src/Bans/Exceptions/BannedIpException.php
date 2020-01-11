<?php

namespace Antriver\LaravelSiteScaffolding\Bans;

use Antriver\LaravelSiteScaffolding\Exceptions\ForbiddenHttpException;

class BannedIpException extends ForbiddenHttpException
{
    public function __construct(Ban $ban)
    {
        $message = 'Your access to the site has been blocked';
        if ($ban->expiresAt) {
            $message .= ' until '.$ban->expiresAt;
        }
        if ($ban->reason) {
            $message .= ' because: '.$ban->reason;
        } else {
            $message .= '.';
        }

        $this->message = $message;

        parent::__construct($message);
    }
}

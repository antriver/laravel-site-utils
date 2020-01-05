<?php

namespace Antriver\LaravelSiteScaffolding\Http\Traits;

use Illuminate\Http\Request;

trait ValidatesCaptchaTrait
{
    protected function validateRequestCaptcha(Request $request)
    {
        $this->validate(
            $request,
            [
                'recaptcha' => 'required_without:solvemedia|recaptcha',
                'solvemedia' => 'required_without:recaptcha|solvemedia',
            ]
        );
    }
}

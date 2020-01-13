<?php

namespace Antriver\LaravelSiteScaffolding\Http\Traits;

use Illuminate\Http\Request;

trait ValidatesCaptchaTrait
{
    protected function validateRequestCaptcha(Request $request)
    {
        $rules = [];

        $hasRecaptcha = !empty(config('services.recaptcha'));
        $hasSolvemedia = !empty(config('services.solvemedia'));

        if ($hasRecaptcha) {
            $rules['recaptcha'] = [
                $hasSolvemedia ? 'required_without:solvemedia' : 'required',
                'recaptcha',
            ];
        }

        if ($hasSolvemedia) {
            $rules['solvemedia'] = [
                $hasRecaptcha ? 'required_without:recaptcha' : 'required',
                'solvemedia',
            ];
        }

        if (!empty($rules)) {
            $this->validate(
                $request,
                $rules
            );
        }
    }
}

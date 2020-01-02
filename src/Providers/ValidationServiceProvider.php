<?php

namespace Antriver\LaravelSiteUtils\Providers;

use Validator;

class ValidationServiceProvider extends \Illuminate\Validation\ValidationServiceProvider
{
    public function register()
    {
        parent::register();

        Validator::extend('email_valid', 'Antriver\LaravelSiteUtils\Http\Validators\EmailMXValidator@validate');
        Validator::extend('hex_color', 'Antriver\LaravelSiteUtils\Http\Validators\HexColorValidator@validate');
        Validator::extend('recaptcha', 'Antriver\LaravelSiteUtils\Http\Validators\RecaptchaValidator@validate');
        Validator::extend('user_image', 'Antriver\LaravelSiteUtils\Http\Validators\UserImageValidator@validate');
        Validator::extend('i_in', 'Antriver\LaravelSiteUtils\Http\Validators\InCaseInsensitiveValidator@validateIn');
        Validator::extend('i_not_in', 'Antriver\LaravelSiteUtils\Http\Validators\InCaseInsensitiveValidator@validateNotIn');
    }
}

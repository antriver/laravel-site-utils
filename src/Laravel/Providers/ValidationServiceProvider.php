<?php

namespace Antriver\SiteUtils\Laravel\Providers;

use Validator;

class ValidationServiceProvider extends \Illuminate\Validation\ValidationServiceProvider
{
    public function register()
    {
        parent::register();

        Validator::extend('email_valid', 'Antriver\SiteUtils\Http\Validators\EmailMXValidator@validate');
        Validator::extend('hex_color', 'Antriver\SiteUtils\Http\Validators\HexColorValidator@validate');
        Validator::extend('recaptcha', 'Antriver\SiteUtils\Http\Validators\RecaptchaValidator@validate');
        Validator::extend('user_image', 'Antriver\SiteUtils\Http\Validators\UserImageValidator@validate');
        Validator::extend('i_in', 'Antriver\SiteUtils\Http\Validators\InCaseInsensitiveValidator@validateIn');
        Validator::extend('i_not_in', 'Antriver\SiteUtils\Http\Validators\InCaseInsensitiveValidator@validateNotIn');
    }
}

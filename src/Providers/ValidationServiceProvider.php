<?php

namespace Antriver\LaravelSiteScaffolding\Providers;

use Validator;

class ValidationServiceProvider extends \Illuminate\Validation\ValidationServiceProvider
{
    public function register()
    {
        parent::register();

        Validator::extend('email_valid', 'Antriver\LaravelSiteScaffolding\Http\Validators\EmailMXValidator@validate');
        Validator::extend('hex_color', 'Antriver\LaravelSiteScaffolding\Http\Validators\HexColorValidator@validate');
        Validator::extend('recaptcha', 'Antriver\LaravelSiteScaffolding\Http\Validators\RecaptchaValidator@validate');
        Validator::extend('user_image', 'Antriver\LaravelSiteScaffolding\Http\Validators\UserImageValidator@validate');
        Validator::extend('i_in', 'Antriver\LaravelSiteScaffolding\Http\Validators\InCaseInsensitiveValidator@validateIn');
        Validator::extend('i_not_in', 'Antriver\LaravelSiteScaffolding\Http\Validators\InCaseInsensitiveValidator@validateNotIn');
    }
}

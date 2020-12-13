<?php

namespace Antriver\LaravelSiteUtils\Providers;

use Antriver\LaravelSiteUtils\Validation\Validators\EmailMXValidator;
use Antriver\LaravelSiteUtils\Validation\Validators\EmptyValidator;
use Antriver\LaravelSiteUtils\Validation\Validators\HexColorValidator;
use Antriver\LaravelSiteUtils\Validation\Validators\InCaseInsensitiveValidator;
use Antriver\LaravelSiteUtils\Validation\Validators\NotPresentValidator;
use Antriver\LaravelSiteUtils\Validation\Validators\RecaptchaValidator;
use Antriver\LaravelSiteUtils\Validation\Validators\UserImageValidator;
use Validator;

class ValidationServiceProvider extends \Illuminate\Validation\ValidationServiceProvider
{
    public function register()
    {
        parent::register();

        Validator::extend('email_valid', EmailMXValidator::class.'@validate');
        Validator::extend('hex_color', HexColorValidator::class.'@validate');
        Validator::extend('recaptcha', RecaptchaValidator::class.'@validate');
        Validator::extend('user_image', UserImageValidator::class.'@validate');
        Validator::extend('i_in', InCaseInsensitiveValidator::class.'@validateIn');
        Validator::extend('i_not_in', InCaseInsensitiveValidator::class.'@validateNotIn');

        /**
         * @see https://laravel.com/docs/5.8/validation#implicit-extensions
         * By default, when an attribute being validated is not present or contains an empty string, normal validation
         * rules, including custom extensions, are not run. For example, the unique rule will not be run against an empty string.
         *
         * For a rule to run even when an attribute is empty, the rule must imply that the attribute is required.
         * To create such an "implicit" extension, use the Validator::extendImplicit() method.
         */
        Validator::extendImplicit('empty', EmptyValidator::class);
        Validator::extendImplicit('not_present', NotPresentValidator::class);
    }
}

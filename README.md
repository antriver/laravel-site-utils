# Laravel Site Utils

A whole bunch of stuff to not have to duplicate common things in every app.
- User login
- Email verification
- Banning users
- Image handling

Laravel has built in methods for doing a lot of this but this does it nicer (read: my way).

Where concrete classes are provided for models, repositories and controllers, matching interfaces and traits are also provided so you can use those on your own concrete versions instead. Where possible type hints are interfaces.

If you take a look at `Providers\LaravelSiteUtilsServiceProvider` you will find an array containing a mapping of interfaces to concrete implementations. These will be bound in Laravel's DI container. If you want to use custom implementations then extend` LaravelSiteUtilsServiceProvider` and overrides the appropriate array values.

## Installation

This is in rapid development so is not tagged with any version. Add this to composer.json:

    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/antriver/laravel-site-utils.git"
        }
    ],

Then run:

    composer require antriver/laravel-site-utils dev-master


### Changing Configuration

Create a subclass of the provider so you can easily change settings. In your `app/providers` directory create `LaravelSiteUtilsServiceProvider.php` with the contents:

    <?php

    namespace App\Providers;

    class LaravelSiteUtilsServiceProvider extends \Antriver\LaravelSiteUtils\Providers\LaravelSiteUtilsServiceProvider
    {

    }

Then add your subclass to the providers array in `config/app.php`

    'providers' => [
        // ...
        App\Providers\LaravelSiteUtilsServiceProvider::class,
        // ...
    ],


And tell Laravel not to auto discover the existing provider. In composer.json:

    "extra": {
        "laravel": {
            "dont-discover": [
                "antriver/laravel-site-utils"
            ]
        }
    }

## Commands

### `site-utils:clean-default-files`
Remove some unused files in a default Laravel install.

### `site-utils:install`
Create some controllers inside your Laravel project, which extends the controllers from this package.

### `site-utils:publish-tests`
Create test classes for your new controllers created by the install command.

## Components

* Login - `Antriver\LaravelSiteUtils\Auth\Http\AuthController`

* Signup - `Antriver\LaravelSiteUtils\Auth\Http\SignupController`

* Forgot Password - `Antriver\LaravelSiteUtils\Auth\Http\ForgotController`

* Reset Password - `Antriver\LaravelSiteUtils\Auth\Http\ResetController`

* Email Verification - `Antriver\LaravelSiteUtils\Auth\Http\ResetController`


## TODO
* Resetting the password of a user who has not verified their email returns an UnverifiedUserException after trying to login so it looks like the password reset failed when it didn't.
* Command to delete expired password reset tokens.

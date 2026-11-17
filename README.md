# Laravel Site Utils

A bunch of stuff to not have to duplicate common things in every app.
- User login
- Email verification
- Banning users
- Image handling

Laravel has built in methods for some of this already but this does it "nicer" (read: the way I like).
This isn't designed to be used by anyone else, but it's here if you want it.

Where concrete classes are provided for models, repositories and controllers, matching interfaces and traits are also
provided, so you can use those on your own concrete versions instead. Where possible type hints are interfaces.

If you take a look at `Providers\LaravelSiteUtilsServiceProvider` you will find an array containing a mapping of
interfaces to concrete implementations. These will be bound in Laravel's DI container.
If you want to use custom implementations then extend` LaravelSiteUtilsServiceProvider` and overrides the
appropriate array values.

## Version support

The major version of this package matches the newest major version of Laravel it supports, so 13.x works with
Laravel 13. A major version may also support older Laravel versions while they still get security fixes.

| Package | Laravel    | PHP       | Status                                                      |
|---------|------------|-----------|-------------------------------------------------------------|
| 13.x    | 12.x, 13.x | 8.3 - 8.5 | Active                                                      |
| 11.x    | 11.x       | 8.2 - 8.4 | Unmaintained (Laravel 11 reached end of life in March 2026) |

There was no 12.x release of this package. Use 13.x with Laravel 12.

## Installation

The package is not on Packagist. Add the repository to composer.json:

    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/antriver/laravel-site-utils.git"
        }
    ],

Then run:

    composer require antriver/laravel-site-utils:^13.0


### Changing Configuration

Create a subclass of the provider so that you can easily change settings. In your `app/providers` directory
create `LaravelSiteUtilsServiceProvider.php` with the contents:

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

## Upgrading to 13.0

* Requires PHP 8.3 or newer and Laravel 12 or 13.
* `intervention/image` is now 4.x. `AbstractImageProcessor::process()`, `ImageOnDisk` and
  `ImageFileRepository::persistImage()` take `Intervention\Image\Interfaces\ImageInterface`, and `$image->getCore()` is
  now `$image->core()->native()`. Update any subclasses of `AbstractImageProcessor`.
* `ImageFileRepository::makeImageFromString()` only decodes binary, base64 and data URI strings. It no longer reads a
  file when given a path, so use `makeImageFromFile()` for that.
* `firebase/php-jwt` is now 7.x, which requires the signing key (`app.key`) to be at least 32 bytes.
* `google/recaptcha` is now 2.x on PHP 8.4 and newer (1.3 on PHP 8.3, since later 1.x releases need 8.4).
  `RecaptchaValidator` fails validation for non-string values instead of throwing.
* Laravel 13's new apps set `cache.serializable_classes` to `false`, so cached objects cannot be unserialized.
  `UserRepository`, `ImageRepository` and `RepositoryUserProvider` cache whole models, so if your app uses that
  setting, list the model classes you use (for example `Antriver\LaravelSiteUtils\Users\User` and
  `Antriver\LaravelSiteUtils\Images\Image`, or your own subclasses) in `cache.serializable_classes`.
  `BanRepository` caches plain arrays and works either way. Its cache keys changed, so bans cached by an older
  version are ignored and looked up again.
* Columns listed in a model's `$dates` property are cast to Carbon again and output in RFC 3339 format by
  `toArray()`. Laravel 10 had silently stopped doing this.
* Testing helpers: `ApiTestCaseTrait::createUser()` replaces the removed `factory(User::class)`,
  `TestCaseTrait::assertArraySubset()` replaces the assertion PHPUnit removed, and `SnsControllerTestTrait` skips the
  SNS signature check because AWS no longer serves the certificate its sample messages were signed with.

## Running the tests

The feature tests need a MySQL database. `scripts/test-db.sh` starts a `mysql:8.4` Docker container on port 33306
and runs the migrations (`scripts/migrate-test-db.sh`). The connection details match the `DB_*` values in
`phpunit.xml`, and can be overridden with environment variables.

    scripts/test-db.sh
    composer test

The image tests need the `imagick` PHP extension and are skipped without it.

GitHub Actions runs the suite on every supported combination: Laravel 12 and 13, each on PHP 8.3, 8.4 and 8.5.
It fails if any test is skipped, so the image tests always run there.

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
* Resetting the password of a user who has not verified their email returns an UnverifiedUserException after trying to
  login, so it looks like the password reset failed when it didn't.
* Command to delete expired password reset tokens.

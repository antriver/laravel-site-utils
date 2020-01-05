# Laravel Site Scaffolding

A whole bunch of stuff to not have to duplicate common things in every app.
- User login
- Email verification
- Banning users
- Image handling

Laravel has built in methods for doing a lot of this but this does it nicer (read: my way).

Where concrete classes are provided for models, repositories and controllers, matching interfaces and traits are also provided so you can use those on your own concrete versions instead. Where possible type hints are interfaces.

If you take a look at `Providers\LaravelSiteScaffoldingServiceProvider` you will find an array containing a mapping of interfaces to concrete implementations. These will be bound in Laravel's DI container. If you want to use custom implementations then extend` LaravelSiteScaffoldingServiceProvider` and overrides the appropriate array values.

## Installation

This is in rapid development so is not tagged with any version. Add this to `composer.json`:
```json
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/antriver/laravel-site-scaffolding.git"
        }
    ],
```

Then run:
```
composer require antriver/laravel-site-scaffolding dev-master
```

Create a subclass of the provider so you can easily change settings. Edit this file if you need to change the namespace.
```
cp vendor/antriver/laravel-site-scaffolding/templates/LaravelSiteScaffoldingServiceProvider.php app/Providers/LaravelSiteScaffoldingServiceProvider.php
```

Add your subclass to the providers array in `config/app.php`
```php
    'providers' => [
        // ...
        App\Providers\LaravelSiteScaffoldingServiceProvider::class,
        // ...
    ],
```

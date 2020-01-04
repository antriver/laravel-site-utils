# Laravel Site Scaffolding

A whole bunch of stuff to not have to duplicate common things in every app.
- User login
- Email verification
- Banning users
- Image handling

Laravel has built in methods for doing a lot of this but this does it nicer (read: my way).

Where concrete classes are provided for models, repositories and controllers, matching interfaces and traits are also provided so you can use those on your own concrete versions instead. Where possible type hints are interfaces.

If you take a look at `Providers\LaravelSiteUtilsServiceProvider` you can see it binds conrete implementations things in Laravel's DI container. If you want to use custom implementations then extend` LaravelSiteUtilsServiceProvider`, override the appropriate methods, and add your subclass in `config/app.php` instead.


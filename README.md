# Laravel Site Scaffolding

A whole bunch of stuff to not have to duplicate common things in every app.
- User login
- Email verification
- Banning users
- Image handling

Laravel has built in methods for doing a lot of this but this does it nicer (read: my way).

Where concrete classes are provided for models, repositories and controllers, matching interfaces and traits are also provided so you can use those on your own concrete versions instead. Where possible type hints are interfaces.

If you take a look at `Providers\LaravelSiteScaffoldingServiceProvider` you will find an array containing a mapping of interfaces to concrete implementations. These will be bound in Laravel's DI container. If you want to use custom implementations then extend` LaravelSiteScaffoldingServiceProvider` and overrides the appropriate array values.


<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application(
    realpath(__DIR__.'/../')
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->register(App\Providers\EventServiceProvider::class);
$app->register(App\Providers\CardServiceProvider::class);
$app->register(App\Providers\GameServiceProvider::class);
$app->register(App\Providers\HeroServiceProvider::class);
$app->register(App\Providers\TriggerQueueServiceProvider::class);
$app->register(App\Providers\SequenceServiceProvider::class);
$app->register(App\Providers\PhaseServiceProvider::class);
$app->register(App\Providers\TargetTypeProvider::class);
$app->register(App\Providers\FirebaseProvider::class);
//$app->register(App\Providers\HelperServiceProvider::class);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;

<?php namespace App\Providers;

use App\Game\Helpers\Random;
use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Random', function () {
            return new Random();
        });
    }
}
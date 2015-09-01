<?php namespace App\Providers;

use App\Models\TriggerQueue;
use Illuminate\Support\ServiceProvider;

class TriggerQueueServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('TriggerQueue', function () {
            return new TriggerQueue();
        });
    }
}
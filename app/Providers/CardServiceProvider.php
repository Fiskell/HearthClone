<?php

namespace App\Providers;

use App\Models\Card;
use Illuminate\Support\ServiceProvider;

class CardServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Card', function () {
            return new Card;
        });
    }
}
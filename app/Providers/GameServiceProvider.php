<?php

namespace App\Providers;

use App\Models\Card;
use App\Models\CardType;
use App\Models\Game;
use Illuminate\Support\ServiceProvider;

class GameServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Game', function () {
            return new Game($this->app['Player'], $this->app['Player']);
        });
    }
}
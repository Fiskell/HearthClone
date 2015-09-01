<?php

namespace App\Providers;

use App\Models\Board;
use App\Models\Deck;
use App\Models\Game;
use App\Models\TriggerTree;
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
        $this->app->singleton('Game', function () {
            return new Game($this->app['Player'], $this->app['Player']);
        });

        $this->app->bind('Deck', function($app, $params) {
            //TODO validate params
            return new Deck(array_get($params, 0), array_get($params, 1));
        });

    }
}
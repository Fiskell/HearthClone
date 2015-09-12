<?php

namespace App\Providers;

use App\Game\Deck;
use App\Game\Game;
use App\Game\Player;
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

        $this->app->bind('Player', function () {
            return new Player();
        });

    }
}
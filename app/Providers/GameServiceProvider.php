<?php

namespace App\Providers;

use App\Models\Board;
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
        $this->app->singleton('Game', function () {
            return new Game($this->app['Player'], $this->app['Player'], $this->app['Board']);
        });

        $this->app->singleton('Board', function () {
            return new Board();
        });
    }
}
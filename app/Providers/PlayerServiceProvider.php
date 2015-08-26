<?php

namespace App\Providers;

use App\Models\Card;
use App\Models\CardType;
use App\Models\Player;
use Illuminate\Support\ServiceProvider;

class PlayerServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Player', function () {
            return new Player();
        });
    }
}
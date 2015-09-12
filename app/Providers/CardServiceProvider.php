<?php

namespace App\Providers;

use App\Game\Cards\Card;
use App\Game\Cards\Minion;
use App\Game\Cards\Weapon;
use App\Game\CardSets\CardSets;
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
        $this->app->bind('Card', function ($app, $params) {
            return new Card(array_get($params, 0));
        });

        $this->app->bind('Minion', function ($app, $params) {
            return new Minion(array_get($params, 0));
        });

        $this->app->bind('Weapon', function ($app, $params) {
            return new Weapon(array_get($params, 0));
        });

        $this->app->singleton('CardSets', function () {
            return new CardSets();
        });
    }
}
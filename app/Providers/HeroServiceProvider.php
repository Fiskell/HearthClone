<?php

namespace App\Providers;

use App\Models\Heroes\Druid;
use App\Models\Heroes\Hunter;
use App\Models\Heroes\Mage;
use App\Models\Heroes\Paladin;
use App\Models\Heroes\Priest;
use App\Models\Heroes\Rogue;
use App\Models\Heroes\Shaman;
use App\Models\Heroes\Warlock;
use App\Models\Heroes\Warrior;
use Illuminate\Support\ServiceProvider;

class HeroServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Druid', function($app, $params) {
            return new Druid();
        });

        $this->app->bind('Hunter', function($app, $params) {
            return new Hunter();
        });

        $this->app->bind('Mage', function($app, $params) {
            return new Mage();
        });

        $this->app->bind('Paladin', function($app, $params) {
            return new Paladin();
        });

        $this->app->bind('Priest', function($app, $params) {
            return new Priest();
        });

        $this->app->bind('Rogue', function($app, $params) {
            return new Rogue();
        });

        $this->app->bind('Shaman', function($app, $params) {
            return new Shaman();
        });

        $this->app->bind('Warlock', function($app, $params) {
            return new Warlock();
        });

        $this->app->bind('Warrior', function($app, $params) {
            return new Warrior();
        });

    }
}
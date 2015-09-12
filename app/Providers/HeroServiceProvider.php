<?php namespace App\Providers;

use App\Game\Cards\Heroes\Hunter;
use App\Game\Cards\Heroes\Mage;
use App\Game\Cards\Heroes\Paladin;
use App\Game\Cards\Heroes\Priest;
use App\Game\Cards\Heroes\Rogue;
use App\Game\Cards\Heroes\Shaman;
use App\Game\Cards\Heroes\Warlock;
use App\Game\Cards\Heroes\Warrior;
use App\Game\Cards\Heroes\Druid;
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
            return new Druid(array_get($params, 0));
        });

        $this->app->bind('Hunter', function($app, $params) {
            return new Hunter(array_get($params, 0));
        });

        $this->app->bind('Mage', function($app, $params) {
            return new Mage(array_get($params, 0));
        });

        $this->app->bind('Paladin', function($app, $params) {
            return new Paladin(array_get($params, 0));
        });

        $this->app->bind('Priest', function($app, $params) {
            return new Priest(array_get($params, 0));
        });

        $this->app->bind('Rogue', function($app, $params) {
            return new Rogue(array_get($params, 0));
        });

        $this->app->bind('Shaman', function($app, $params) {
            return new Shaman(array_get($params, 0));
        });

        $this->app->bind('Warlock', function($app, $params) {
            return new Warlock(array_get($params, 0));
        });

        $this->app->bind('Warrior', function($app, $params) {
            return new Warrior(array_get($params, 0));
        });
    }
}
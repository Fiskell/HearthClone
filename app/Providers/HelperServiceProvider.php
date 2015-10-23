<?php namespace App\Providers;

use App\Game\Cards\Aura;
use App\Game\Cards\Card;
use App\Game\Cards\Minion;
use App\Game\Cards\Weapon;
use App\Game\CardSets\CardSets;
use App\Game\CardSets\CardSetTriggers;
use app\Game\Helpers\Random;
use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Random', function () {
            return new Random();
        });
    }
}
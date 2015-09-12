<?php namespace app\Providers;

use App\Game\Sequences\CardSequence;
use app\Game\Sequences\CombatSequence;
use app\Game\Sequences\HeroPowerSequence;
use App\Game\Sequences\PlayMinionSequence;
use app\Game\Sequences\PlaySpellSequence;
use app\Game\Sequences\PlayWeaponSequence;
use app\Game\Sequences\SummonMinionSequence;
use app\Game\Sequences\TurnSequence;
use Illuminate\Support\ServiceProvider;

class SequenceServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->app->bind('CardSequence', function() {
            return new CardSequence();
        });

        $this->app->bind('SummonMinionSequence', function() {
            return new SummonMinionSequence();
        });

        $this->app->bind('PlayMinionSequence', function() {
            return new PlayMinionSequence();
        });

        $this->app->bind('CombatSequence', function() {
            return new CombatSequence();
        });

        $this->app->bind('HeroPowerSequence', function() {
            return new HeroPowerSequence();
        });

        $this->app->bind('PlaySpellSequence', function() {
            return new PlaySpellSequence();
        });

        $this->app->bind('PlayWeaponSequence', function() {
            return new PlayWeaponSequence();
        });

        $this->app->bind('TurnSequence', function() {
            return new TurnSequence();
        });

    }
}
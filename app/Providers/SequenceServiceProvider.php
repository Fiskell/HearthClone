<?php namespace App\Providers;

use App\Game\Sequences\CardSequence;
use App\Game\Sequences\CombatSequence;
use App\Game\Sequences\HeroPowerSequence;
use App\Game\Sequences\PlayMinionSequence;
use App\Game\Sequences\PlaySpellSequence;
use App\Game\Sequences\PlayWeaponSequence;
use App\Game\Sequences\SummonMinionSequence;
use App\Game\Sequences\TurnSequence;
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
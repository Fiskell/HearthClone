<?php namespace App\Providers;

use App\Game\Sequences\Phases\AfterSummon;
use App\Game\Sequences\Phases\AuraHealth;
use App\Game\Sequences\Phases\AuraOther;
use App\Game\Sequences\Phases\Battlecry;
use App\Game\Sequences\Phases\DeathProcessing;
use App\Game\Sequences\Phases\SpellText;
use App\Game\Sequences\Phases\SubCardPhase;
use Illuminate\Support\ServiceProvider;

class PhaseServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->app->bind('AfterSummon', function() {
            return new AfterSummon();
        });

        $this->app->bind('Battlecry', function() {
            return new Battlecry();
        });

        $this->app->bind('SubCardPhase', function() {
            return new SubCardPhase();
        });

        $this->app->bind('SpellText', function() {
            return new SpellText();
        });

        $this->app->bind('DeathProcessing', function() {
            return new DeathProcessing();
        });

        $this->app->bind('AuraOther', function() {
            return new AuraOther();
        });

        $this->app->bind('AuraHealth', function() {
            return new AuraHealth();
        });
    }
}
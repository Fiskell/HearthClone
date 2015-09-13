<?php namespace App\Providers;

use App\Game\Sequences\Phases\AfterSummon;
use App\Game\Sequences\Phases\Battlecry;
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
    }
}
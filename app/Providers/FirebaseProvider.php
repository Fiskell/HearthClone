<?php namespace App\Providers;

use App\Game\Sequences\CardSequence;
use App\Game\Sequences\CombatSequence;
use App\Game\Sequences\HeroPowerSequence;
use App\Game\Sequences\PlayMinionSequence;
use App\Game\Sequences\PlaySpellSequence;
use App\Game\Sequences\PlayWeaponSequence;
use App\Game\Sequences\SummonMinionSequence;
use App\Game\Sequences\TurnSequence;
use App\Models\FirebaseModel;
use Illuminate\Support\ServiceProvider;

class FirebaseProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->app->singleton('FirebaseModel', function() {
            return new FirebaseModel();
        });
    }
}
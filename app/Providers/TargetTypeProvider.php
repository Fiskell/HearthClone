<?php namespace App\Providers;

use App\Game\Cards\TargetTypes\AdjacentMinions;
use App\Game\Cards\TargetTypes\AllCharacters;
use App\Game\Cards\TargetTypes\AllFriendlyCharacters;
use App\Game\Cards\TargetTypes\AllFriendlyMinions;
use App\Game\Cards\TargetTypes\AllMinions;
use App\Game\Cards\TargetTypes\AllOpponentCharacters;
use App\Game\Cards\TargetTypes\AllOpponentMinions;
use App\Game\Cards\TargetTypes\AllOtherCharacters;
use App\Game\Cards\TargetTypes\AllOtherMinionsWithRace;
use App\Game\Cards\TargetTypes\DamagedProvidedMinion;
use App\Game\Cards\TargetTypes\FriendlyHero;
use App\Game\Cards\TargetTypes\FriendlyPlayer;
use App\Game\Cards\TargetTypes\FriendlyWeapon;
use App\Game\Cards\TargetTypes\OpponentHero;
use App\Game\Cards\TargetTypes\OpponentWeapon;
use App\Game\Cards\TargetTypes\OtherFriendlyMinions;
use App\Game\Cards\TargetTypes\OtherFriendlyMinionsWithRace;
use App\Game\Cards\TargetTypes\ProvidedEnemyMinion;
use App\Game\Cards\TargetTypes\ProvidedMinion;
use App\Game\Cards\TargetTypes\RandomOpponentCharacter;
use App\Game\Cards\TargetTypes\TriggerCard;
use App\Game\Cards\TargetTypes\UndamagedProvidedMinion;
use App\Game\Cards\Triggers\TargetTypes;
use Illuminate\Support\ServiceProvider;

class TargetTypeProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TargetTypes::$ADJACENT_MINIONS, function () {
            return new AdjacentMinions();
        });

        $this->app->bind(TargetTypes::$ALL_CHARACTERS, function () {
            return new AllCharacters();
        });

        $this->app->bind(TargetTypes::$ALL_FRIENDLY_CHARACTERS, function () {
            return new AllFriendlyCharacters();
        });

        $this->app->bind(TargetTypes::$ALL_FRIENDLY_MINIONS, function () {
            return new AllFriendlyMinions();
        });

        $this->app->bind(TargetTypes::$ALL_MINIONS, function () {
            return new AllMinions();
        });

        $this->app->bind(TargetTypes::$ALL_OPPONENT_CHARACTERS, function () {
            return new AllOpponentCharacters();
        });

        $this->app->bind(TargetTypes::$ALL_OPPONENT_MINIONS, function () {
            return new AllOpponentMinions();
        });

        $this->app->bind(TargetTypes::$ALL_OTHER_CHARACTERS, function () {
            return new AllOtherCharacters();
        });

        $this->app->bind(TargetTypes::$All_OTHER_MINIONS_WITH_RACE, function () {
            return new AllOtherMinionsWithRace();
        });

        $this->app->bind(TargetTypes::$DAMAGED_PROVIDED_MINION, function () {
            return new DamagedProvidedMinion();
        });

        $this->app->bind(TargetTypes::$FRIENDLY_HERO, function () {
            return new FriendlyHero();
        });

        $this->app->bind(TargetTypes::$FRIENDLY_PLAYER, function () {
            return new FriendlyPlayer();
        });

        $this->app->bind(TargetTypes::$FRIENDLY_WEAPON, function () {
            return new FriendlyWeapon();
        });

        $this->app->bind(TargetTypes::$OPPONENT_HERO, function () {
            return new OpponentHero();
        });

        $this->app->bind(TargetTypes::$OPPONENT_WEAPON, function () {
            return new OpponentWeapon();
        });

        $this->app->bind(TargetTypes::$OTHER_FRIENDLY_MINIONS, function () {
            return new OtherFriendlyMinions();
        });

        $this->app->bind(TargetTypes::$OTHER_FRIENDLY_MINIONS_WITH_RACE, function () {
            return new OtherFriendlyMinionsWithRace();
        });

        $this->app->bind(TargetTypes::$PROVIDED_ENEMY_MINION, function () {
            return new ProvidedEnemyMinion();
        });

        $this->app->bind(TargetTypes::$PROVIDED_MINION, function () {
            return new ProvidedMinion();
        });

        $this->app->bind(TargetTypes::$RANDOM_OPPONENT_CHARACTER, function () {
            return new RandomOpponentCharacter();
        });

        $this->app->bind(TargetTypes::$SELF, function () {
            return new TriggerCard();
        });

        $this->app->bind(TargetTypes::$UNDAMAGED_PROVIDED_MINION, function () {
            return new UndamagedProvidedMinion();
        });
    }
}
<?php
use App\Models\Card;
use App\Models\CardType;
use App\Models\Game;
use App\Models\Mechanics;
use App\Models\Player;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/23/15
 * Time: 3:01 PM
 */
class CardTest extends TestCase
{

    /**
     * Creatures
     */
    public $argent_squire_handle      = 'Argent Squire';
    public $amani_berserker_handle    = 'Amani Berserker';
    public $bluegill_warrior_handle   = 'Bluegill Warrior';
    public $dread_corsair_handle      = 'Dread Corsair';
    public $knife_juggler_handle      = 'Knife Juggler';
    public $spellbreaker_handle       = 'Spellbreaker';
    public $wisp_handle               = 'Wisp';
    public $worgen_infiltrator_handle = 'Worgen Infiltrator';

    /**
     * Spells
     */
    public $consecrate_handle = 'Consecrate';

    /** @var  Card $card */
    public $card;

    /** @var  Game $game */
    public $game;

    public function setUp() {
        parent::setUp();
        $this->card = app('Card');
        $this->game = app('Game');
    }

    /**
     * @param $handle
     * @param $player_id
     * @param array $targets
     * @param bool|false $summoning_sickness
     * @return Card
     * @throws \App\Exceptions\MissingCardHandleException
     * @throws \App\Exceptions\UnknownCardHandleException
     */
    public function playCard($handle, $player_id, $targets = [], $summoning_sickness = false) {
        /** @var Card $card */
        $card = app('Card');
        $card->load($handle);

        /** @var Player $player */
        $player = $this->game->getPlayer1();
        if ($player_id == 2) {
            $player = $this->game->getPlayer2();
        }

        $player->play($card, $targets);

        if (!$summoning_sickness) {
            $active_player = $this->game->getActivePlayer();
            $active_player->passTurn();

            $active_player = $this->game->getActivePlayer();
            $active_player->passTurn();
        }

        // TODO summoning sickness
        return $card;
    }

    /** @expectedException \App\Exceptions\MissingCardHandleException */
    public function test_card_load_throws_when_no_card_name_specified() {
        $this->card->load();
    }

    /** @expectedException \App\Exceptions\UnknownCardHandleException */
    public function test_card_load_throws_when_unknown_handle_is_given() {
        $this->card->load('NOT_A_REAL_CARD_HANDLE');
    }

    public function test_card_handle_is_set_when_handle_passed_into_load() {
        $this->card->load($this->wisp_handle);
        $this->assertTrue($this->card->getHandle() == $this->wisp_handle);
    }

    public function test_card_attack_is_set_on_load_wisp() {
        $this->card->load($this->wisp_handle);
        $this->assertTrue($this->card->getAttack() == 1);
    }

    public function test_card_defense_is_set_on_load_wisp() {
        $this->card->load($this->wisp_handle);
        $this->assertTrue($this->card->getDefense() == 1);
    }

    public function test_card_attack_is_set_on_load_knife_juggler() {
        $this->card->load($this->knife_juggler_handle);
        $this->assertTrue($this->card->getAttack() == 3);
    }

    public function test_card_defense_is_set_on_load_knife_juggler() {
        $this->card->load($this->knife_juggler_handle);
        $this->assertTrue($this->card->getDefense() == 2);
    }

    public function test_wisp_is_a_creature() {
        $this->card->load($this->wisp_handle);
        $this->assertTrue($this->card->getType() == CardType::$CREATURE);
    }

    public function test_consecrate_is_a_spell() {
        $this->card->load($this->consecrate_handle);
        $this->assertTrue($this->card->getType() == CardType::$SPELL);
    }

    public function test_knife_juggler_attack_kills_wisp_without_divine_shield() {
        $wisp          = $this->playCard($this->wisp_handle, 1);
        $knife_juggler = $this->playCard($this->knife_juggler_handle, 2);

        $knife_juggler->attack($wisp);
        $this->assertTrue($wisp->getDefense() == 0);
        $this->assertTrue($wisp->isAlive() == false);
    }

    public function test_knife_juggler_defense_is_1_after_attacking_wisp() {
        $wisp          = $this->playCard($this->wisp_handle, 1);
        $knife_juggler = $this->playCard($this->knife_juggler_handle, 2);

        $knife_juggler->attack($wisp);
        $this->assertTrue($knife_juggler->getDefense() == 1);
    }

    /** @expectedException \App\Exceptions\InvalidTargetException */
    public function test_knife_juggler_cannot_attack_wisp_when_dread_corsair_is_on_the_field() {
        $wisp          = $this->playCard($this->wisp_handle, 1);
        $dread_corsair = $this->playCard($this->dread_corsair_handle, 1);
        $knife_juggler = $this->playCard($this->knife_juggler_handle, 2);

        $knife_juggler->attack($wisp);
    }

    public function test_knife_juggler_can_attack_wisp_after_dread_corsair_is_killed() {
        $wisp           = $this->playCard($this->wisp_handle, 1);
        $dread_corsair  = $this->playCard($this->dread_corsair_handle, 1);
        $knife_juggler  = $this->playCard($this->knife_juggler_handle, 2);
        $knife_juggler2 = $this->playCard($this->knife_juggler_handle, 2);


        $knife_juggler->attack($dread_corsair);
        $knife_juggler2->attack($wisp);
    }

    public function test_wisp_is_added_to_player_1_graveyard() {
        $wisp          = $this->playCard($this->wisp_handle, 1);
        $knife_juggler = $this->playCard($this->knife_juggler_handle, 2);

        $knife_juggler->attack($wisp);
        $player1_graveyard = $this->game->getPlayer1()->getGraveyard();

        /** @var Card $first_dead_card */
        $first_dead_card = array_get($player1_graveyard, '0');

        $this->assertTrue($first_dead_card->getHandle() === $this->wisp_handle);
    }

    public function test_argent_squire_does_not_die_from_attack_if_divine_shield_active() {
        $argent_squire = $this->playCard($this->argent_squire_handle, 1);
        $knife_juggler = $this->playCard($this->knife_juggler_handle, 2);

        $knife_juggler->attack($argent_squire);

        $this->assertTrue($argent_squire->isAlive());
        $this->assertTrue($knife_juggler->getDefense() == 1);
    }

    public function test_argent_squire_dies_from_attack_after_being_silenced() {
        $argent_squire = $this->playCard($this->argent_squire_handle, 1);
        $spellbreaker  = $this->playCard($this->spellbreaker_handle, 2, [$argent_squire]);
        $knife_juggler = $this->playCard($this->knife_juggler_handle, 2);

        $knife_juggler->attack($argent_squire);

        $this->assertTrue(!$argent_squire->isAlive());
    }

    /** @expectedException \App\Exceptions\InvalidTargetException */
    public function test_attacking_stealth_worgen_infiltrator_throws() {
        $worgen_infiltrator = $this->playCard($this->worgen_infiltrator_handle, 1);
        $knife_juggler      = $this->playCard($this->knife_juggler_handle, 2);

        $knife_juggler->attack($worgen_infiltrator);
    }

    public function test_card_played_this_turn_is_sleeping() {
        $wisp = $this->playCard($this->wisp_handle, 1, [], true);
        $this->assertTrue($wisp->isSleeping());
    }

    public function test_creature_wakes_up_after_passing_turn() {
        $active_player = $this->game->getActivePlayer();
        $wisp          = $this->playCard($this->wisp_handle, $active_player->getPlayerId(), [], true);

        $active_player->passTurn();
        $this->assertTrue(!$wisp->isSleeping());
    }

    /** @expectedException \App\Exceptions\InvalidTargetException */
    public function test_sleeping_creatures_attacking_throws() {
        $wisp  = $this->playCard($this->wisp_handle, 1, [], true);
        $wisp2 = $this->playCard($this->wisp_handle, 2, [], true);
        $wisp->attack($wisp2);
    }

    public function test_creature_with_charge_does_not_fall_asleep() {
        $bluegill_warrior = $this->playCard($this->bluegill_warrior_handle, 1, [], true);
        $wisp2            = $this->playCard($this->wisp_handle, 2, [], true);
        $bluegill_warrior->attack($wisp2);
    }

    public function test_amani_berserker_has_five_attack_after_being_attacked() {
        $amani_beserker = $this->playCard($this->amani_berserker_handle, 1);
        $wisp           = $this->playCard($this->wisp_handle, 2);

        $wisp->attack($amani_beserker);

        $this->assertTrue($amani_beserker->getAttack() == 5);
    }

    public function test_amani_berserker_has_five_attack_after_attacking_and_taking_damage() {
        $amani_beserker = $this->playCard($this->amani_berserker_handle, 1);
        $wisp           = $this->playCard($this->wisp_handle, 2);

        $amani_beserker->attack($wisp);

        $this->assertTrue($amani_beserker->getAttack() == 5);
    }

    public function test_worgen_infiltrator_loses_stealth_after_attacking() {
        $worgen_infiltrator = $this->playCard($this->worgen_infiltrator_handle, 1);
        $wisp               = $this->playCard($this->wisp_handle, 1);
        $worgen_infiltrator->attack($wisp);

        $has_stealth = $worgen_infiltrator->hasMechanic(Mechanics::$STEALTH);
        $this->assertTrue(!$has_stealth);
    }

    public function test_argent_squire_loses_divine_shield_after_attacking() {
        $argent_squire = $this->playCard($this->argent_squire_handle, 1);
        $knife_juggler = $this->playCard($this->knife_juggler_handle, 2);
        $argent_squire->attack($knife_juggler);

        $has_divine_shield = $argent_squire->hasMechanic(Mechanics::$DIVINE_SHIELD);
        $this->assertTrue(!$has_divine_shield);
    }

    /** @expectedException \App\Exceptions\InvalidTargetException */
    public function test_spellbreaker_silencing_worgen_infiltrator_throws() {
        $worgen_infiltrator = $this->playCard($this->worgen_infiltrator_handle, 1);
        $spellbreaker  = $this->playCard($this->spellbreaker_handle, 2, [$worgen_infiltrator]);
    }

}
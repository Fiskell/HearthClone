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
     * Minions
     */
    public $argent_squire_name      = 'Argent Squire';
    public $amani_berserker_name    = 'Amani Berserker';
    public $bluegill_warrior_name   = 'Bluegill Warrior';
    public $chillwind_yeti_name     = 'Chillwind Yeti';
    public $dread_corsair_name      = 'Dread Corsair';
    public $knife_juggler_name      = 'Knife Juggler';
    public $loot_hoarder_name       = 'Loot Hoarder';
    public $ogre_magi_name          = 'Ogre Magi';
    public $si7_agent               = "SI:7 Agent";
    public $spellbreaker_name       = 'Spellbreaker';
    public $thrallmar_farseer_name  = 'Thrallmar Farseer';
    public $water_elemental_name    = 'Water Elemental';
    public $wisp_name               = 'Wisp';
    public $worgen_infiltrator_name = 'Worgen Infiltrator';

    /**
     * Spells
     */
    public $consecrate_name = 'Consecration';

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
     * @param $name
     * @param $player_id
     * @param array $targets
     * @param bool|false $summoning_sickness
     * @return Card
     * @throws \App\Exceptions\MissingCardNameException
     * @throws \App\Exceptions\UnknownCardNameException
     */
    public function playCard($name, $player_id, $targets = [], $summoning_sickness = false) {
        /** @var Card $card */
        $card = app('Card');
        $card->load($name);

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

        return $card;
    }

    public function getActivePlayerId() {
        return $this->game->getActivePlayer()->getPlayerId();
    }

    public function getDefendingPlayerId() {
        return ($this->getActivePlayerId() % 2) + 1;
    }

    /** @expectedException \App\Exceptions\MissingCardNameException */
    public function test_card_load_throws_when_no_card_name_specified() {
        $this->card->load();
    }

    /** @expectedException \App\Exceptions\UnknownCardNameException */
    public function test_card_load_throws_when_unknown_name_is_given() {
        $this->card->load('NOT_A_REAL_CARD_HANDLE');
    }

    public function test_card_name_is_set_when_name_passed_into_load() {
        $this->card->load($this->wisp_name);
        $this->assertTrue($this->card->getName() == $this->wisp_name);
    }

    public function test_card_attack_is_set_on_load_wisp() {
        $this->card->load($this->wisp_name);
        $this->assertTrue($this->card->getAttack() == 1);
    }

    public function test_card_health_is_set_on_load_wisp() {
        $this->card->load($this->wisp_name);
        $this->assertTrue($this->card->getHealth() == 1);
    }

    public function test_card_attack_is_set_on_load_knife_juggler() {
        $this->card->load($this->knife_juggler_name);
        $this->assertTrue($this->card->getAttack() == 3);
    }

    public function test_card_health_is_set_on_load_knife_juggler() {
        $this->card->load($this->knife_juggler_name);
        $this->assertTrue($this->card->getHealth() == 2);
    }

    public function test_wisp_is_a_minion() {
        $this->card->load($this->wisp_name);
        $this->assertTrue($this->card->getType() == CardType::$MINION);
    }

    public function test_consecrate_is_a_spell() {
        $this->card->load($this->consecrate_name);
        $this->assertTrue($this->card->getType() == CardType::$SPELL);
    }

    public function test_knife_juggler_attack_kills_wisp_without_divine_shield() {
        $wisp          = $this->playCard($this->wisp_name, 1);
        $knife_juggler = $this->playCard($this->knife_juggler_name, 2);

        $knife_juggler->attack($wisp);
        $this->assertTrue($wisp->getHealth() == 0);
        $this->assertTrue($wisp->isAlive() == false);
    }

    public function test_knife_juggler_health_is_1_after_attacking_wisp() {
        $wisp          = $this->playCard($this->wisp_name, 1);
        $knife_juggler = $this->playCard($this->knife_juggler_name, 2);

        $knife_juggler->attack($wisp);
        $this->assertTrue($knife_juggler->getHealth() == 1);
    }

    /** @expectedException \App\Exceptions\InvalidTargetException */
    public function test_knife_juggler_cannot_attack_wisp_when_dread_corsair_is_on_the_field() {
        $wisp = $this->playCard($this->wisp_name, 1);
        $this->playCard($this->dread_corsair_name, 1);
        $knife_juggler = $this->playCard($this->knife_juggler_name, 2);

        $knife_juggler->attack($wisp);
    }

    public function test_knife_juggler_can_attack_wisp_after_dread_corsair_is_killed() {
        $wisp          = $this->playCard($this->wisp_name, 1);
        $dread_corsair = $this->playCard($this->dread_corsair_name, 1);

        $knife_juggler  = $this->playCard($this->knife_juggler_name, 2);
        $knife_juggler2 = $this->playCard($this->knife_juggler_name, 2);


        $knife_juggler->attack($dread_corsair);
        $knife_juggler2->attack($wisp);
    }

    public function test_wisp_is_added_to_player_1_graveyard() {
        $wisp          = $this->playCard($this->wisp_name, 1);
        $knife_juggler = $this->playCard($this->knife_juggler_name, 2);

        $knife_juggler->attack($wisp);
        $player1_graveyard = $this->game->getPlayer1()->getGraveyard();

        /** @var Card $first_dead_card */
        $first_dead_card = array_get($player1_graveyard, '0');

        $this->assertTrue($first_dead_card->getName() === $this->wisp_name);
    }

    public function test_argent_squire_does_not_die_from_attack_if_divine_shield_active() {
        $argent_squire = $this->playCard($this->argent_squire_name, 1);
        $knife_juggler = $this->playCard($this->knife_juggler_name, 2);

        $knife_juggler->attack($argent_squire);

        $this->assertTrue($argent_squire->isAlive());
        $this->assertTrue($knife_juggler->getHealth() == 1);
    }

    public function test_argent_squire_dies_from_attack_after_being_silenced() {
        $argent_squire = $this->playCard($this->argent_squire_name, 1);

        $this->playCard($this->spellbreaker_name, 2, [$argent_squire]);
        $knife_juggler = $this->playCard($this->knife_juggler_name, 2);

        $knife_juggler->attack($argent_squire);

        $this->assertTrue(!$argent_squire->isAlive());
    }

    /** @expectedException \App\Exceptions\InvalidTargetException */
    public function test_attacking_stealth_worgen_infiltrator_throws() {
        $worgen_infiltrator = $this->playCard($this->worgen_infiltrator_name, 1);
        $knife_juggler      = $this->playCard($this->knife_juggler_name, 2);

        $knife_juggler->attack($worgen_infiltrator);
    }

    public function test_card_played_this_turn_is_sleeping() {
        $wisp = $this->playCard($this->wisp_name, 1, [], true);
        $this->assertTrue($wisp->isSleeping());
    }

    public function test_minion_wakes_up_after_passing_turn() {
        $active_player = $this->game->getActivePlayer();
        $wisp          = $this->playCard($this->wisp_name, $active_player->getPlayerId(), [], true);

        $active_player->passTurn();
        $this->assertTrue(!$wisp->isSleeping());
    }

    /** @expectedException \App\Exceptions\InvalidTargetException */
    public function test_sleeping_minions_attacking_throws() {
        $wisp  = $this->playCard($this->wisp_name, 1, [], true);
        $wisp2 = $this->playCard($this->wisp_name, 2, [], true);
        $wisp->attack($wisp2);
    }

    public function test_minion_with_charge_does_not_fall_asleep() {
        $bluegill_warrior = $this->playCard($this->bluegill_warrior_name, 1, [], true);
        $wisp2            = $this->playCard($this->wisp_name, 2, [], true);
        $bluegill_warrior->attack($wisp2);
    }

    public function test_amani_berserker_has_five_attack_after_being_attacked() {
        $amani_beserker = $this->playCard($this->amani_berserker_name, 1);
        $wisp           = $this->playCard($this->wisp_name, 2);

        $wisp->attack($amani_beserker);

        $this->assertTrue($amani_beserker->getAttack() == 5);
    }

    public function test_amani_berserker_has_five_attack_after_attacking_and_taking_damage() {
        $amani_berserker = $this->playCard($this->amani_berserker_name, 1);
        $wisp            = $this->playCard($this->wisp_name, 2);

        $amani_berserker->attack($wisp);

        $this->assertTrue($amani_berserker->getAttack() == 5);
    }

    public function test_worgen_infiltrator_loses_stealth_after_attacking() {
        $worgen_infiltrator = $this->playCard($this->worgen_infiltrator_name, 1);
        $wisp               = $this->playCard($this->wisp_name, 1);
        $worgen_infiltrator->attack($wisp);

        $has_stealth = $worgen_infiltrator->hasMechanic(Mechanics::$STEALTH);
        $this->assertTrue(!$has_stealth);
    }

    public function test_argent_squire_loses_divine_shield_after_attacking() {
        $argent_squire = $this->playCard($this->argent_squire_name, 1);
        $knife_juggler = $this->playCard($this->knife_juggler_name, 2);
        $argent_squire->attack($knife_juggler);

        $has_divine_shield = $argent_squire->hasMechanic(Mechanics::$DIVINE_SHIELD);
        $this->assertTrue(!$has_divine_shield);
    }

    /** @expectedException \App\Exceptions\InvalidTargetException */
    public function test_spellbreaker_silencing_worgen_infiltrator_throws() {
        $worgen_infiltrator = $this->playCard($this->worgen_infiltrator_name, 1);
        $this->playCard($this->spellbreaker_name, 2, [$worgen_infiltrator]);
    }

    public function test_dread_corsair_loses_taunt_when_silenced() {
        $dread_corsair = $this->playCard($this->dread_corsair_name, 1);
        $this->playCard($this->spellbreaker_name, 2, [$dread_corsair]);

        $has_taunt = $dread_corsair->hasMechanic(Mechanics::$TAUNT);
        $this->assertTrue(!$has_taunt);
    }

    public function test_ogre_magi_increases_spell_power_by_one() {
        $this->playCard($this->ogre_magi_name, 1);
        $this->assertTrue($this->game->getPlayer1()->getSpellPowerModifier() == 1);
    }

    public function test_chillwind_yeti_is_frozen_when_attacked_by_water_elemental() {
        $water_elemental = $this->playCard($this->water_elemental_name, 1);
        $chillwind_yeti  = $this->playCard($this->chillwind_yeti_name, 2);

        $water_elemental->attack($chillwind_yeti);

        $is_frozen = $chillwind_yeti->isFrozen();
        $this->assertTrue($is_frozen);
    }

    public function test_chillwind_yeti_is_frozen_when_attacking_water_elemental() {
        $water_elemental = $this->playCard($this->water_elemental_name, 1);
        $chillwind_yeti  = $this->playCard($this->chillwind_yeti_name, 2);

        $chillwind_yeti->attack($water_elemental);

        $is_frozen = $chillwind_yeti->isFrozen();
        $this->assertTrue($is_frozen);
    }

    /** @expectedException \App\Exceptions\InvalidTargetException */
    public function test_chillwind_yeti_can_not_attack_when_frozen() {
        $water_elemental = $this->playCard($this->water_elemental_name, $this->getActivePlayerId());
        $chillwind_yeti  = $this->playCard($this->chillwind_yeti_name, $this->getDefendingPlayerId());

        $water_elemental->attack($chillwind_yeti);
        $this->game->getActivePlayer()->passTurn();
        $chillwind_yeti->attack($water_elemental);
    }

    public function test_chillwind_yeti_is_thawed_after_passing_turn() {
        $water_elemental = $this->playCard($this->water_elemental_name, $this->getActivePlayerId());
        $chillwind_yeti  = $this->playCard($this->chillwind_yeti_name, $this->getDefendingPlayerId());

        $water_elemental->attack($chillwind_yeti);
        $this->game->getActivePlayer()->passTurn();
        $this->assertTrue($chillwind_yeti->isFrozen());
        $this->game->getActivePlayer()->passTurn();
        $this->assertTrue(!$chillwind_yeti->isFrozen());
    }

    public function test_loot_hoarder_draws_card_when_killed() {
        $loot_hoarder = $this->playCard($this->loot_hoarder_name, 1);
        $wisp         = $this->playCard($this->wisp_name, 2);

        $hand_size = $this->game->getPlayer1()->getHandSize();

        $wisp->attack($loot_hoarder);

        $new_hand_size = $this->game->getPlayer1()->getHandSize();

        $this->assertTrue($new_hand_size == ($hand_size + 1));
    }

    public function test_both_players_loot_hoarder_will_draw_a_card_when_killed() {
        $loot_hoarder  = $this->playCard($this->loot_hoarder_name, 1);
        $loot_hoarder2 = $this->playCard($this->loot_hoarder_name, 2);

        $hand_size_player1 = $this->game->getPlayer1()->getHandSize();
        $hand_size_player2 = $this->game->getPlayer2()->getHandSize();

        $loot_hoarder2->attack($loot_hoarder);

        $new_hand_size_player1 = $this->game->getPlayer1()->getHandSize();
        $new_hand_size_player2 = $this->game->getPlayer2()->getHandSize();

        $this->assertTrue($new_hand_size_player1 == ($hand_size_player1 + 1));
        $this->assertTrue($new_hand_size_player2 == ($hand_size_player2 + 1));
    }

    /** @expectedException \App\Exceptions\MinionAlreadyAttackedException */
    public function test_chillwind_yeti_can_only_attack_once_per_turn() {
        $chillwind_yeti = $this->playCard($this->chillwind_yeti_name, 1);
        $wisp           = $this->playCard($this->wisp_name, 2);
        $wisp2          = $this->playCard($this->wisp_name, 2);

        $chillwind_yeti->attack($wisp);
        $chillwind_yeti->attack($wisp2);
    }

    public function test_chillwind_yeti_can_attack_twice_in_two_turns() {
        $chillwind_yeti = $this->playCard($this->chillwind_yeti_name, 1);
        $wisp           = $this->playCard($this->wisp_name, 2);
        $wisp2          = $this->playCard($this->wisp_name, 2);

        $chillwind_yeti->attack($wisp);
        $this->game->getActivePlayer()->passTurn();
        $this->assertTrue($chillwind_yeti->getTimesAttackedThisTurn() == 0);
        $chillwind_yeti->attack($wisp2);
        $this->assertTrue(!$wisp->isAlive());
        $this->assertTrue(!$wisp2->isAlive());
    }

    public function test_thrallmar_farseer_can_attack_twice_per_turn() {
        $thrallmar_farseer = $this->playCard($this->thrallmar_farseer_name, 1);
        $wisp              = $this->playCard($this->wisp_name, 2);
        $wisp2             = $this->playCard($this->wisp_name, 2);

        $thrallmar_farseer->attack($wisp);
        $thrallmar_farseer->attack($wisp2);

        $this->assertTrue(!$wisp->isAlive());
        $this->assertTrue(!$wisp2->isAlive());
    }

    public function test_thrallmar_farseer_can_still_attack_after_attacking_once() {
        $thrallmar_farseer = $this->playCard($this->thrallmar_farseer_name, 1);
        $wisp              = $this->playCard($this->wisp_name, 2);

        $thrallmar_farseer->attack($wisp);

        $this->assertTrue(!$wisp->isAlive());
        $this->assertFalse($thrallmar_farseer->alreadyAttacked());
    }

    /** @expectedException \App\Exceptions\MinionAlreadyAttackedException */
    public function test_thrallmar_farseer_can_not_attack_more_than_twice_per_turn() {
        $thrallmar_farseer = $this->playCard($this->thrallmar_farseer_name, 1);
        $wisp              = $this->playCard($this->wisp_name, 2);
        $wisp2             = $this->playCard($this->wisp_name, 2);
        $wisp3             = $this->playCard($this->wisp_name, 2);

        $thrallmar_farseer->attack($wisp);
        $thrallmar_farseer->attack($wisp2);
        $thrallmar_farseer->attack($wisp3);
    }

    public function test_si7_agent_does_not_combo_if_played_first() {
        $wisp      = $this->playCard($this->wisp_name, 2);
        $this->playCard($this->si7_agent, 1, [$wisp]);

        $this->assertTrue($wisp->isAlive());
    }

    public function test_si7_agent_combos_if_not_played_first() {
        $wisp = $this->playCard($this->wisp_name, 2);

        $this->playCard($this->wisp_name, 1);
        $this->playCard($this->si7_agent, 1, [$wisp]);

        $this->assertFalse($wisp->isAlive());
    }
}
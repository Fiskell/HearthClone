<?php

use App\Game\Cards\Card;
use App\Game\Cards\CardType;
use App\Game\Cards\Heroes\HeroClass;
use App\Game\Cards\Mechanics;
use App\Models\HearthCloneTest;

class CardTest extends HearthCloneTest
{
    /** @expectedException \App\Exceptions\MissingCardNameException */
    public function test_card_load_throws_when_no_card_name_specified() {
        app('Card', [$this->game->getPlayer1()]);
    }

    /** @expectedException \App\Exceptions\UnknownCardNameException */
    public function test_card_load_throws_when_unknown_name_is_given() {
        app('Card', [$this->game->getPlayer1(), 'NOT_A_REAL_CARD_HANDLE']);
    }

    /* Loading Properties */
    public function test_card_name_is_set_when_name_passed_into_load() {
        $wisp = app('Minion', [$this->game->getPlayer1(), 'Wisp']);
        $this->assertEquals('Wisp', $wisp->getName());
    }

    public function test_minion_attack_is_set_on_load() {
        $wisp = app('Minion', [$this->game->getPlayer1(), 'Wisp']);
        $this->assertEquals(1, $wisp->getAttack());
    }

    public function test_minion_health_is_set_on_load() {
        $wisp = app('Minion', [$this->game->getPlayer1(), 'Wisp']);
        $this->assertEquals(1, $wisp->getHealth());
    }

    public function test_card_play_order_id_is_set_when_card_is_played() {
        $current_card_counter = $this->game->getCardsPlayedThisGame();
        $wisp1                = $this->playCard('Wisp', 1);
        $wisp2                = $this->playCard('Wisp', 1);

        $wisp3 = $this->playCard('Wisp', 2);
        $wisp4 = $this->playCard('Wisp', 2);

        $this->assertEquals($current_card_counter + 1, $wisp1->getPlayOrderId());
        $this->assertEquals($current_card_counter + 2, $wisp2->getPlayOrderId());
        $this->assertEquals($current_card_counter + 3, $wisp3->getPlayOrderId());
        $this->assertEquals($current_card_counter + 4, $wisp4->getPlayOrderId());
    }

    /* Card Types */
    public function test_wisp_is_a_minion() {
        $wisp = app('Minion', [$this->game->getPlayer1(), 'Wisp']);
        $this->assertEquals(CardType::$MINION, $wisp->getType());
    }

    public function test_consecrate_is_a_spell() {
        $consecration = app('Minion', [$this->game->getPlayer1(), 'Consecration']);
        $this->assertEquals(CardType::$SPELL, $consecration->getType());
    }

    /* Cost */
    /** @expectedException \App\Exceptions\NotEnoughManaCrystalsException */
    public function test_two_cost_minion_cannot_be_played_on_first_turn() {
        $this->playCardStrict('Knife Juggler');
    }

    /* Attacking */
    public function test_stronger_minion_attack_kills_weaker_minion_without_divine_shield() {
        $wisp          = $this->playCard('Wisp', 1);
        $knife_juggler = $this->playCard('Knife Juggler', 2);

        $knife_juggler->attack($wisp);
        $this->assertTrue($wisp->getHealth() == 0);
        $this->assertTrue($wisp->isAlive() == false);
    }

    public function test_attacking_minion_takes_damage_from_defending_minion() {
        $wisp          = $this->playCard('Wisp', 1);
        $knife_juggler = $this->playCard('Knife Juggler', 2);

        $knife_juggler->attack($wisp);
        $this->assertTrue($knife_juggler->getHealth() == 1);
    }

    /** @expectedException \App\Exceptions\MinionAlreadyAttackedException */
    public function test_minion_can_only_attack_once_per_turn() {
        $chillwind_yeti = $this->playCard('Chillwind Yeti', 1);
        $wisp           = $this->playCard('Wisp', 2);
        $wisp2          = $this->playCard('Wisp', 2);

        $chillwind_yeti->attack($wisp);
        $chillwind_yeti->attack($wisp2);
    }

    public function test_exhaustion_reset_at_end_of_turn() {
        $chillwind_yeti = $this->playCard('Chillwind Yeti', 1);
        $wisp           = $this->playCard('Wisp', 2);
        $wisp2          = $this->playCard('Wisp', 2);

        $chillwind_yeti->attack($wisp);
        $this->game->getActivePlayer()->passTurn();
        $this->assertTrue($chillwind_yeti->getTimesAttackedThisTurn() == 0);
        $chillwind_yeti->attack($wisp2);
        $this->assertTrue(!$wisp->isAlive());
        $this->assertTrue(!$wisp2->isAlive());
    }

    public function test_minion_can_attack_hero() {
        $this->initPlayers();

        $this->assertEquals(30, $this->game->getPlayer1()->getHero()->getHealth());
        $knife_juggler = $this->playCard('Knife Juggler', 2);
        $knife_juggler->attack($this->game->getPlayer1()->getHero());
        $this->assertEquals(27, $this->game->getPlayer1()->getHero()->getHealth());
    }

    public function test_armor_is_used_before_life_when_damage_is_taken() {
        $this->initPlayers(HeroClass::$WARRIOR);

        $this->assertEquals(30, $this->game->getPlayer1()->getHero()->getHealth());
        $this->assertEquals(0, $this->game->getPlayer1()->getHero()->getArmor());

        $this->game->getPlayer1()->useAbility();
        $this->assertEquals(2, $this->game->getPlayer1()->getHero()->getArmor());

        $knife_juggler = $this->playCard('Knife Juggler', 2);
        $knife_juggler->attack($this->game->getPlayer1()->getHero());
        $this->assertEquals(29, $this->game->getPlayer1()->getHero()->getHealth());
        $this->assertEquals(0, $this->game->getPlayer1()->getHero()->getArmor());
    }

    /* Graveyard */
    public function test_killed_minion_is_added_to_graveyard() {
        $wisp          = $this->playCard('Wisp', 1);
        $knife_juggler = $this->playCard('Knife Juggler', 2);

        $knife_juggler->attack($wisp);
        $player1_graveyard = $this->game->getPlayer1()->getGraveyard();

        /** @var Card $first_dead_card */
        $first_dead_card = array_get($player1_graveyard, '0');

        $this->assertTrue($first_dead_card->getName() === 'Wisp');
    }

    /* Summoning Sickness */
    public function test_card_played_this_turn_is_sleeping() {
        $wisp = $this->playCard('Wisp', 1, [], true);
        $this->assertTrue($wisp->isSleeping());
    }

    public function test_minion_wakes_up_after_passing_turn() {
        $active_player = $this->game->getActivePlayer();
        $wisp          = $this->playCard('Wisp', $active_player->getPlayerId(), [], true);

        $active_player->passTurn();
        $this->assertTrue(!$wisp->isSleeping());
    }

    /** @expectedException \App\Exceptions\InvalidTargetException */
    public function test_sleeping_minions_attack_throws() {
        $wisp  = $this->playCard('Wisp', 1, [], true);
        $wisp2 = $this->playCard('Wisp', 2, [], true);
        $wisp->attack($wisp2);
    }

    /* Taunt */
    /** @expectedException \App\Exceptions\InvalidTargetException */
    public function test_minion_cannot_attak_opponent_minion_behind_taunt() {
        $wisp = $this->playCard('Wisp', 1);
        $this->playCard('Dread Corsair', 1);
        $knife_juggler = $this->playCard('Knife Juggler', 2);

        $knife_juggler->attack($wisp);
    }

    public function test_minion_can_attack_opponent_minion_once_taunt_minion_killed() {
        $wisp          = $this->playCard('Wisp', 1);
        $dread_corsair = $this->playCard('Dread Corsair', 1);

        $knife_juggler  = $this->playCard('Knife Juggler', 2);
        $knife_juggler2 = $this->playCard('Knife Juggler', 2);


        $knife_juggler->attack($dread_corsair);
        $knife_juggler2->attack($wisp);
    }

    /* Divine Shield */
    public function test_minion_does_not_die_from_attack_when_divine_shield_active() {
        $argent_squire = $this->playCard('Argent Squire', 1);
        $knife_juggler = $this->playCard('Knife Juggler', 2);

        $knife_juggler->attack($argent_squire);

        $this->assertTrue($argent_squire->isAlive());
        $this->assertTrue($knife_juggler->getHealth() == 1);
    }

    public function test_minion_loses_divine_shield_after_attacking_and_taking_damage() {
        $argent_squire = $this->playCard('Argent Squire', 1);
        $knife_juggler = $this->playCard('Knife Juggler', 2);
        $argent_squire->attack($knife_juggler);

        $has_divine_shield = $argent_squire->hasMechanic(Mechanics::$DIVINE_SHIELD);
        $this->assertTrue(!$has_divine_shield);
    }

    /* Silence */
    public function test_minion_with_divine_shield_dies_from_attack_when_silenced() {
        $argent_squire = $this->playCard('Argent Squire', 1);

        $this->playCard('Spellbreaker', 2, [$argent_squire]);
        $knife_juggler = $this->playCard('Knife Juggler', 2);

        $knife_juggler->attack($argent_squire);

        $this->assertTrue(!$argent_squire->isAlive());
    }

    /** @expectedException \App\Exceptions\InvalidTargetException */
    public function test_silencing_stealth_minion_throws() {
        $worgen_infiltrator = $this->playCard('Worgen Infiltrator', 1);
        $this->playCard('Spellbreaker', 2, [$worgen_infiltrator]);
    }

    public function test_minion_loses_taunt_when_silenced() {
        $dread_corsair = $this->playCard('Dread Corsair', 1);
        $this->playCard('Spellbreaker', 2, [$dread_corsair]);

        $has_taunt = $dread_corsair->hasMechanic(Mechanics::$TAUNT);
        $this->assertTrue(!$has_taunt);
    }

    /* Stealth */
    /** @expectedException \App\Exceptions\InvalidTargetException */
    public function test_attacking_stealth_minion_throws() {
        $worgen_infiltrator = $this->playCard('Worgen Infiltrator', 1);
        $knife_juggler      = $this->playCard('Knife Juggler', 2);

        $knife_juggler->attack($worgen_infiltrator);
    }

    public function test_stealth_minion_loses_stealth_after_attacking() {
        $worgen_infiltrator = $this->playCard('Worgen Infiltrator', 1);
        $wisp               = $this->playCard('Wisp', 1);
        $worgen_infiltrator->attack($wisp);

        $has_stealth = $worgen_infiltrator->hasMechanic(Mechanics::$STEALTH);
        $this->assertTrue(!$has_stealth);
    }

    /* Charge */
    public function test_minion_with_charge_does_not_fall_asleep() {
        $bluegill_warrior = $this->playCard('Bluegill Warrior', 1, [], true);
        $wisp2            = $this->playCard('Wisp', 2, [], true);
        $bluegill_warrior->attack($wisp2);
    }

    /* Enrage */
    public function test_minion_becomes_enraged_after_being_attacked_and_taking_damage() {
        $amani_berserker = $this->playCard('Amani Berserker', 1);
        $wisp           = $this->playCard('Wisp', 2);

        $wisp->attack($amani_berserker);

        $this->assertTrue($amani_berserker->getAttack() == 5);
    }

    public function test_minion_becomes_enraged_after_attacking_and_taking_damage() {
        $amani_berserker = $this->playCard('Amani Berserker', 1);
        $wisp            = $this->playCard('Wisp', 2);

        $amani_berserker->attack($wisp);

        $this->assertTrue($amani_berserker->getAttack() == 5);
    }

    /* Deathrattle */
    public function test_deathrattle_triggers_when_minion_dies() {
        $loot_hoarder = $this->playCard('Loot Hoarder', 1);
        $wisp         = $this->playCard('Wisp', 2);

        $hand_size = $this->game->getPlayer1()->getHandSize();

        $wisp->attack($loot_hoarder);

        $new_hand_size = $this->game->getPlayer1()->getHandSize();

        $this->assertTrue($new_hand_size == ($hand_size + 1));
    }

    public function test_both_players_deathrattle_trigger_when_killed() {
        $loot_hoarder  = $this->playCard('Loot Hoarder', 1);
        $loot_hoarder2 = $this->playCard('Loot Hoarder', 2);

        $hand_size_player1 = $this->game->getPlayer1()->getHandSize();
        $hand_size_player2 = $this->game->getPlayer2()->getHandSize();

        $loot_hoarder2->attack($loot_hoarder);

        $new_hand_size_player1 = $this->game->getPlayer1()->getHandSize();
        $new_hand_size_player2 = $this->game->getPlayer2()->getHandSize();

        $this->assertEquals($hand_size_player1 + 1, $new_hand_size_player1);
        $this->assertEquals($hand_size_player2 + 1, $new_hand_size_player2);
    }

    /* Windfury */
    public function test_minion_with_windfury_can_attack_twice_per_turn() {
        $thrallmar_farseer = $this->playCard('Thrallmar Farseer', 1);
        $wisp              = $this->playCard('Wisp', 2);
        $wisp2             = $this->playCard('Wisp', 2);

        $thrallmar_farseer->attack($wisp);
        $thrallmar_farseer->attack($wisp2);

        $this->assertTrue(!$wisp->isAlive());
        $this->assertTrue(!$wisp2->isAlive());
    }

    public function test_minion_with_windfury_can_still_attack_after_attacking_once() {
        $thrallmar_farseer = $this->playCard('Thrallmar Farseer', 1);
        $wisp              = $this->playCard('Wisp', 2);

        $thrallmar_farseer->attack($wisp);

        $this->assertTrue(!$wisp->isAlive());
        $this->assertFalse($thrallmar_farseer->alreadyAttacked());
    }

    /** @expectedException \App\Exceptions\MinionAlreadyAttackedException */
    public function test_minion_with_windfury_can_not_attack_more_than_twice_per_turn() {
        $thrallmar_farseer = $this->playCard('Thrallmar Farseer', 1);
        $wisp              = $this->playCard('Wisp', 2);
        $wisp2             = $this->playCard('Wisp', 2);
        $wisp3             = $this->playCard('Wisp', 2);

        $thrallmar_farseer->attack($wisp);
        $thrallmar_farseer->attack($wisp2);
        $thrallmar_farseer->attack($wisp3);
    }

    /* Combo */
    public function test_combo_does_not_trigger_when_played_first() {
        $wisp = $this->playCard('Wisp', 2);
        $this->playCard('SI:7 Agent', 1, [$wisp], true);

        $this->assertTrue($wisp->isAlive());
    }

    public function test_combo_triggers_when_not_played_first() {
        $wisp = $this->playCard('Wisp', 2);

        $this->playCard('Wisp', 1, [], true);
        $this->playCard('SI:7 Agent', 1, [$wisp], true);

        $this->assertFalse($wisp->isAlive());
    }

    /* Choose */
    public function test_choose_mechanic_option_1() {
        $wisp = $this->playCard('Wisp', 1);
        $this->playCard('Keeper of the Grove', 2, [$wisp], false, 1);

        $this->assertFalse($wisp->isAlive());
    }

    public function test_choose_mechanic_option_2() {
        $argent_squire = $this->playCard('Argent Squire', 1);
        $this->playCard('Keeper of the Grove', 2, [$argent_squire], false, 2);

        $this->assertFalse($argent_squire->hasMechanic(Mechanics::$DIVINE_SHIELD));
    }

    /* Overload */
    public function test_locked_mana_crystals_are_unlocked_at_beginning_of_next_turn() {
        $player_a = $this->game->getActivePlayer();
        $player_b = $this->game->getDefendingPlayer();
        $this->playCardStrict('Earth Elemental', $player_a->getPlayerId(), 5);

        $player_a->passTurn();
        $this->assertEquals(3, $player_a->getLockedManaCrystalCount());
        $player_b->passTurn();

        $this->assertEquals(0, $player_a->getLockedManaCrystalCount());
        $this->assertEquals(3, $player_a->getManaCrystalsUsed());
    }

    public function test_card_with_overload() {
        $active_player_id = $this->game->getActivePlayer()->getPlayerId();
        $this->playCardStrict('Earth Elemental', $active_player_id, 5);
        $this->assertEquals(3, $this->game->getActivePlayer()->getLockedManaCrystalCount());
    }


}
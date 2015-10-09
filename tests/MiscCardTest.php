<?php
use App\Game\Cards\Heroes\HeroClass;
use App\Game\Cards\Heroes\Shaman;
use App\Game\Cards\Card;
use App\Game\Cards\CardType;
use App\Game\Cards\Mechanics;
use App\Models\HearthCloneTest;


/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/23/15
 * Time: 3:01 PM
 */
class MiscCardTest extends HearthCloneTest
{
    /** @expectedException \App\Exceptions\MissingCardNameException */
    public function test_card_load_throws_when_no_card_name_specified() {
        app('Card', [$this->game->getPlayer1()]);
    }

    /** @expectedException \App\Exceptions\UnknownCardNameException */
    public function test_card_load_throws_when_unknown_name_is_given() {
        app('Card', [$this->game->getPlayer1(), 'NOT_A_REAL_CARD_HANDLE']);
    }

    public function test_card_name_is_set_when_name_passed_into_load() {
        $wisp = app('Minion', [$this->game->getPlayer1(), 'Wisp']);
        $this->assertEquals('Wisp', $wisp->getName());
    }

    public function test_card_attack_is_set_on_load_wisp() {
        $wisp = app('Minion', [$this->game->getPlayer1(), 'Wisp']);
        $this->assertEquals(1, $wisp->getAttack());
    }

    public function test_card_health_is_set_on_load_wisp() {
        $wisp = app('Minion', [$this->game->getPlayer1(), 'Wisp']);
        $this->assertEquals(1, $wisp->getHealth());
    }

    public function test_card_attack_is_set_on_load_knife_juggler() {
        $knife_juggler = app('Minion', [$this->game->getPlayer1(), 'Knife Juggler']);
        $this->assertEquals(3, $knife_juggler->getAttack());
    }

    public function test_card_health_is_set_on_load_knife_juggler() {
        $knife_juggler = app('Minion', [$this->game->getPlayer1(), 'Knife Juggler']);
        $this->assertEquals(2, $knife_juggler->getHealth());
    }

    public function test_wisp_is_a_minion() {
        $wisp = app('Minion', [$this->game->getPlayer1(), 'Wisp']);
        $this->assertEquals(CardType::$MINION, $wisp->getType());
    }

    public function test_consecrate_is_a_spell() {
        $consecration = app('Minion', [$this->game->getPlayer1(), 'Consecration']);
        $this->assertEquals(CardType::$SPELL, $consecration->getType());
    }

    public function test_knife_juggler_attack_kills_wisp_without_divine_shield() {
        $wisp          = $this->playCard('Wisp', 1);
        $knife_juggler = $this->playCard('Knife Juggler', 2);

        $knife_juggler->attack($wisp);
        $this->assertTrue($wisp->getHealth() == 0);
        $this->assertTrue($wisp->isAlive() == false);
    }

    public function test_knife_juggler_health_is_1_after_attacking_wisp() {
        $wisp          = $this->playCard('Wisp', 1);
        $knife_juggler = $this->playCard('Knife Juggler', 2);

        $knife_juggler->attack($wisp);
        $this->assertTrue($knife_juggler->getHealth() == 1);
    }

    /** @expectedException \App\Exceptions\InvalidTargetException */
    public function test_knife_juggler_cannot_attack_wisp_when_dread_corsair_is_on_the_field() {
        $wisp = $this->playCard('Wisp', 1);
        $this->playCard('Dread Corsair', 1);
        $knife_juggler = $this->playCard('Knife Juggler', 2);

        $knife_juggler->attack($wisp);
    }

    public function test_knife_juggler_can_attack_wisp_after_dread_corsair_is_killed() {
        $wisp          = $this->playCard('Wisp', 1);
        $dread_corsair = $this->playCard('Dread Corsair', 1);

        $knife_juggler  = $this->playCard('Knife Juggler', 2);
        $knife_juggler2 = $this->playCard('Knife Juggler', 2);


        $knife_juggler->attack($dread_corsair);
        $knife_juggler2->attack($wisp);
    }

    public function test_wisp_is_added_to_player_1_graveyard() {
        $wisp          = $this->playCard('Wisp', 1);
        $knife_juggler = $this->playCard('Knife Juggler', 2);

        $knife_juggler->attack($wisp);
        $player1_graveyard = $this->game->getPlayer1()->getGraveyard();

        /** @var Card $first_dead_card */
        $first_dead_card = array_get($player1_graveyard, '0');

        $this->assertTrue($first_dead_card->getName() === 'Wisp');
    }

    public function test_argent_squire_does_not_die_from_attack_if_divine_shield_active() {
        $argent_squire = $this->playCard('Argent Squire', 1);
        $knife_juggler = $this->playCard('Knife Juggler', 2);

        $knife_juggler->attack($argent_squire);

        $this->assertTrue($argent_squire->isAlive());
        $this->assertTrue($knife_juggler->getHealth() == 1);
    }

    public function test_argent_squire_dies_from_attack_after_being_silenced() {
        $argent_squire = $this->playCard('Argent Squire', 1);

        $this->playCard('Spellbreaker', 2, [$argent_squire]);
        $knife_juggler = $this->playCard('Knife Juggler', 2);

        $knife_juggler->attack($argent_squire);

        $this->assertTrue(!$argent_squire->isAlive());
    }

    /** @expectedException \App\Exceptions\InvalidTargetException */
    public function test_attacking_stealth_worgen_infiltrator_throws() {
        $worgen_infiltrator = $this->playCard('Worgen Infiltrator', 1);
        $knife_juggler      = $this->playCard('Knife Juggler', 2);

        $knife_juggler->attack($worgen_infiltrator);
    }

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
    public function test_sleeping_minions_attacking_throws() {
        $wisp  = $this->playCard('Wisp', 1, [], true);
        $wisp2 = $this->playCard('Wisp', 2, [], true);
        $wisp->attack($wisp2);
    }

    public function test_minion_with_charge_does_not_fall_asleep() {
        $bluegill_warrior = $this->playCard('Bluegill Warrior', 1, [], true);
        $wisp2            = $this->playCard('Wisp', 2, [], true);
        $bluegill_warrior->attack($wisp2);
    }

    public function test_amani_berserker_has_five_attack_after_being_attacked() {
        $amani_berserker = $this->playCard('Amani Berserker', 1);
        $wisp           = $this->playCard('Wisp', 2);

        $wisp->attack($amani_berserker);

        $this->assertTrue($amani_berserker->getAttack() == 5);
    }

    public function test_amani_berserker_has_five_attack_after_attacking_and_taking_damage() {
        $amani_berserker = $this->playCard('Amani Berserker', 1);
        $wisp            = $this->playCard('Wisp', 2);

        $amani_berserker->attack($wisp);

        $this->assertTrue($amani_berserker->getAttack() == 5);
    }

    public function test_worgen_infiltrator_loses_stealth_after_attacking() {
        $worgen_infiltrator = $this->playCard('Worgen Infiltrator', 1);
        $wisp               = $this->playCard('Wisp', 1);
        $worgen_infiltrator->attack($wisp);

        $has_stealth = $worgen_infiltrator->hasMechanic(Mechanics::$STEALTH);
        $this->assertTrue(!$has_stealth);
    }

    public function test_argent_squire_loses_divine_shield_after_attacking() {
        $argent_squire = $this->playCard('Argent Squire', 1);
        $knife_juggler = $this->playCard('Knife Juggler', 2);
        $argent_squire->attack($knife_juggler);

        $has_divine_shield = $argent_squire->hasMechanic(Mechanics::$DIVINE_SHIELD);
        $this->assertTrue(!$has_divine_shield);
    }

    /** @expectedException \App\Exceptions\InvalidTargetException */
    public function test_spellbreaker_silencing_worgen_infiltrator_throws() {
        $worgen_infiltrator = $this->playCard('Worgen Infiltrator', 1);
        $this->playCard('Spellbreaker', 2, [$worgen_infiltrator]);
    }

    public function test_dread_corsair_loses_taunt_when_silenced() {
        $dread_corsair = $this->playCard('Dread Corsair', 1);
        $this->playCard('Spellbreaker', 2, [$dread_corsair]);

        $has_taunt = $dread_corsair->hasMechanic(Mechanics::$TAUNT);
        $this->assertTrue(!$has_taunt);
    }

    public function test_loot_hoarder_draws_card_when_killed() {
        $loot_hoarder = $this->playCard('Loot Hoarder', 1);
        $wisp         = $this->playCard('Wisp', 2);

        $hand_size = $this->game->getPlayer1()->getHandSize();

        $wisp->attack($loot_hoarder);

        $new_hand_size = $this->game->getPlayer1()->getHandSize();

        $this->assertTrue($new_hand_size == ($hand_size + 1));
    }

    public function test_both_players_loot_hoarder_will_draw_a_card_when_killed() {
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

    /** @expectedException \App\Exceptions\MinionAlreadyAttackedException */
    public function test_chillwind_yeti_can_only_attack_once_per_turn() {
        $chillwind_yeti = $this->playCard('Chillwind Yeti', 1);
        $wisp           = $this->playCard('Wisp', 2);
        $wisp2          = $this->playCard('Wisp', 2);

        $chillwind_yeti->attack($wisp);
        $chillwind_yeti->attack($wisp2);
    }

    public function test_chillwind_yeti_can_attack_twice_in_two_turns() {
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

    public function test_thrallmar_farseer_can_attack_twice_per_turn() {
        $thrallmar_farseer = $this->playCard('Thrallmar Farseer', 1);
        $wisp              = $this->playCard('Wisp', 2);
        $wisp2             = $this->playCard('Wisp', 2);

        $thrallmar_farseer->attack($wisp);
        $thrallmar_farseer->attack($wisp2);

        $this->assertTrue(!$wisp->isAlive());
        $this->assertTrue(!$wisp2->isAlive());
    }

    public function test_thrallmar_farseer_can_still_attack_after_attacking_once() {
        $thrallmar_farseer = $this->playCard('Thrallmar Farseer', 1);
        $wisp              = $this->playCard('Wisp', 2);

        $thrallmar_farseer->attack($wisp);

        $this->assertTrue(!$wisp->isAlive());
        $this->assertFalse($thrallmar_farseer->alreadyAttacked());
    }

    /** @expectedException \App\Exceptions\MinionAlreadyAttackedException */
    public function test_thrallmar_farseer_can_not_attack_more_than_twice_per_turn() {
        $thrallmar_farseer = $this->playCard('Thrallmar Farseer', 1);
        $wisp              = $this->playCard('Wisp', 2);
        $wisp2             = $this->playCard('Wisp', 2);
        $wisp3             = $this->playCard('Wisp', 2);

        $thrallmar_farseer->attack($wisp);
        $thrallmar_farseer->attack($wisp2);
        $thrallmar_farseer->attack($wisp3);
    }

    public function test_si7_agent_does_not_combo_if_played_first() {
        $wisp = $this->playCard('Wisp', 2);
        $this->playCard('SI:7 Agent', 1, [$wisp], true);

        $this->assertTrue($wisp->isAlive());
    }

    public function test_si7_agent_combos_if_not_played_first() {
        $wisp = $this->playCard('Wisp', 2);

        $this->playCard('Wisp', 1, [], true);
        $this->playCard('SI:7 Agent', 1, [$wisp], true);

        $this->assertFalse($wisp->isAlive());
    }

    public function test_cards_played_this_turn_is_reset_at_end_of_turn() {
        $wisp = $this->playCard('Wisp', 2);

        $this->playCard('Wisp', 1, [], true);

        $this->game->getPlayer1()->passTurn();
        $this->game->getPlayer2()->passTurn();

        $this->playCard('SI:7 Agent', 1, [$wisp], true);

        $this->assertTrue($wisp->isAlive());
    }

    public function test_keeper_of_the_grove_kills_wisp_when_damage_is_chosen() {
        $wisp = $this->playCard('Wisp', 1);
        $this->playCard('Keeper of the Grove', 2, [$wisp], false, 1);

        $this->assertFalse($wisp->isAlive());
    }

    public function test_keeper_of_the_grove_silences_argent_squire_when_silence_is_chosen() {
        $argent_squire = $this->playCard('Argent Squire', 1);
        $this->playCard('Keeper of the Grove', 2, [$argent_squire], false, 2);

        $this->assertFalse($argent_squire->hasMechanic(Mechanics::$DIVINE_SHIELD));
    }

    public function test_player_gets_mana_crystal_at_beginning_of_turn() {
        $player_a = $this->game->getActivePlayer();
        $player_b = $this->game->getDefendingPlayer();
        $this->assertEquals(1, $player_a->getManaCrystalCount());
        $player_a->passTurn();
        $player_b->passTurn();
        $this->assertEquals(2, $player_a->getManaCrystalCount());
    }

    public function test_player_mana_crystals_reset_at_beginning_of_next_turn() {
        $player_a = $this->game->getActivePlayer();
        $player_b = $this->game->getDefendingPlayer();

        $this->assertEquals(0, $player_a->getManaCrystalsUsed());
        $this->playCardStrict('Argent Squire', $player_a->getPlayerId());
        $this->assertEquals(1, $player_a->getManaCrystalsUsed());

        $player_a->passTurn(); // player a: 1 crystal
        $player_b->passTurn(); // player b: 1 crystal

        $this->assertEquals(0, $player_a->getManaCrystalsUsed());
    }

    /** @expectedException \App\Exceptions\NotEnoughManaCrystalsException */
    public function test_knife_juggler_can_not_be_played_turn_one() {
        $this->playCardStrict('Knife Juggler');
    }

    public function test_earth_elemental_locks_three_mana_crystals_when_played() {
        $active_player_id = $this->game->getActivePlayer()->getPlayerId();
        $this->playCardStrict('Earth Elemental', $active_player_id, 5);
        $this->assertEquals(3, $this->game->getActivePlayer()->getLockedManaCrystalCount());
    }

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

    public function test_player_is_killed_when_hero_dies() {
        $this->initPlayers();

        $this->game->getPlayer2()->getHero()->takeDamage(28);
        $this->game->getPlayer1()->useAbility();

        $this->assertFalse($this->game->getPlayer2()->isAlive());
    }

    public function test_game_ends_when_player_is_killed() {
        $this->initPlayers();
        $this->game->getPlayer2()->getHero()->takeDamage(28);
        $this->game->getPlayer1()->useAbility();
        $this->assertTrue($this->game->isOver());
        $this->assertEquals(1, $this->game->getWinningPlayer()->getPlayerId());
    }

    public function test_card_order_increments_when_card_is_played() {
        $current_card_counter = $this->game->getCardsPlayedThisGame();
        $this->playCard('Wisp', 1);
        $this->assertEquals($current_card_counter + 1, $this->game->getCardsPlayedThisGame());
        $this->playCard('Wisp', 2);
        $this->assertEquals($current_card_counter + 2, $this->game->getCardsPlayedThisGame());
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

    public function test_knife_juggler_can_attack_hero_and_hero_takes_damage() {
        $this->initPlayers();

        $this->assertEquals(30, $this->game->getPlayer1()->getHero()->getHealth());
        $knife_juggler = $this->playCard('Knife Juggler', 2);
        $knife_juggler->attack($this->game->getPlayer1()->getHero());
        $this->assertEquals(27, $this->game->getPlayer1()->getHero()->getHealth());
    }

    public function test_armor_is_used_before_life_when_damage_is_taken_from_attack() {
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

    public function test_lights_justice_can_be_equipped() {
        $this->initPlayers(HeroClass::$PALADIN);
        $this->playWeaponCard('Light\'s Justice', 1);
        $this->assertEquals(1, $this->game->getPlayer1()->getHero()->getWeapon()->getAttack());
        $this->assertEquals(4, $this->game->getPlayer1()->getHero()->getWeapon()->getDurability());
        $this->assertEquals('Light\'s Justice', $this->game->getPlayer1()->getHero()->getWeapon()->getName());
    }

    public function test_knife_juggler_kills_enemy_minion_when_friendly_minion_is_summoned() {
        $knife_juggler = $this->playCard('Knife Juggler', 1);
        $knife_juggler->setRandomNumber(1);
        $wisp = $this->playCard('Wisp', 2);
        $this->playCard('Argent Squire', 1);
        $this->assertFalse($wisp->isAlive());
    }

    public function test_knife_juggler_damages_hero_when_friendly_minion_is_summoned() {
        $knife_juggler = $this->playCard('Knife Juggler', 1);
        $knife_juggler->setRandomNumber(0);
        $this->playCard('Argent Squire', 1);
        $this->assertEquals(29, $this->game->getPlayer2()->getHero()->getHealth());
    }

}


<?php
use App\Models\Card;
use App\Models\CardType;
use App\Models\HearthCloneTest;
use App\Models\HeroClass;
use App\Models\Heroes\Shaman;
use App\Models\Mechanics;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/23/15
 * Time: 3:01 PM
 */
class CardTest extends HearthCloneTest
{
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

        $this->assertEquals($hand_size_player1 + 1, $new_hand_size_player1);
        $this->assertEquals($hand_size_player2 + 1, $new_hand_size_player2);
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
        $wisp = $this->playCard($this->wisp_name, 2);
        $this->playCard($this->si7_agent, 1, [$wisp], true);

        $this->assertTrue($wisp->isAlive());
    }

    public function test_si7_agent_combos_if_not_played_first() {
        $wisp = $this->playCard($this->wisp_name, 2);

        $this->playCard($this->wisp_name, 1, [], true);
        $this->playCard($this->si7_agent, 1, [$wisp], true);

        $this->assertFalse($wisp->isAlive());
    }

    public function test_cards_played_this_turn_is_reset_at_end_of_turn() {
        $wisp = $this->playCard($this->wisp_name, 2);

        $this->playCard($this->wisp_name, 1, [], true);

        $this->game->getPlayer1()->passTurn();
        $this->game->getPlayer2()->passTurn();

        $this->playCard($this->si7_agent, 1, [$wisp], true);

        $this->assertTrue($wisp->isAlive());
    }

    public function test_keeper_of_the_grove_kills_wisp_when_damage_is_chosen() {
        $wisp = $this->playCard($this->wisp_name, 1);
        $this->playCard($this->keeper_of_the_grove_name, 2, [$wisp], false, 1);

        $this->assertFalse($wisp->isAlive());
    }

    public function test_keeper_of_the_grove_silences_argent_squire_when_silence_is_chosen() {
        $argent_squire = $this->playCard($this->argent_squire_name, 1);
        $this->playCard($this->keeper_of_the_grove_name, 2, [$argent_squire], false, 2);

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
        $this->playCardStrict($this->argent_squire_name, $player_a->getPlayerId());
        $this->assertEquals(1, $player_a->getManaCrystalsUsed());

        $player_a->passTurn(); // player a: 1 crystal
        $player_b->passTurn(); // player b: 1 crystal

        $this->assertEquals(0, $player_a->getManaCrystalsUsed());
    }

    /** @expectedException \App\Exceptions\NotEnoughManaCrystalsException */
    public function test_knife_juggler_can_not_be_played_turn_one() {
        $this->playCardStrict($this->knife_juggler_name);
    }

    public function test_earth_elemental_locks_three_mana_crystals_when_played() {
        $active_player_id = $this->game->getActivePlayer()->getPlayerId();
        $this->playCardStrict($this->earth_elemental_name, $active_player_id, 5);
        $this->assertEquals(3, $this->game->getActivePlayer()->getLockedManaCrystalCount());
    }

    public function test_locked_mana_crystals_are_unlocked_at_beginning_of_next_turn() {
        $player_a = $this->game->getActivePlayer();
        $player_b = $this->game->getDefendingPlayer();
        $this->playCardStrict($this->earth_elemental_name, $player_a->getPlayerId(), 5);

        $player_a->passTurn();
        $this->assertEquals(3, $player_a->getLockedManaCrystalCount());
        $player_b->passTurn();

        $this->assertEquals(0, $player_a->getLockedManaCrystalCount());
        $this->assertEquals(3, $player_a->getManaCrystalsUsed());
    }

    public function test_mage_power_kills_wisp() {
        $this->initPlayers();
        $wisp = $this->playCard($this->wisp_name, 1);
        $this->game->getPlayer2()->useAbility([$wisp]);

        $this->assertFalse($wisp->isAlive());
    }

    /** @expectedException \App\Exceptions\HeroPowerAlreadyFlippedException */
    public function test_mage_hero_power_can_only_be_played_once_per_turn() {
        $this->initPlayers();
        $wisp  = $this->playCard($this->wisp_name, 1);
        $wisp2 = $this->playCard($this->wisp_name, 1);
        $this->game->getPlayer2()->useAbility([$wisp]);
        $this->game->getPlayer2()->useAbility([$wisp2]);
    }

    public function test_mage_can_use_hero_power_twice_in_two_turns() {
        $this->initPlayers();
        $wisp  = $this->playCard($this->wisp_name, 1);
        $wisp2 = $this->playCard($this->wisp_name, 1);
        $this->game->getPlayer2()->useAbility([$wisp]);

        $this->game->getPlayer2()->passTurn();
        $this->game->getPlayer1()->passTurn();

        $this->game->getPlayer2()->useAbility([$wisp2]);

        $this->assertFalse($wisp->isAlive());
        $this->assertFalse($wisp2->isAlive());
    }

    public function test_hunter_power_does_two_damage_to_enemy_hero() {
        $this->initPlayers();
        $this->assertEquals(30, $this->game->getPlayer2()->getHero()->getHealth());
        $this->game->getPlayer1()->useAbility();
        $this->assertEquals(28, $this->game->getPlayer2()->getHero()->getHealth());
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
        $this->playCard($this->wisp_name, 1);
        $this->assertEquals($current_card_counter + 1, $this->game->getCardsPlayedThisGame());
        $this->playCard($this->wisp_name, 2);
        $this->assertEquals($current_card_counter + 2, $this->game->getCardsPlayedThisGame());
    }

    public function test_card_play_order_id_is_set_when_card_is_played() {
        $current_card_counter = $this->game->getCardsPlayedThisGame();
        $wisp1                = $this->playCard($this->wisp_name, 1);
        $wisp2                = $this->playCard($this->wisp_name, 1);

        $wisp3 = $this->playCard($this->wisp_name, 2);
        $wisp4 = $this->playCard($this->wisp_name, 2);

        $this->assertEquals($current_card_counter + 1, $wisp1->getPlayOrderId());
        $this->assertEquals($current_card_counter + 2, $wisp2->getPlayOrderId());
        $this->assertEquals($current_card_counter + 3, $wisp3->getPlayOrderId());
        $this->assertEquals($current_card_counter + 4, $wisp4->getPlayOrderId());
    }

    public function test_using_paladin_power_summons_silver_hand_recruit() {
        $this->initPlayers(HeroClass::$PALADIN);

        $this->assertEquals(0, count($this->game->getPlayer1()->getMinionsInPlay()));
        $this->game->getPlayer1()->useAbility();
        $this->assertEquals(1, count($this->game->getPlayer1()->getMinionsInPlay()));

        /** @var Card $minion_in_play */
        $minion_in_play = current($this->game->getPlayer1()->getMinionsInPlay());
        $this->assertEquals($this->silver_hand_recruit_name, $minion_in_play->getName());
    }

    public function test_using_priest_power_heals_target_by_two_health() {
        $this->initPlayers(HeroClass::$PRIEST);
        $chillwind_yeti = $this->playCard($this->chillwind_yeti_name, 1);
        $knife_juggler  = $this->playCard($this->knife_juggler_name, 2);
        $knife_juggler->attack($chillwind_yeti);

        $this->assertEquals(2, $chillwind_yeti->getHealth());
        $this->game->getPlayer1()->useAbility([$chillwind_yeti]);
        $this->assertEquals(4, $chillwind_yeti->getHealth());
    }

    public function test_using_priest_power_to_heal_does_not_go_over_thirty_life() {
        $this->initPlayers(HeroClass::$PRIEST);
        $this->assertEquals(30, $this->game->getPlayer1()->getHero()->getHealth());
        $this->game->getPlayer1()->useAbility([$this->game->getPlayer1()->getHero()]);
        $this->assertEquals(30, $this->game->getPlayer1()->getHero()->getHealth());
    }

    public function test_using_warlock_power_deals_two_damage_to_hero_and_player_draws_card() {
        $this->initPlayers(HeroClass::$WARLOCK);

        $this->assertEquals(30, $this->game->getPlayer1()->getHero()->getHealth());
        $this->assertEquals(0, $this->game->getPlayer1()->getHandSize());
        $this->game->getPlayer1()->useAbility();
        $this->assertEquals(28, $this->game->getPlayer1()->getHero()->getHealth());
        $this->assertEquals(1, $this->game->getPlayer1()->getHandSize());
    }

    public function test_using_warrior_power_adds_two_armor_to_hero() {
        $this->initPlayers(HeroClass::$WARRIOR);

        $this->assertEquals(0, $this->game->getPlayer1()->getHero()->getArmor());
        $this->game->getPlayer1()->useAbility();
        $this->assertEquals(2, $this->game->getPlayer1()->getHero()->getArmor());
    }

    public function test_armor_is_used_before_life_when_damage_is_taken_by_hero_power() {
        $this->initPlayers(HeroClass::$WARRIOR, [], HeroClass::$HUNTER);

        $this->assertEquals(30, $this->game->getPlayer1()->getHero()->getHealth());
        $this->assertEquals(0, $this->game->getPlayer1()->getHero()->getArmor());

        $this->game->getPlayer1()->useAbility();
        $this->assertEquals(2, $this->game->getPlayer1()->getHero()->getArmor());

        $this->game->getPlayer2()->useAbility();

        $this->assertEquals(30, $this->game->getPlayer1()->getHero()->getHealth());
        $this->assertEquals(0, $this->game->getPlayer1()->getHero()->getArmor());
    }

    public function test_knife_juggler_can_attack_hero_and_hero_takes_damage() {
        $this->initPlayers();

        $this->assertEquals(30, $this->game->getPlayer1()->getHero()->getHealth());
        $knife_juggler = $this->playCard($this->knife_juggler_name, 2);
        $knife_juggler->attack($this->game->getPlayer1()->getHero());
        $this->assertEquals(27, $this->game->getPlayer1()->getHero()->getHealth());
    }

    public function test_armor_is_used_before_life_when_damage_is_taken_from_attack() {
        $this->initPlayers(HeroClass::$WARRIOR);

        $this->assertEquals(30, $this->game->getPlayer1()->getHero()->getHealth());
        $this->assertEquals(0, $this->game->getPlayer1()->getHero()->getArmor());

        $this->game->getPlayer1()->useAbility();
        $this->assertEquals(2, $this->game->getPlayer1()->getHero()->getArmor());

        $knife_juggler = $this->playCard($this->knife_juggler_name, 2);
        $knife_juggler->attack($this->game->getPlayer1()->getHero());
        $this->assertEquals(29, $this->game->getPlayer1()->getHero()->getHealth());
        $this->assertEquals(0, $this->game->getPlayer1()->getHero()->getArmor());
    }

    public function test_lights_justice_can_be_equipped() {
        $this->initPlayers(HeroClass::$PALADIN);
        $this->playWeaponCard($this->lights_justice_name, 1);
        $this->assertEquals(1, $this->game->getPlayer1()->getHero()->getWeapon()->getAttack());
        $this->assertEquals(4, $this->game->getPlayer1()->getHero()->getWeapon()->getDurability());
        $this->assertEquals($this->lights_justice_name, $this->game->getPlayer1()->getHero()->getWeapon()->getName());
    }

    public function test_rogue_ability_epuips_a_1_2_dagger() {
        $this->initPlayers(HeroClass::$ROGUE);
        $this->game->getPlayer1()->useAbility();
        $this->assertEquals(1, $this->game->getPlayer1()->getHero()->getWeapon()->getAttack());
        $this->assertEquals(2, $this->game->getPlayer1()->getHero()->getWeapon()->getDurability());
        $this->assertEquals('Dagger', $this->game->getPlayer1()->getHero()->getWeapon()->getName());
    }

    public function test_druid_ability_adds_one_attack_and_one_armor() {
        $this->initPlayers(HeroClass::$DRUID);
        $this->game->getPlayer1()->useAbility();
        $this->assertEquals(1, $this->game->getPlayer1()->getHero()->getWeapon()->getAttack());
        $this->assertEquals(1, $this->game->getPlayer1()->getHero()->getArmor());
    }

    public function test_knife_juggler_kills_enemy_minion_when_friendly_minion_is_summoned() {
        $this->playCard($this->knife_juggler_name, 1);
        $wisp = $this->playCard($this->wisp_name, 2);
        $this->playCard($this->argent_squire_name, 1);
        $this->assertFalse($wisp->isAlive());
    }

    public function test_knife_juggler_damages_hero_when_friendly_minion_is_summoned() {
        $this->playCard($this->knife_juggler_name, 1);
        $this->playCard($this->argent_squire_name, 1);
        $this->assertEquals(29, $this->game->getPlayer2()->getHero()->getHealth());
    }

    public function test_shaman_ability_summons_a_totem() {
        $this->initPlayers(HeroClass::$SHAMAN);

        $player1 = $this->game->getPlayer1();
        $player1->useAbility();
        $minions_in_play = $player1->getMinionsInPlay();
        $this->assertEquals(1, count($minions_in_play));
        $minion = current($minions_in_play);

        /** @var Shaman $player1_hero */
        $player1_hero = $player1->getHero();
        $this->assertTrue(in_array($minion->getName(), $player1_hero->getTotems()));
    }

}


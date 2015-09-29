<?php
use App\Game\Cards\Mechanics;
use App\Models\HearthCloneTest;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/8/15
 * Time: 9:48 PM
 */
class BasicSpellTest extends HearthCloneTest
{
    /* Ancestral Healing */
    public function test_ancestral_healing_heals_minion_to_full_and_gives_taunt() {
        $chillwind_yeti = $this->playCard('Chillwind Yeti', 1);
        $chillwind_yeti->takeDamage(4);
        $this->assertEquals(1, $chillwind_yeti->getHealth());
        $this->playCard('Ancestral Healing', 1, [$chillwind_yeti]);
        $this->assertEquals(5, $chillwind_yeti->getHealth());
        $this->assertTrue($chillwind_yeti->hasMechanic(Mechanics::$TAUNT));
    }

    /* Arcane Explosion */
    public function test_arcane_explosion_deals_one_damage_to_all_enemy_minions() {
        $wisp1         = $this->playCard('Wisp', 1);
        $wisp2         = $this->playCard('Wisp', 1);
        $knife_juggler = $this->playCard('Knife Juggler', 1);

        $this->playCard('Arcane Explosion', 2);
        $this->assertFalse($wisp1->isAlive());
        $this->assertFalse($wisp2->isAlive());
        $this->assertEquals(1, $knife_juggler->getHealth());
        $this->assertEquals(30, $this->game->getPlayer1()->getHero()->getHealth());
    }

    /* Arcane Intellect */
    public function test_arcane_intellect_draws_two_cards() {
        $this->assertEquals(0, $this->game->getPlayer1()->getHandSize());
        $this->playCard('Arcane Intellect', 1);
        $this->assertEquals(2, $this->game->getPlayer1()->getHandSize());
    }

    /* Arcane Shot */
    public function test_arcane_shot_does_two_damage_to_hero_when_played() {
        $player2 = $this->game->getPlayer2();
        $this->playCard('Arcane Shot', 1, [$player2->getHero()]);
        $this->assertEquals(28, $player2->getHero()->getHealth());
    }

    public function test_arcane_shot_does_two_damage_to_minion_when_played() {
        $knife_juggler = $this->playCard('Knife Juggler', 1);
        $this->playCard('Arcane Shot', 2, [$knife_juggler]);
        $this->assertFalse($knife_juggler->isAlive());
    }

    /* Assassinate */
    public function test_assassinate_destroys_target_minion() {
        $wisp = $this->playCard('Wisp', 1);
        $this->playCard('Assassinate', 2, [$wisp]);
        $this->assertFalse($wisp->isAlive());
    }

    /** @expectedException App\Exceptions\InvalidTargetException */
    public function test_assassinate_throws_when_targeting_hero() {
        $this->playCard('Assassinate', 2, [$this->game->getPlayer1()->getHero()]);
    }

    /* Backstab */
    public function test_backstab_deals_two_damage_to_an_undamaged_minion() {
        $chillwind_yeti = $this->playCard('Chillwind Yeti', 1);
        $this->playCard('Backstab', 2, [$chillwind_yeti]);
        $this->assertEquals(3, $chillwind_yeti->getHealth());
    }

    /** @expectedException App\Exceptions\InvalidTargetException */
    public function test_backstab_throws_when_targeting_damaged_minion() {
        $chillwind_yeti = $this->playCard('Chillwind Yeti', 1);
        $chillwind_yeti->takeDamage(1);
        $this->playCard('Backstab', 2, [$chillwind_yeti]);
    }

    /* Blessing of Kings */
    public function test_blessing_of_kings_gives_wisp_4_4() {
        $wisp = $this->playCard('Wisp', 1);
        $this->playCard('Blessing of Kings', 1, [$wisp]);
        $this->assertEquals(5, $wisp->getAttack());
        $this->assertEquals(5, $wisp->getMaxHealth());
        $this->assertEquals(5, $wisp->getHealth());
    }

    /* Bloodlust */
    // TODO test bloodlust wears off at end of turn
    public function test_bloodlust_gives_friendly_minions_three_attack() {
        $wisp1 = $this->playCard('Wisp', 1);
        $wisp2 = $this->playCard('Wisp', 1);
        $this->playCard('Bloodlust', 1);
        $this->assertEquals(4, $wisp1->getAttack());
        $this->assertEquals(4, $wisp2->getAttack());
    }

    /* Consecration */
    public function test_consecration_deals_two_damage_to_all_enemies() {
        $wisp          = $this->playCard('Wisp', 1);
        $wisp1         = $this->playCard('Wisp', 2);
        $chillwind_yei = $this->playCard('Chillwind Yeti', 2);
        $this->playCard('Consecration', 1);
        $this->assertEquals(28, $this->game->getPlayer2()->getHero()->getHealth());
        $this->assertFalse($wisp1->isAlive());
        $this->assertEquals(3, $chillwind_yei->getHealth());
        $this->assertTrue($wisp->isAlive());
    }

    /* Divine Spirit */
    public function test_divine_spirit_doubles_targets_health() {
        $chillwind_yeti = $this->playCard('Chillwind Yeti', 1);
        $this->playCard('Divine Spirit', 1, [$chillwind_yeti]);
        $this->assertEquals(10, $chillwind_yeti->getHealth());
    }

    /* Execute */
    public function test_execute_kills_provided_damaged_minion() {
        $chillwind_yeti = $this->playCard('Chillwind Yeti', 1);
        $chillwind_yeti->takeDamage(2);
        $this->playCard('Execute', 1, [$chillwind_yeti]);
        $this->assertFalse($chillwind_yeti->isAlive());
    }

    /** @expectedException App\Exceptions\InvalidTargetException */
    public function test_execute_throws_when_target_is_not_damaged() {
        $chillwind_yeti = $this->playCard('Chillwind Yeti', 1);
        $this->playCard('Execute', 1, [$chillwind_yeti]);
    }

    /* Fan of Knives */
    //Todo must wait until we can have multiple abilities associated to a target
//    public function test_fan_of_knives_deals_1_damage_to_all_enemy_minions_and_player_draws_card() {
//        $wisp1          = $this->playCard('Wisp', 2);
//        $chillwind_yeti = $this->playCard('Chillwind Yeti', 2);
//        $this->playCard('Fan of Knives', 1);
//        $this->assertEquals(1, $this->game->getPlayer1()->getHandSize());
//        $this->assertFalse($wisp1->isAlive());
//        $this->assertEquals(4, $chillwind_yeti->getHealth());
//    }

    /* Fireball */
    public function test_fireball_does_six_damage() {
        $this->playCard('Fireball', 1, [$this->game->getPlayer2()->getHero()]);
        $this->assertEquals(24, $this->game->getPlayer2()->getHero()->getHealth());
    }

    /* Flamestrike */
    public function test_flamestrike_deals_four_damage_to_all_opponent_minions() {
        $wisp           = $this->playCard('Wisp', 2);
        $chillwind_yeti = $this->playCard('Chillwind Yeti', 2);
        $this->playCard('Flamestrike', 1);
        $this->assertFalse($wisp->isAlive());
        $this->assertEquals(1, $chillwind_yeti->getHealth());
    }

    /* Frost Nova */
    public function test_frost_nova_freezes_all_opponent_minions() {
        $wisp           = $this->playCard('Wisp', 2);
        $chillwind_yeti = $this->playCard('Chillwind Yeti', 2);
        $this->playCard('Frost Nova', 1, [], true);
        $this->assertTrue($wisp->isFrozen());
        $this->assertTrue($chillwind_yeti->isFrozen());
    }

    /* Hand of Protection */
    public function test_hand_of_protection_gives_minion_divine_shield() {
        $wisp = $this->playCard('Wisp', 1);
        $this->playCard('Hand of Protection', 1, [$wisp]);
        $this->assertTrue($wisp->hasMechanic(Mechanics::$DIVINE_SHIELD));
    }

    /* Healing Touch */
    public function test_healing_touch_heals_eight_health_to_friendly_hero() {
        $player1 = $this->game->getPlayer1();
        $player1->getHero()->takeDamage(10);
        $this->playCard('Healing Touch', 1);
        $this->assertEquals(28, $player1->getHero()->getHealth());
    }

    public function test_healing_touch_can_only_heal_up_to_thirty_life() {
        $player1 = $this->game->getPlayer1();
        $player1->getHero()->takeDamage(5);
        $this->playCard('Healing Touch', 1);
        $this->assertEquals(30, $player1->getHero()->getHealth());
    }

    /* Hellfire */
    public function test_hellfire_deals_three_damage_to_all_characters() {
        $player1 = $this->game->getPlayer1();
        $player2 = $this->game->getPlayer2();
        $chillwind_yeti1 = $this->playCard('Chillwind Yeti', 1);
        $chillwind_yeti2 = $this->playCard('Chillwind Yeti', 2);
        $this->playCard('Hellfire', 1);
        $this->assertEquals(27, $player1->getHero()->getHealth());
        $this->assertEquals(27, $player2->getHero()->getHealth());
        $this->assertEquals(2, $chillwind_yeti1->getHealth());
        $this->assertEquals(2, $chillwind_yeti2->getHealth());
    }

    /* Holy Light */
    public function test_holy_light_heals_six_health_to_friendly_hero() {
        $player1 = $this->game->getPlayer1();
        $player1->getHero()->takeDamage(10);
        $this->playCard('Holy Light', 1);
        $this->assertEquals(26, $player1->getHero()->getHealth());
    }

    public function test_holy_light_can_only_heal_up_to_thirty_life() {
        $player1 = $this->game->getPlayer1();
        $player1->getHero()->takeDamage(3);
        $this->playCard('Holy Light', 1);
        $this->assertEquals(30, $player1->getHero()->getHealth());
    }

    /* Holy Smite */
    public function test_holy_smite_deals_two_damage() {
        $this->playCard('Holy Smite', 1, [$this->game->getPlayer2()->getHero()]);
        $this->assertEquals(28, $this->game->getPlayer2()->getHero()->getHealth());
    }

    /* Humility */
    public function test_humility_sets_target_attack_to_one() {
        $chillwind_yeti = $this->playCard('Chillwind Yeti', 1);
        $this->playCard('Humility', 2, [$chillwind_yeti]);
        $this->assertEquals(1, $chillwind_yeti->getAttack());
    }

    /* Hunters Mark */
    public function test_hunters_mark_sets_target_health_to_one() {
        $chillwind_yeti = $this->playCard('Chillwind Yeti', 1);
        $this->playCard('Hunter\'s Mark', 2, [$chillwind_yeti]);
        $this->assertEquals(1, $chillwind_yeti->getHealth());
    }

    /* Wild Growth */
    public function test_playing_wild_growth_adds_one_mana_crystal() {
        $this->playCardStrict('Wild Growth', 1, 2, []);
        $this->assertEquals(3, $this->game->getPlayer1()->getManaCrystalCount());
    }

    /* Windfury */
    public function test_playing_spell_windfury_gives_a_minion_windfury() {
        $wisp = $this->playCard('Wisp', 1);
        $this->playCard('Windfury', 1, [$wisp]);
        $this->assertTrue($wisp->hasMechanic(Mechanics::$WINDFURY));
    }

}
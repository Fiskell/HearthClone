<?php

use App\Game\Cards\Card;
use App\Game\Cards\Heroes\HeroClass;
use App\Game\Cards\Heroes\Shaman;
use App\Game\Cards\Minion;
use App\Models\HearthCloneTest;

class HeroPowerTest extends HearthCloneTest
{
    /* Druid */
    public function test_druid_ability_adds_one_attack_and_one_armor() {
        $this->initPlayers(HeroClass::$DRUID);
        $this->game->getPlayer1()->useAbility();
        $this->assertEquals(1, $this->game->getPlayer1()->getHero()->getAttack());
        $this->assertEquals(1, $this->game->getPlayer1()->getHero()->getArmor());
    }

    /* Hunter */
    public function test_hunter_power_does_two_damage_to_enemy_hero() {
        $this->initPlayers();
        $this->assertEquals(30, $this->game->getPlayer2()->getHero()->getHealth());
        $this->game->getPlayer1()->useAbility();
        $this->assertEquals(28, $this->game->getPlayer2()->getHero()->getHealth());
    }

    /* Mage */
    public function test_mage_power_kills_wisp() {
        $this->initPlayers();
        $wisp = $this->playCard('Wisp', 1);
        $this->game->getPlayer2()->useAbility([$wisp]);

        $this->assertFalse($wisp->isAlive());
    }

    /** @expectedException \App\Exceptions\HeroPowerAlreadyFlippedException */
    public function test_mage_hero_power_can_only_be_played_once_per_turn() {
        $this->initPlayers();
        $wisp  = $this->playCard('Wisp', 1);
        $wisp2 = $this->playCard('Wisp', 1);
        $this->game->getPlayer2()->useAbility([$wisp]);
        $this->game->getPlayer2()->useAbility([$wisp2]);
    }

    public function test_mage_can_use_hero_power_twice_in_two_turns() {
        $this->initPlayers();
        $wisp  = $this->playCard('Wisp', 1);
        $wisp2 = $this->playCard('Wisp', 1);
        $this->game->getPlayer2()->useAbility([$wisp]);

        $this->game->getPlayer2()->passTurn();
        $this->game->getPlayer1()->passTurn();

        $this->game->getPlayer2()->useAbility([$wisp2]);

        $this->assertFalse($wisp->isAlive());
        $this->assertFalse($wisp2->isAlive());
    }

    /* Paladin */
    public function test_using_paladin_power_summons_silver_hand_recruit() {
        $this->initPlayers(HeroClass::$PALADIN);

        $this->assertEquals(0, count($this->game->getPlayer1()->getMinionsInPlay()));
        $this->game->getPlayer1()->useAbility();
        $this->assertEquals(1, count($this->game->getPlayer1()->getMinionsInPlay()));

        /** @var Card $minion_in_play */
        $minion_in_play = current($this->game->getPlayer1()->getMinionsInPlay());
        $this->assertEquals('Silver Hand Recruit', $minion_in_play->getName());
    }

    /* Priest */
    public function test_using_priest_power_heals_target_by_two_health() {
        $this->initPlayers(HeroClass::$PRIEST);
        $chillwind_yeti = $this->playCard('Chillwind Yeti', 1);
        $knife_juggler  = $this->playCard('Knife Juggler', 2);
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

    public function test_priest_can_not_overheal_a_minion_past_max_hp() {
        $this->initPlayers(HeroClass::$PRIEST);
        $chillwind_yeti = $this->playCard('Chillwind Yeti', 1);
        $chillwind_yeti->takeDamage(1);
        $this->game->getPlayer1()->useAbility([$chillwind_yeti]);
        $this->assertEquals(5, $chillwind_yeti->getHealth());
    }

    /* Rogue */
    public function test_rogue_ability_epuips_a_1_2_wicked_knife() {
        $this->initPlayers(HeroClass::$ROGUE);
        $this->game->getPlayer1()->useAbility();
        $this->assertEquals(1, $this->game->getPlayer1()->getHero()->getWeapon()->getAttack());
        $this->assertEquals(2, $this->game->getPlayer1()->getHero()->getWeapon()->getDurability());
        $this->assertEquals('Wicked Knife', $this->game->getPlayer1()->getHero()->getWeapon()->getName());
    }

    /* Shaman */
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

    public function test_shaman_ability_summons_healing_totem() {
        $random_mock = Mockery::mock('Random')->makePartial();
        $random_mock->shouldReceive('getFromRange')->once()->andReturn(0);
        $this->instance('Random', $random_mock);

        $this->initPlayers(HeroClass::$SHAMAN);

        $this->player1->useAbility();
        $minions_in_play = $this->player1->getMinionsInPlay();
        $this->assertEquals(1, count($minions_in_play));

        /** @var Minion $minion */
        $minion = current($minions_in_play);
        $this->assertEquals('Healing Totem', $minion->getName());
    }

    public function test_shaman_ability_summons_searing_totem() {
        $random_mock = Mockery::mock('Random')->makePartial();
        $random_mock->shouldReceive('getFromRange')->once()->andReturn(1);
        $this->instance('Random', $random_mock);

        $this->initPlayers(HeroClass::$SHAMAN);

        $this->player1->useAbility();
        $minions_in_play = $this->player1->getMinionsInPlay();
        $this->assertEquals(1, count($minions_in_play));

        /** @var Minion $minion */
        $minion = current($minions_in_play);
        $this->assertEquals('Searing Totem', $minion->getName());
    }

    public function test_shaman_ability_summons_stoneclaw_totem() {
        $random_mock = Mockery::mock('Random')->makePartial();
        $random_mock->shouldReceive('getFromRange')->once()->andReturn(2);
        $this->instance('Random', $random_mock);

        $this->initPlayers(HeroClass::$SHAMAN);

        $this->player1->useAbility();
        $minions_in_play = $this->player1->getMinionsInPlay();
        $this->assertEquals(1, count($minions_in_play));

        /** @var Minion $minion */
        $minion = current($minions_in_play);
        $this->assertEquals('Stoneclaw Totem', $minion->getName());
    }

    public function test_shaman_ability_summons_wrath_of_air_totem() {
        $random_mock = Mockery::mock('Random')->makePartial();
        $random_mock->shouldReceive('getFromRange')->once()->andReturn(3);
        $this->instance('Random', $random_mock);

        $this->initPlayers(HeroClass::$SHAMAN);

        $this->player1->useAbility();
        $minions_in_play = $this->player1->getMinionsInPlay();
        $this->assertEquals(1, count($minions_in_play));

        /** @var Minion $minion */
        $minion = current($minions_in_play);
        $this->assertEquals('Wrath of Air Totem', $minion->getName());
    }

    /* Warlock */
    public function test_using_warlock_power_deals_two_damage_to_hero_and_player_draws_card() {
        $this->initPlayers(HeroClass::$WARLOCK);

        $this->assertEquals(30, $this->game->getPlayer1()->getHero()->getHealth());
        $this->assertEquals(0, $this->game->getPlayer1()->getHandSize());
        $this->game->getPlayer1()->useAbility();
        $this->assertEquals(28, $this->game->getPlayer1()->getHero()->getHealth());
        $this->assertEquals(1, $this->game->getPlayer1()->getHandSize());
    }

    /* Warrior */
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

}
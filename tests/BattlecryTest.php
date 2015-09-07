<?php
use App\Models\HearthCloneTest;
use App\Models\HeroClass;
use App\Models\Mechanics;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/5/15
 * Time: 7:00 PM
 */
class BattlecryTest extends HearthCloneTest
{
    /* Houndmaster */

    public function test_houndmaster_does_not_target_himself() {
        $this->initPlayers();
        $houndmaster = $this->playCard($this->houndmaster_name, 1);
        $this->assertEquals(4, $houndmaster->getAttack());
        $this->assertEquals(3, $houndmaster->getHealth());
    }

    /** @expectedException \App\Exceptions\InvalidTargetException */
    public function test_houndmaster_fails_when_target_is_not_a_beast() {
        $this->initPlayers();
        $wisp = $this->playCard($this->wisp_name, 1);
        $this->playCard($this->houndmaster_name, 1, [$wisp]);
    }

    public function test_houndmaster_adds_2_2_and_taunt_to_valid_beast_target() {
        $this->initPlayers();
        $timber_wolf = $this->playCard($this->timber_wolf_name, 1);
        $this->playCard($this->houndmaster_name, 1, [$timber_wolf]);
        $this->assertEquals(3, $timber_wolf->getAttack());
        $this->assertEquals(3, $timber_wolf->getHealth());
        $this->assertTrue($timber_wolf->hasMechanic(Mechanics::$TAUNT));
    }

    /* Guardian of Kings */
    public function test_guardian_of_kings_heals_friendly_hero_by_6() {
        $this->initPlayers();
        $this->game->getPlayer1()->getHero()->takeDamage(20);
        $this->playCard($this->guardian_of_kings_name, 1);
        $this->assertEquals(16, $this->game->getPlayer1()->getHero()->getHealth());
    }

    /* Windspeaker */
    public function test_windspeaker_gives_friendly_minion_windfury_when_played() {
        $this->initPlayers();
        $wisp = $this->playCard($this->wisp_name, 1);
        $this->assertTrue(!$wisp->hasMechanic(Mechanics::$WINDFURY));
        $this->playCard($this->windspeaker_name, 1, [$wisp]);
        $this->assertTrue($wisp->hasMechanic(Mechanics::$WINDFURY));
    }

    /* Fire Elemental */
    public function test_fire_elemental_does_3_damage_when_played() {
        $this->initPlayers();
        $player2 = $this->game->getPlayer2();
        $this->assertEquals(30, $player2->getHero()->getHealth());
        $this->playCard($this->fire_elemental_name, 1, [$player2->getHero()]);
        $this->assertEquals(27, $player2->getHero()->getHealth());
    }

    /* Succubus */
    public function test_succubus_forces_player_to_discard_one_card() {
        $this->initPlayers();
        $player1 = $this->game->getPlayer1();
        $player1->drawCard();
        $player1->drawCard();
        $this->assertEquals(2, $player1->getHandSize());
        $this->playCard($this->succubus_name, 1);
        $this->assertEquals(1, $player1->getHandSize());
    }

    public function test_succubus_does_not_discard_when_hand_is_empty() {
        $this->initPlayers();
        $player1 = $this->game->getPlayer1();
        $this->assertEquals(0, $player1->getHandSize());
        $this->playCard($this->succubus_name, 1);
        $this->assertEquals(0, $player1->getHandSize());
    }

    /* Dread Infernal */
    public function test_dread_infernal_damages_all_other_characters() {
        $this->initPlayers();
        $wisp           = $this->playCard('Wisp', 1);
        $wisp2          = $this->playCard('Wisp', 2);
        $dread_infernal = $this->playCard('Dread Infernal', 1);
        $this->assertFalse($wisp->isAlive());
        $this->assertFalse($wisp2->isAlive());
        $this->assertEquals(29, $this->game->getPlayer1()->getHero()->getHealth());
        $this->assertEquals(29, $this->game->getPlayer2()->getHero()->getHealth());
        $this->assertEquals(6, $dread_infernal->getHealth());
    }

    /* Acidic Swamp Ooze */
    public function test_acidic_swamp_ooze_destroys_enemy_weapon_when_played() {
        $this->initPlayers(HeroClass::$PALADIN);
        $this->playWeaponCard($this->lights_justice_name, 1);
        $this->assertTrue(!!$this->game->getPlayer1()->getHero()->getWeapon());
        $this->playCard('Acidic Swamp Ooze', 2);
        $this->assertFalse(!!$this->game->getPlayer1()->getHero()->getWeapon());
    }
}
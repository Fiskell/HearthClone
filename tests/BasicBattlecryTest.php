<?php
use App\Game\Cards\Heroes\HeroClass;
use App\Game\Cards\Mechanics;
use App\Models\HearthCloneTest;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/5/15
 * Time: 7:00 PM
 */
class BasicBattlecryTest extends HearthCloneTest
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

    /* Darkscale Healer*/
    public function test_darkscale_healer_heals_hero_by_2() {
        $player1 = $this->game->getPlayer1();
        $player1->getHero()->takeDamage(5);
        $this->assertEquals(25, $player1->getHero()->getHealth());
        $this->playCard('Darkscale Healer', 1);
        $this->assertEquals(27, $player1->getHero()->getHealth());
    }

    public function test_darkscale_healer_heals_damaged_chillwind_yeti() {
        $chillwind_yeti = $this->playCard('Chillwind Yeti', 1);
        $chillwind_yeti->takeDamage(2);
        $this->assertEquals(3, $chillwind_yeti->getHealth());
        $this->playCard('Darkscale Healer', 1);
        $this->assertEquals(5, $chillwind_yeti->getHealth());
    }

    /* Elven Archer */
    public function test_elven_archer_kills_wisp_when_played() {
        $wisp = $this->playCard('Wisp', 1);
        $this->playCard('Elven Archer', 2, [$wisp]);
        $this->assertFalse($wisp->isAlive());
    }

    /* Voodoo Doctor */
    public function test_voodoo_doctor_heals_friendly_hero_by_2() {
        $hero = $this->game->getPlayer1()->getHero();
        $hero->takeDamage(2);
        $this->assertEquals(28, $hero->getHealth());
        $this->playCard('Voodoo Doctor', 1);
        $this->assertEquals(30, $hero->getHealth());
    }

    /* Shattered Sun Cleric */
    public function test_shattered_sun_cleric_gives_wisp_1_1() {
        $wisp = $this->playCard('Wisp', 1);
        $this->playCard('Shattered Sun Cleric', 1, [$wisp]);
        $this->assertEquals(2, $wisp->getAttack());
        $this->assertEquals(2, $wisp->getAttack());
    }

    /* Novice Engineer */
    public function test_novice_engineer_draws_card_when_played() {
        $player = $this->game->getPlayer1();
        $this->assertEquals(0, $player->getHandSize());
        $this->playCard('Novice Engineer', 1);
        $this->assertEquals(1, $player->getHandSize());
    }

    /* Gnomish Inventor */
    public function test_gnomish_inventor_draws_card_when_played() {
        $player = $this->game->getPlayer1();
        $this->assertEquals(0, $player->getHandSize());
        $this->playCard('Gnomish Inventor', 1);
        $this->assertEquals(1, $player->getHandSize());
    }

    /* Stormpike Commando */
    public function test_stormpike_commando_deals_2_damage_to_minion_when_played() {
        $chillwind_yeti = $this->playCard('Chillwind Yeti', 1);
        $this->playCard('Stormpike Commando', 2, [$chillwind_yeti]);
        $this->assertEquals(3, $chillwind_yeti->getHealth());
    }

    /* Razorfen Hunter */
    public function test_razorfen_hunter_plays_boar_when_not_max_minions() {
        $this->playCard('Razorfen Hunter', 1);
        $player = $this->game->getPlayer1();
        $this->assertEquals(2, count($player->getMinionsInPlay()));
    }

    /* Ironforge Rifleman */
    public function test_ironforge_rifleman_deals_1_damage_to_minion_when_played() {
        $chillwind_yeti = $this->playCard('Chillwind Yeti', 1);
        $this->playCard('Ironforge Rifleman', 2, [$chillwind_yeti]);
        $this->assertEquals(4, $chillwind_yeti->getHealth());
    }

    /* Nightblade */
    public function test_nightblade_deals_3_damage_to_minion_when_played() {
        $this->playCard('Nightblade', 1);
        $this->assertEquals(27, $this->game->getPlayer2()->getHero()->getHealth());
    }

    /* Murloc Tidehunter */
    public function test_murlock_tidehunter_plays_murloc_scout_when_not_max_minions() {
        $this->playCard('Murloc Tidehunter', 1);
        $player = $this->game->getPlayer1();
        $this->assertEquals(2, count($player->getMinionsInPlay()));
    }

    /* Dragonling Mechanic */
    public function test_dragonling_mechanic_plays_mechanical_dragonling_when_not_max_minions() {
        $this->playCard('Dragonling Mechanic', 1);
        $player = $this->game->getPlayer1();
        $this->assertEquals(2, count($player->getMinionsInPlay()));
    }


    /* Frostwolf Warlord */
    public function test_frostwolf_warlord_gains_no_health_with_empty_board() {
        $frostwolf_warlord = $this->playCard('Frostwolf Warlord', 1);
        $this->assertEquals(4, $frostwolf_warlord->getAttack());
        $this->assertEquals(4, $frostwolf_warlord->getHealth());
    }

    public function test_frostwolf_warlord_gains_2_2_when_2_minions_on_field() {
        $this->playCard('Wisp', 1);
        $this->playCard('Wisp', 1);
        $frostwolf_warlord = $this->playCard('Frostwolf Warlord', 1);
        $this->assertEquals(6, $frostwolf_warlord->getAttack());
        $this->assertEquals(6, $frostwolf_warlord->getHealth());
    }

}
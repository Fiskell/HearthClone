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
        $wisp1 = $this->playCard('Wisp', 1);
        $wisp2 = $this->playCard('Wisp', 1);
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
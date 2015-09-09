<?php
use App\Models\HearthCloneTest;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/8/15
 * Time: 9:48 PM
 */
class BasicSpellTest extends HearthCloneTest
{
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

}
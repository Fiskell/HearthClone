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
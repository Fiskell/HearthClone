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
    public function test_arcane_shot_does_two_damage_when_played() {
        $player2 = $this->game->getPlayer2();
        $this->playCard('Arcane Shot', 1, [$player2]);
        $this->assertEquals(28, $player2->getHero()->getHealth());

    }
}
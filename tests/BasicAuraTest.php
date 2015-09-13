<?php namespace tests;

use App\Models\HearthCloneTest;

class BasicAuraTest extends HearthCloneTest
{
    public function test_leokk_gives_two_friendly_minions_one_attack() {
        $wisp = $this->playCard('Wisp', 1);

        $this->playCard('Leokk', 1);
        $this->assertEquals(2, $wisp->getAttack());

        $wisp2 = $this->playCard('Wisp', 1);
        $this->assertEquals(2, $wisp2->getAttack());
    }
}
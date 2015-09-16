<?php namespace tests;

use App\Models\HearthCloneTest;

class BasicAuraTest extends HearthCloneTest
{
    /* Leokk */
    public function test_leokk_gives_two_friendly_minions_one_attack() {
        $wisp = $this->playCard('Wisp', 1);

        $this->playCard('Leokk', 1);
        $this->assertEquals(2, $wisp->getAttack());

        $wisp2 = $this->playCard('Wisp', 1);

        // Make sure wisp 1 is still buffed
        $this->assertEquals(2, $wisp->getAttack());

        $this->assertEquals(2, $wisp2->getAttack());
    }

    /* Raid Leader */
    public function test_raid_leader_gives_two_friendly_minions_one_attack() {
        $wisp = $this->playCard('Wisp', 1);

        $this->playCard('Raid Leader', 1);
        $this->assertEquals(2, $wisp->getAttack());

        $wisp2 = $this->playCard('Wisp', 1);

        // Make sure wisp 1 is still buffed
        $this->assertEquals(2, $wisp->getAttack());

        $this->assertEquals(2, $wisp2->getAttack());
    }

    /* Timber Wolf */
    public function test_timber_wolf_gives_beast_one_attack() {
        $leokk = $this->playCard('Leokk', 1);
        $this->playCard('Timber Wolf', 1);
        $this->assertEquals(3, $leokk->getAttack());
    }

    public function test_timber_wolf_does_not_buff_non_wisp() {
        $wisp = $this->playCard('Wisp', 1);
        $this->playCard('Timber Wolf', 1);
        $this->assertEquals(1, $wisp->getAttack());
    }

}
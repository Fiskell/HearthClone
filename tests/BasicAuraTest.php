<?php namespace tests;

use App\Models\HearthCloneTest;

class BasicAuraTest extends HearthCloneTest
{
    /* Flametongue Totem */
    public function test_flametongue_totem_gives_adjacent_minions_two_attack() {
        $wisp1 = $this->playWispAtPosition(1, 1);
        $wisp2 = $this->playWispAtPosition(1, 2);
        $this->playCard('Flametongue Totem', 1, [], false, null, 2);
        $this->assertEquals(3, $wisp1->getAttack());
        $this->assertEquals(3, $wisp2->getAttack());
    }

    /* Grimscale Oracle */
    public function test_grimscale_give_friendly_and_opponent_murloc_one_attack() {
        $bluegill_warrior1 = $this->playCard('Bluegill Warrior', 1);
        $bluegill_warrior2 = $this->playCard('Bluegill Warrior', 2);
        $wisp = $this->playCard('Wisp', 1);
        $this->playCard('Grimscale Oracle', 1);
        $this->assertEquals(3, $bluegill_warrior1->getAttack());
        $this->assertEquals(3, $bluegill_warrior2->getAttack());

        // Should not buff the wisp
        $this->assertEquals(1, $wisp->getAttack());
    }

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

    /* Stormwind Champion */
    public function test_stormwind_champion_gives_other_minions_1_1() {
        $knife_juggler = $this->playCard('Knife Juggler', 1);
        $this->playCard('Stormwind Champion', 1);
        $this->assertEquals(4, $knife_juggler->getAttack());
        $this->assertEquals(3, $knife_juggler->getHealth());
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
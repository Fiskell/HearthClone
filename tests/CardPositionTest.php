<?php namespace tests;

use App\Models\HearthCloneTest;

class CardPositionTest extends HearthCloneTest
{
    public function test_single_card_goes_to_position_1() {
        $wisp = $this->playCard('Wisp', 1, [], false, null, 1);
        $this->assertEquals(1, $wisp->getPosition());
    }

    public function test_second_minion_moves_first_minion_to_position_2() {
        $wisp = $this->playCard('Wisp', 1, [], false, null, 1);
        $this->assertEquals(1, $wisp->getPosition());
        $wisp2 = $this->playCard('Wisp', 1, [], false, null, 2);

        $this->assertEquals(1, $wisp->getPosition());
        $this->assertEquals(2, $wisp2->getPosition());
    }

    public function test_first_minion_dies_moves_second_position_minion_to_position_1() {
        $attacking_wisp = $this->playCard('Wisp', 2);

        $wisp = $this->playCard('Wisp', 1, [], false, null, 1);
        $this->assertEquals(1, $wisp->getPosition());
        $wisp2 = $this->playCard('Wisp', 1, [], false, null, 2);

        $this->assertEquals(1, $wisp->getPosition());
        $this->assertEquals(2, $wisp2->getPosition());

        $attacking_wisp->attack($wisp);

        $this->assertEquals(1, $wisp2->getPosition());
    }

}
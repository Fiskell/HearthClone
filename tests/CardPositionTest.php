<?php namespace tests;

use App\Models\HearthCloneTest;

class CardPositionTest extends HearthCloneTest
{
    /**
     * Before:
     * . . . . . . .
     *
     * After play (1 option):
     * . . . 1 . . .
     */
    public function test_single_card_goes_to_position_3() {
        $wisp = $this->playCard('Wisp', 1, [], false, null, 3);
        $this->assertEquals(3, $wisp->getPosition());
    }

    /**
     * Before:
     * . . . 1 . . .
     *
     * After play (1 option):
     * . . 1 2 . . .
     */
    public function test_second_minion_moves_first_minion_to_position_2() {
        $wisp = $this->playCard('Wisp', 1, [], false, null, 3);
        $this->assertEquals(3, $wisp->getPosition());
        $wisp2 = $this->playCard('Wisp', 1, [], false, null, 3);

        $this->assertEquals(2, $wisp->getPosition());
        $this->assertEquals(3, $wisp2->getPosition());
    }

}
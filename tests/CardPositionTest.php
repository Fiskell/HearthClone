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

    public function test_first_minion_dies_on_both_sides_moves_second_position_minion_to_position_1() {
        $wisp_player2_1 = $this->playCard('Wisp', 2, [], false, null, 1);
        $wisp_player2_2 = $this->playCard('Wisp', 2, [], false, null, 2);

        $wisp_player1_1 = $this->playCard('Wisp', 1, [], false, null, 1);
        $this->assertEquals(1, $wisp_player1_1->getPosition());
        $wisp_player1_2 = $this->playCard('Wisp', 1, [], false, null, 2);

        $this->assertEquals(1, $wisp_player1_1->getPosition());
        $this->assertEquals(2, $wisp_player1_2->getPosition());

        $wisp_player2_1->attack($wisp_player1_1);

        $this->assertEquals(1, $wisp_player1_2->getPosition());
        $this->assertEquals(1, $wisp_player2_2->getPosition());
    }

}
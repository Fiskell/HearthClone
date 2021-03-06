<?php namespace tests;

use App\Models\HearthCloneTest;

class CardPositionTest extends HearthCloneTest
{
    public function test_single_card_goes_to_position_one() {
        $wisp = $this->playWispAtPosition(1, 1);
        $this->assertEquals(1, $wisp->getPosition());
    }

    public function test_second_minion_moves_first_minion_to_position_two() {
        $wisp = $this->playWispAtPosition(1, 1);
        $this->assertEquals(1, $wisp->getPosition());
        $wisp2 = $this->playWispAtPosition(1, 2);

        $this->assertEquals(1, $wisp->getPosition());
        $this->assertEquals(2, $wisp2->getPosition());
    }

    public function test_second_minion_played_at_position_one_forces_other_minion_to_position_two() {
        $wisp  = $this->playWispAtPosition(1, 1);
        $wisp2 = $this->playWispAtPosition(1, 1);
        $this->assertEquals(1, $wisp2->getPosition());
        $this->assertEquals(2, $wisp->getPosition());
    }

    public function test_third_minion_played_between_first_and_second_shifts_second_minion_to_third_position() {
        $wisp  = $this->playWispAtPosition(1, 1);
        $wisp2 = $this->playWispAtPosition(1, 2);
        $wisp3 = $this->playWispAtPosition(1, 2);
        $this->assertEquals(1, $wisp->getPosition());
        $this->assertEquals(2, $wisp3->getPosition());
        $this->assertEquals(3, $wisp2->getPosition());
    }

    public function test_first_minion_dies_on_both_sides_moves_second_position_minion_to_position_one() {
        $wisp_player2_1 = $this->playWispAtPosition(2, 1);
        $wisp_player2_2 = $this->playWispAtPosition(2, 2);

        $wisp_player1_1 = $this->playWispAtPosition(1, 1);
        $this->assertEquals(1, $wisp_player1_1->getPosition());
        $wisp_player1_2 = $this->playWispAtPosition(1, 2);

        $this->assertEquals(1, $wisp_player1_1->getPosition());
        $this->assertEquals(2, $wisp_player1_2->getPosition());

        $wisp_player2_1->attack($wisp_player1_1);

        $this->assertEquals(1, $wisp_player1_2->getPosition());
        $this->assertEquals(1, $wisp_player2_2->getPosition());
    }

}
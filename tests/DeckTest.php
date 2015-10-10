<?php

use App\Models\HearthCloneTest;

class DeckTest extends HearthCloneTest
{
    public function test_draw_card_removes_one_card_from_deck() {
        $deck_card_count = $this->game->getPlayer1()->getDeck()->getRemainingCount();
        $this->game->getPlayer1()->drawCard();
        $this->assertEquals(($deck_card_count - 1), $this->game->getPlayer1()->getDeck()->getRemainingCount());
    }
}
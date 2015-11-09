<?php namespace App\Game\Zones;

use App\Game\Cards\Card;
use App\Game\Player;

class Decision
{
    //todo decision should eventually be a factory for types of decisions.

    /**
     * Return array of cards for the player's opening hand.
     *
     * @param Player $player
     * @return Card[]
     */
    public function drawOpeningHand(Player $player) {
        $opening_hand = [];
        for($i = 0; $i < 4; $i++) {
             $drawn_card = $player->getDeck()->draw();
             $opening_hand[$drawn_card->getId()] = $drawn_card;
        }
        return $opening_hand;
    }


}
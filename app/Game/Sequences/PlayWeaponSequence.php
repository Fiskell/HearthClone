<?php namespace App\Game\Sequences;

use App\Game\Cards\Card;
use App\Game\Cards\Weapon;

class PlayWeaponSequence extends CardSequence
{
    public function resolve(Card $card) {
        $player = $card->getOwner();
        /** @var Weapon $card */
        $player->getHero()->equipWeapon($card);
    }
}
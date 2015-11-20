<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/12/15
 * Time: 2:12 PM
 */

namespace App\Game\Sequences;

use App\Game\Cards\Card;
use App\Game\Cards\Weapon;

class PlayWeaponSequence extends CardSequence
{
    public function resolve(Card $card, array $targets = []) {
        $player = $card->getOwner();
        /** @var Weapon $card */
        $player->getHero()->equipWeapon($card, $targets);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/12/15
 * Time: 2:04 PM
 */

namespace App\Game\Sequences;

use App\Exceptions\NotEnoughManaCrystalsException;
use App\Game\Cards\Card;
use App\Game\Cards\CardType;
use App\Game\Cards\Minion;

class CardSequence extends AbstractSequence
{
    public function play(Card $card, array $targets=[], $choose_mechanic=null) {

        $player = $card->getOwner();
        $remaining_mana_crystals = $player->getManaCrystalCount() - $player->getManaCrystalsUsed();
        if (($remaining_mana_crystals - $card->getCost()) < 0) {
            throw new NotEnoughManaCrystalsException('Cost of ' . $card->getName() . ' is ' . $card->getCost() . ' you have ' . $remaining_mana_crystals);
        }

        $player->setManaCrystalsUsed($player->getManaCrystalsUsed() + $card->getCost());


        switch ($card->getType()) {
            case CardType::$MINION:
//                PlayMinionSequence::resolve($card, $targets, $choose_mechanic);
                /** @var Minion $card */
                $player->playMinion($card, $targets, $choose_mechanic);
                break;
            case CardType::$SPELL:
                $player->playSpell($card, $targets, $choose_mechanic);
                break;
            case CardType::$WEAPON:
                $player->playWeapon($card, $targets);
                break;
        }

        $player->incrementCardsPlayedThisTurn();

        $player->getGame()->resolveDeaths();
    }
}
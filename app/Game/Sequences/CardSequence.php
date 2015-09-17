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
use App\Game\Sequences\Phases\SubCardPhase;

class CardSequence extends AbstractSequence
{
    public function play(Card $card, array $targets = [], $position) {
        $player = $card->getOwner();

        $remaining_mana_crystals = $player->getManaCrystalCount() - $player->getManaCrystalsUsed();
        if (($remaining_mana_crystals - $card->getCost()) < 0) {
            throw new NotEnoughManaCrystalsException('Cost of ' . $card->getName() . ' is ' . $card->getCost() . ' you have ' . $remaining_mana_crystals);
        }

        if($card instanceof Minion) {
            // todo validation
            $card->setPosition($position);
        }

        $player->setManaCrystalsUsed($player->getManaCrystalsUsed() + $card->getCost());

        switch ($card->getType()) {
            case CardType::$MINION:
                App('PlayMinionSequence')->resolve($card, $targets);
                break;
            case CardType::$SPELL:
                App('PlaySpellSequence')->resolve($card, $targets);
                break;
            case CardType::$WEAPON:
                App('PlayWeaponSequence')->resolve($card, $targets);
                break;
        }

        $player->incrementCardsPlayedThisTurn();

        $player->getGame()->resolveDeaths();
    }

    /**
     * @param Minion $minion
     * @param $targets
     * @param $sub_phase_type
     * @return SubCardPhase
     */
    public function resolveSubPhase(Minion $minion, $targets, $sub_phase_type) {
        /** @var SubCardPhase $choose_one_sub_phase */
        $choose_one_sub_phase = App('SubCardPhase');
        $choose_one_sub_phase->queue($minion, $targets);
        $choose_one_sub_phase->setPhaseName($sub_phase_type);
        App('TriggerQueue')->resolveQueue();
    }
}
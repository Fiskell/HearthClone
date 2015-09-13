<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/13/15
 * Time: 2:21 PM
 */

namespace app\Game\Cards;


class Aura extends Enchantment
{
    public function load(Card $card) {
        $aura                  = $card->getTrigger();
        $this->id              = $card->getId();
        $this->source_card     = $card;
        $this->name            = array_get($aura, 'aura.name');
        $this->modified_attack = array_get($aura, 'aura.attack');
        $this->modified_health = array_get($aura, 'aura.health');
    }
}
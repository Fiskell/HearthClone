<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/13/15
 * Time: 2:21 PM
 */

namespace App\Game\Cards;


class Aura extends Enchantment
{
    public function load(Card $card) {
        $aura                     = $card->getTrigger();
        $this->id                 = $card->getId();
        $this->source_card        = $card;
        $this->name               = array_get($aura, 'aura.0.buff_name', 'No Name');
        $this->modified_attack    = array_get($aura, 'aura.0.attack', 0);
        $this->modified_health    = array_get($aura, 'aura.0.max_health', 0);
        $this->modified_mechanics = array_get($aura, 'aura.0.mechanics', []);
    }
}
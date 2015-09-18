<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/12/15
 * Time: 6:25 PM
 */

namespace App\Game\Sequences\Phases;

use App\Game\Cards\Aura;
use App\Game\Cards\Minion;
use App\Game\Player;

class AuraHealth extends CardPhase
{
    public $phase_name = 'aura_health';

    public function queueAllForPlayer(Player $player) {
        $all_minions = $player->getAllMinions();

        foreach ($all_minions as $single_minion) {

            if (!array_get($single_minion->getTrigger(), 'aura.enchantment.health')) {
                continue;
            }

            $tmp_aura          = App('AuraHealth');
            $tmp_aura->card    = $single_minion;
            App('TriggerQueue')->queue($tmp_aura);
        }
    }

    public function resolve() {
        $aura_trigger = array_get($this->card->getTrigger(), 'aura');
        $target_type  = array_get($aura_trigger, 'targets.type');
        $target_race  = array_get($aura_trigger, 'targets.race');
        $targets      = $this->getTargets($this->card, $target_type, $target_race);

        /** @var Aura $aura */
        $aura = App('Aura');
        $aura->load($this->card);

        /** @var Minion $target */
        foreach ($targets as $target) {
            // todo clean up
            $target->setMaxHealth($target->getMaxHealth() + $aura->getModifiedHealth());
        }
    }

}
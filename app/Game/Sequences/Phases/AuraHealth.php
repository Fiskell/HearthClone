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

class AuraHealth extends CardPhase
{
    public $phase_name = 'aura_health';

    public function queue(Minion $minion, array $targets = []) {
        $all_minions = $minion->getOwner()->getAllMinions($minion);

        foreach ($all_minions as $single_minion) {

            if (!array_get($single_minion->getTrigger(), 'aura.enchantment.health')) {
                continue;
            }

            $tmp_aura          = App('AuraHealth');
            $tmp_aura->card    = $single_minion;
            $tmp_aura->targets = $targets;
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
            $target->setHealth($target->getHealth() + $aura->getModifiedHealth());
        }
    }

}
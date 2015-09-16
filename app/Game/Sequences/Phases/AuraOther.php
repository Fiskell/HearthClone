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

class AuraOther extends CardPhase
{
    public $phase_name = 'aura_other';

    public function queue(Minion $minion, array $targets = []) {
        $all_minions = $minion->getOwner()->getAllMinions($minion);

        foreach ($all_minions as $single_minion) {
            // Clear out old auras.
//            $single_minion->setAuras([]);

            if (!array_get($single_minion->getTrigger(), 'aura')) {
                continue;
            }

            // todo I think I need to queue only auras and then each aura should know how to interact with it's targets.
            // todo or i could have each aura apply auras and then at the end recalculate for each card.
            // todo or i could have each aura apply to the 'type' and then figure out the overlap and what stat changes need to happen and apply at one time.
            // Add card to queue to have aura calculations applied
            $tmp_aura          = App('AuraOther');
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
        $aura         = App('Aura');
        $aura->load($this->card);

        /** @var Minion $target */
        foreach ($targets as $target) {
            $target->addAura($aura);
        }
    }

}
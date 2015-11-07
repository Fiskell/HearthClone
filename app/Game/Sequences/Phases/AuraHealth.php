<?php namespace App\Game\Sequences\Phases;

use App\Game\Cards\Aura;
use App\Game\Cards\Minion;
use App\Game\Cards\Triggers\TargetTypes;
use App\Game\Player;

class AuraHealth extends CardPhase
{
    public $phase_name = 'aura_health';

    public function queueAllForPlayer(Player $player) {
        $all_minions = $player->getAllMinions();

        foreach ($all_minions as $single_minion) {

            if (!array_get($single_minion->getTrigger(), 'aura.0.max_health')) {
                continue;
            }

            $tmp_aura          = App('AuraHealth');
            $tmp_aura->card    = $single_minion;
            App('TriggerQueue')->queue($tmp_aura);
        }
    }

    public function resolve() {
        $aura_trigger = array_get($this->card->getTrigger(), 'aura.0');
        $target_type  = array_get($aura_trigger, 'target_type');
        $target_race  = array_get($aura_trigger, 'target_race');
        $targets      = TargetTypes::getTargets($this->card, $target_type, $target_race, $this->targets);

        /** @var Aura $aura */
        $aura = App('Aura', [$this->card]);

        /** @var Minion $target */
        foreach ($targets as $target) {
            $target->setMaxHealth($target->getMaxHealth() + $aura->getModifiedHealth());
        }
    }

}
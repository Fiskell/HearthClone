<?php namespace App\Listeners;

use App\Events\SummonEvent;
use App\Game\Cards\Mechanics;
use App\Game\Cards\Minion;
use App\Models\TriggerQueue;

class OnPlayPhase extends AbstractTrigger
{
    /**
     * Handle the event.
     *
     * @param  SummonEvent $event
     * @return void
     */
    public function handle(SummonEvent $event) {
        $this->event = $event;

        $this->trigger_card            = $event->getSummonedMinion();
        $this->trigger_card_targets    = $event->getTargets();
        $this->trigger_choose_mechanic = $event->getChooseMechanic();

        /** @var TriggerQueue $trigger_queue */
        $trigger_queue = app('TriggerQueue');
        $trigger_queue->queue($this);
    }

    public function resolve() {
        $player = $this->trigger_card->getOwner();
        /** @var Minion $card */
        $card    = $this->trigger_card;
        $targets = $this->trigger_card_targets;
        $choose_mechanic = $this->trigger_choose_mechanic;

        if ($card->hasMechanic(Mechanics::$OVERLOAD)) {
            // todo I hate this
            $player->addLockedManaCrystalCount($card->getOverloadValue());
        }

        if ($card->hasMechanic(Mechanics::$SPELL_POWER)) {
            $player->recalculateSpellPower();
        }

        if ($card->hasMechanic(Mechanics::$CHOOSE)) {
            $card->resolveChoose($targets, $choose_mechanic);
        }

        if ($card->hasMechanic(Mechanics::$COMBO) && $player->getCardsPlayedThisTurn() > 0) {
            $card->resolveCombo($targets);
        }
    }
}

<?php namespace App\Listeners;

use App\Events\SummonEvent;
use App\Game\Cards\Mechanics;
use App\Game\Cards\Minion;
use App\Game\Cards\Triggers\TriggerTypes;
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

        $this->trigger_card         = $event->getSummonedMinion();
        $this->trigger_card_targets = $event->getTargets();

        /** @var TriggerQueue $trigger_queue */
        $trigger_queue = app('TriggerQueue');
        $trigger_queue->queue($this);
    }

    public function resolve() {
        $player = $this->trigger_card->getOwner();
        /** @var Minion $card */
        $card    = $this->trigger_card;
        $targets = $this->trigger_card_targets;

        if ($card->hasMechanic(Mechanics::$OVERLOAD)) {
            // todo I hate this
            $player->addLockedManaCrystalCount($card->getOverloadValue());
        }

        if ($card->hasMechanic(Mechanics::$SPELL_POWER)) {
            $player->recalculateSpellPower();
        }

        if (array_get($card->getTrigger(), TriggerTypes::$CHOOSE_ONE)) {
            $card->resolveChoose($targets);
        }

        if ($card->hasMechanic(Mechanics::$COMBO) && $player->getCardsPlayedThisTurn() > 0) {
            $card->resolveCombo($targets);
        }
    }
}

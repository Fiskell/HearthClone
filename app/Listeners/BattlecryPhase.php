<?php

namespace App\Listeners;

use App\Events\BattlecryPhaseEvent;
use App\Exceptions\DumbassDeveloperException;
use App\Exceptions\InvalidTargetException;
use App\Models\Mechanics;
use App\Models\Minion;
use App\Models\Player;
use App\Models\TriggerableInterface;
use App\Models\TriggerQueue;
use App\Models\Triggers\TargetTypes;
use App\Models\Triggers\TriggerTypes;

class BattlecryPhase extends SummonListener implements TriggerableInterface
{
    /** @var BattlecryPhaseEvent $event */
    private $event;

    /**
     * Handle the event.
     *
     * @param BattlecryPhaseEvent $event
     * @return void
     */
    public function handle(BattlecryPhaseEvent $event) {
        $this->event = $event;

        if ($event->getSummonedMinion()->hasMechanic(Mechanics::$BATTLECRY)) {

            /** @var Minion $target */
            foreach ($event->getTargets() as $target) {
                if ($target->hasMechanic(Mechanics::$STEALTH)) {
                    throw new InvalidTargetException('Cannot silence stealth minion');
                }
            }

            /** @var TriggerQueue $trigger_queue */
            $trigger_queue = app('TriggerQueue');
            $trigger_queue->queue($this);
        }
    }

    public function resolve() {
        $trigger_array              = $this->getSetTriggers();
        $summoned_minion            = $this->event->getSummonedMinion();
        $player                     = $summoned_minion->getOwner();
        $player_minions             = $player->getMinionsInPlay();
        $opponent                   = $player->getOtherPlayer();
        $opponent_minions           = $opponent->getMinionsInPlay();
        $hero_id                    = $opponent->getHero()->getId();
        $opponent_minions[$hero_id] = $opponent->getHero();

        // todo assumes we only have one trigger.
        $trigger = array_get($trigger_array, $this->event->getSummonedMinion()->getName() . '.triggers.0.' . TriggerTypes::$BATTLECRY);

        if (is_null($trigger)) {
            throw new DumbassDeveloperException('Trigger not specified for ' . $this->event->getSummonedMinion()->getName());
        }


        $target_type = array_get($trigger, 'targets.type');
        if (is_null($target_type)) {
            throw new DumbassDeveloperException('Missing target type for ' . $this->event->getSummonedMinion()->getName());
        }

        switch ($target_type) {
            case TargetTypes::$PROVIDED_MINION:
                $num_targets = array_get($trigger, 'targets.quantity');

                // todo some battlecry may require a minimum number of targets.
//                if(count($this->event->getTargets()) < $num_targets) {
//                    throw new InvalidTargetException(count($this->event->getTargets()) . ' targets passed in, expected ' . $num_targets);
//                }

                $targets = $this->event->getTargets();
                break;
            case TargetTypes::$FRIENDLY_HERO:
                $targets = [$player->getHero()];
                break;
            case TargetTypes::$FRIENDLY_PLAYER:
                $targets = [$player];
                break;
            default:
                throw new DumbassDeveloperException('Unknown target type ' . $target_type);
        }

        /* Check if race is correct */
        $required_race = array_get($trigger, 'targets.race');
        if ($required_race) {
            /** @var Minion $target */
            foreach ($targets as $target) {
                if (strtolower($target->getRace()) != strtolower($required_race)) {
                    throw new InvalidTargetException('Target must be a ' . $required_race . ' ' . $target->getRace() . ' given');
                }
            }
        }

        /* Damage */
        $damage = array_get($trigger, 'damage.value');
        if (!is_null($damage)) {
            if (!count($targets)) {
                throw new InvalidTargetException('You must have at least one target');
            }

            /** @var Minion $target */
            foreach ($targets as $target) {
                $target->takeDamage($damage);
            }
        }

        /* Silence */
        $silence = array_get($trigger, 'silence');
        if (!is_null($silence)) {
            foreach ($targets as $target) {
                $target->removeAllMechanics();
            }
        }

        /* Enchantment */
        $enchantment = array_get($trigger, 'enchantment');
        if (!is_null($enchantment)) {
            foreach ($targets as $target) {
                $target->setMechanics(array_get($enchantment, 'mechanics', []));

                $delta_attack = array_get($enchantment, 'attack', 0);
                $target->setAttack($target->getAttack() + $delta_attack);

                $delta_health = array_get($enchantment, 'health', 0);
                $target->setHealth($target->getHealth() + $delta_health);


            }
        }

        /* Discard */
        $discard = array_get($trigger, 'discard');
        if (!is_null($discard)) {
            /** @var Player $target */
            foreach ($targets as $target) {
                $type     = array_get($discard, 'type');
                $quantity = array_get($discard, 'quantity');
                switch($type) {
                    case 'random':
                        $target->discardRandom($quantity);
                        break;
                    default:
                        throw new DumbassDeveloperException('Unknown discard type ' . $type);
                }
            }
        }
    }
}

<?php namespace App\Listeners;

use App\Events\BattlecryPhaseEvent;
use App\Exceptions\DumbassDeveloperException;
use App\Exceptions\InvalidTargetException;
use App\Models\AbstractHero;
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
     * @throws InvalidTargetException
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
        $trigger_array    = $this->getSetTriggers();
        $summoned_minion  = $this->event->getSummonedMinion();

        // todo assumes we only have one trigger.
        $trigger = array_get($trigger_array, $this->event->getSummonedMinion()->getName() . '.triggers.0.' . TriggerTypes::$BATTLECRY);

        if (is_null($trigger)) {
            throw new DumbassDeveloperException('Trigger not specified for ' . $this->event->getSummonedMinion()->getName());
        }

        $targets = [];
        if(array_get($trigger, 'targets')) {
            $target_type = array_get($trigger, 'targets.type');
            if (is_null($target_type)) {
                throw new DumbassDeveloperException('Missing target type for ' . $summoned_minion->getName());
            }

            $targets = $this->getTargets($trigger, $summoned_minion, $target_type);
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

        /* Destroy */
        $destroy = array_get($trigger, 'destroy');
        if (!is_null($destroy)) {
            /** @var AbstractHero $target */
            foreach ($targets as $target) {
                // todo may need to have other types of things to destroy.
                $target->destroyWeapon();
            }
        }

        /* Silence */
        $silence = array_get($trigger, 'silence');
        if (!is_null($silence)) {
            foreach ($targets as $target) {
                $target->removeAllMechanics();
            }
        }

        /* Spell */
        $spell = array_get($trigger, 'spell');
        if (!is_null($spell)) {
            foreach ($targets as $target) {
                $target->setMechanics(array_get($spell, 'mechanics', []));

                $delta_attack = array_get($spell, 'attack', 0);
                $target->setAttack($target->getAttack() + $delta_attack);

                $delta_health = array_get($spell, 'health', 0);
                $target->setHealth($target->getHealth() + $delta_health);
            }

            $attack_by_count = array_get($spell, 'attack_by_count');
            if(!is_null($attack_by_count)) {
                $delta_attack = count($this->getTargets($trigger, $summoned_minion, $attack_by_count));
                $summoned_minion->setAttack($summoned_minion->getAttack() + $delta_attack);
            }

            $health_by_count = array_get($spell, 'health_by_count');
            if(!is_null($health_by_count)) {
                $delta_health = count($this->getTargets($trigger, $summoned_minion, $health_by_count));
                $summoned_minion->setHealth($summoned_minion->getHealth() + $delta_health);
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
                $target->setMaxHealth($target->getHealth() + $delta_health);
                $target->setHealth($target->getHealth() + $delta_health);
            }

            $attack_by_count = array_get($enchantment, 'attack_by_count');
            if(!is_null($attack_by_count)) {
                $delta_attack = count($this->getTargets($trigger, $summoned_minion, $attack_by_count));
                $summoned_minion->setAttack($summoned_minion->getAttack() + $delta_attack);
            }

            $health_by_count = array_get($enchantment, 'health_by_count');
            if(!is_null($health_by_count)) {
                $delta_health = count($this->getTargets($trigger, $summoned_minion, $health_by_count));
                $summoned_minion->setMaxHealth($summoned_minion->getHealth() + $delta_health);
                $summoned_minion->setHealth($summoned_minion->getHealth() + $delta_health);
            }
        }

        /* Discard */
        $discard = array_get($trigger, 'discard');
        if (!is_null($discard)) {
            /** @var Player $target */
            foreach ($targets as $target) {
                $type     = array_get($discard, 'type');
                $quantity = array_get($discard, 'quantity');
                switch ($type) {
                    case 'random':
                        $target->discardRandom($quantity);
                        break;
                    default:
                        throw new DumbassDeveloperException('Unknown discard type ' . $type);
                }
            }
        }

        /* Draw */
        $draw = array_get($trigger, 'draw');
        if(!is_null($draw)) {
            /** @var Player $target */
            foreach($targets as $target) {
                for($i = 0; $i < $draw; $i++) {
                    $target->drawCard();
                }
            }
        }

        /* Summon */
        $summon_name = array_get($trigger, 'summon.name');
        $summon_quantity = array_get($trigger, 'summon.quantity');
        if(!is_null($summon_name)) {
            /** @var Player $target */
            for($i = 0; $i < $summon_quantity; $i++) {
                $tmp_minion = app('Minion', [$summoned_minion->getOwner()]);
                $tmp_minion->load($summon_name);
                $this->event->getSummonedMinion()->getOwner()->play($tmp_minion);
            }
        }
    }

    /**
     * @param $trigger
     * @param Minion $summoned_minion
     * @return array
     * @throws DumbassDeveloperException
     */
    private function getTargets($trigger, $summoned_minion, $target_type) {
        $player           = $summoned_minion->getOwner();
        $player_minions   = $player->getMinionsInPlay();
        $opponent         = $player->getOtherPlayer();
        $opponent_minions = $opponent->getMinionsInPlay();

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
            case TargetTypes::$ALL_OTHER_CHARACTERS:
                $opponent_minions[$opponent->getHero()->getId()] = $opponent->getHero();
                $player_minions[$player->getHero()->getId()]     = $player->getHero();

                $targets = $opponent_minions + $player_minions;
                unset($targets[$summoned_minion->getId()]);
                break;
            case TargetTypes::$OPPONENT_HERO:
                $targets = [$opponent->getHero()];
                break;
            case TargetTypes::$ALL_FRIENDLY_CHARACTERS:
                $player_minions[$player->getHero()->getId()] = $player->getHero();
                $targets                                     = $player_minions;
                break;
            case TargetTypes::$OTHER_FRIENDLY_MINIONS:
                unset($player_minions[$summoned_minion->getId()]);
                $targets = $player_minions;
                break;
            default:
                throw new DumbassDeveloperException('Unknown target type ' . $target_type);
        }
        
        return $targets;
    }
}

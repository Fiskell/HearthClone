<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/8/15
 * Time: 10:54 PM
 */

namespace App\Listeners;

use App\Events\SummonEvent;
use App\Exceptions\DumbassDeveloperException;
use App\Exceptions\InvalidTargetException;
use App\Game\Cards\Card;
use App\Game\Cards\Heroes\AbstractHero;
use App\Game\Cards\Minion;
use App\Game\Cards\Triggers\TargetTypes;
use App\Game\CardSets\CardSets;
use App\Game\Player;
use App\Models\TriggerableInterface;

abstract class AbstractTrigger implements TriggerableInterface
{
    public $event                = null;

    public $event_name           = null;

    /** @var Card $trigger_card */
    public $trigger_card         = null;

    public $trigger_card_targets = [];

    public $set_triggers         = [];

    public function __construct() {
        foreach (CardSets::$set_names as $set_name) {
            $trigger_json = @file_get_contents(__DIR__ . '/../../resources/triggers/' . $set_name . '.json');

            if (!$trigger_json) {
                continue;
            }

            $trigger_array      = json_decode($trigger_json, true);
            $this->set_triggers = array_merge($this->set_triggers, $trigger_array);
        }
    }

    abstract function handle(SummonEvent $event);

    /**
     * @return array
     */
    public function getSetTriggers() {
        return $this->set_triggers;
    }

    public function resolve() {
        $trigger_array = $this->getSetTriggers();
        $trigger_card  = $this->trigger_card;

        $trigger = array_get($trigger_array, $trigger_card->getName() . '.' . $this->event_name);

        if (is_null($trigger)) {
            throw new DumbassDeveloperException('Trigger not specified for ' . $this->trigger_card->getName());
        }

        $targets = [];
        if (array_get($trigger, 'targets')) {
            $target_type = array_get($trigger, 'targets.type');
            if (is_null($target_type)) {
                throw new DumbassDeveloperException('Missing target type for ' . $trigger_card->getName());
            }

            $targets = $this->getTargets($trigger, $trigger_card, $target_type);
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

                $full_health = array_get($spell, 'full_health');
                if($full_health) {
                    $target->setHealth($target->getMaxHealth());
                }
            }

            $attack_by_count = array_get($spell, 'attack_by_count');
            if (!is_null($attack_by_count)) {
                $delta_attack = count($this->getTargets($trigger, $trigger_card, $attack_by_count));
                $trigger_card->setAttack($trigger_card->getAttack() + $delta_attack);
            }

            $health_by_count = array_get($spell, 'health_by_count');
            if (!is_null($health_by_count)) {
                $delta_health = count($this->getTargets($trigger, $trigger_card, $health_by_count));
                $trigger_card->setHealth($trigger_card->getHealth() + $delta_health);
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
            if (!is_null($attack_by_count)) {
                $delta_attack = count($this->getTargets($trigger, $trigger_card, $attack_by_count));
                $trigger_card->setAttack($trigger_card->getAttack() + $delta_attack);
            }

            $health_by_count = array_get($enchantment, 'health_by_count');
            if (!is_null($health_by_count)) {
                $delta_health = count($this->getTargets($trigger, $trigger_card, $health_by_count));
                $trigger_card->setMaxHealth($trigger_card->getHealth() + $delta_health);
                $trigger_card->setHealth($trigger_card->getHealth() + $delta_health);
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
        if (!is_null($draw)) {
            /** @var Player $target */
            foreach ($targets as $target) {
                for ($i = 0; $i < $draw; $i++) {
                    $target->drawCard();
                }
            }
        }

        /* Summon */
        $summon_name     = array_get($trigger, 'summon.name');
        $summon_quantity = array_get($trigger, 'summon.quantity');
        if (!is_null($summon_name)) {
            /** @var Player $target */
            for ($i = 0; $i < $summon_quantity; $i++) {
                $tmp_minion = app('Minion', [$trigger_card->getOwner()]);
                $tmp_minion->load($summon_name);
                $this->trigger_card->getOwner()->play($tmp_minion);
            }
        }
    }

    /**
     * @param $trigger
     * @param Minion $trigger_card
     * @param $target_type
     * @return array
     * @throws DumbassDeveloperException
     */
    private function getTargets($trigger, $trigger_card, $target_type) {
        $player           = $trigger_card->getOwner();
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

                $targets = $this->trigger_card_targets;
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
                unset($targets[$trigger_card->getId()]);
                break;
            case TargetTypes::$OPPONENT_HERO:
                $targets = [$opponent->getHero()];
                break;
            case TargetTypes::$ALL_FRIENDLY_CHARACTERS:
                $player_minions[$player->getHero()->getId()] = $player->getHero();
                $targets                                     = $player_minions;
                break;
            case TargetTypes::$OTHER_FRIENDLY_MINIONS:
                unset($player_minions[$trigger_card->getId()]);
                $targets = $player_minions;
                break;
            case TargetTypes::$RANDOM_OPPONENT_CHARACTER:
                $opponent_minions[$opponent->getHero()->getId()] = $opponent->getHero();
                $targets = $opponent_minions;
                break;
            case TargetTypes::$ALL_OPPONENT_MINIONS:
                $targets = $opponent_minions;
                break;
            default:
                throw new DumbassDeveloperException('Unknown target type ' . $target_type);
        }

        return $targets;
    }
}
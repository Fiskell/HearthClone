<?php namespace App\Game\Sequences\Phases;

use App\Exceptions\DumbassDeveloperException;
use App\Exceptions\InvalidTargetException;
use App\Game\Cards\Card;
use App\Game\Cards\Heroes\AbstractHero;
use App\Game\Cards\Minion;
use App\Game\Cards\Triggers\TargetTypes;
use App\Game\Cards\Triggers\TriggerTypes;
use App\Game\Player;

abstract class CardPhase extends AbstractPhase
{
    /** @var Minion $minion */
    protected $card;
    protected $targets;
    public $phase_name;

    abstract function queue(Minion $minion, array $targets=[]);

    public function resolve() {

        $trigger = array_get($this->card->getTrigger(), $this->phase_name);

        if (is_null($trigger)) {
            $trigger = array_get($this->card->getTrigger(), TriggerTypes::$CHOOSE_ONE . '.' . ($this->card->getChooseOption() - 1));
        }

        if (is_null($trigger)) {
            throw new DumbassDeveloperException('Trigger not specified for ' . $this->card->getName());
        }

        $targets = [];
        if (array_get($trigger, 'targets')) {
            $target_type = array_get($trigger, 'targets.type');
            if (is_null($target_type)) {
                throw new DumbassDeveloperException('Missing target type for ' . $this->card->getName());
            }

            $targets = $this->getTargets($this->card, $target_type);
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
                if ($full_health) {
                    $target->setHealth($target->getMaxHealth());
                }
            }

            $attack_by_count = array_get($spell, 'attack_by_count');
            if (!is_null($attack_by_count)) {
                $delta_attack = count($this->getTargets($this->card, $attack_by_count));
                $this->card->setAttack($this->card->getAttack() + $delta_attack);
            }

            $health_by_count = array_get($spell, 'health_by_count');
            if (!is_null($health_by_count)) {
                $delta_health = count($this->getTargets($this->card, $health_by_count));
                $this->card->setHealth($this->card->getHealth() + $delta_health);
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
                $delta_attack = count($this->getTargets($this->card, $attack_by_count));
                $this->card->setAttack($this->card->getAttack() + $delta_attack);
            }

            $health_by_count = array_get($enchantment, 'health_by_count');
            if (!is_null($health_by_count)) {
                $delta_health = count($this->getTargets($this->card, $health_by_count));
                $this->card->setMaxHealth($this->card->getHealth() + $delta_health);
                $this->card->setHealth($this->card->getHealth() + $delta_health);
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
                $tmp_minion = app('Minion', [$this->card->getOwner()]);
                $tmp_minion->load($summon_name);
                $this->card->getOwner()->play($tmp_minion);
            }
        }
    }

    /**
     * @param Card $trigger_card
     * @param $target_type
     * @return array
     * @throws DumbassDeveloperException
     */
    private
    function getTargets(Card $trigger_card, $target_type) {

        $player           = $trigger_card->getOwner();
        $player_minions   = $player->getMinionsInPlay();
        $opponent         = $player->getOtherPlayer();
        $opponent_minions = $opponent->getMinionsInPlay();

        switch ($target_type) {
            case TargetTypes::$PROVIDED_MINION:
                // todo some battlecry may require a minimum number of targets.
                $targets = $this->targets;
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
                $targets                                         = $opponent_minions;
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
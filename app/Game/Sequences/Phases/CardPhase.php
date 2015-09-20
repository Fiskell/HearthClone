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
    public    $phase_name;

    public function queue(Minion $minion, array $targets = []) {
        throw new DumbassDeveloperException('Queue function not overridden');
    }

    public function queueAllForPlayer(Player $player) {
        throw new DumbassDeveloperException('QueueAll function not overridden');
    }

    public function resolve() {

        $trigger = array_get($this->card->getTrigger(), $this->phase_name);
        if (array_get($this->card->getTrigger(), TriggerTypes::$CHOOSE_ONE)) {
            $trigger = array_get($this->card->getTrigger(), TriggerTypes::$CHOOSE_ONE . '.' . ($this->card->getChooseOption() - 1));
        }


        if (array_get($this->card->getTrigger(), TriggerTypes::$OVERLOAD)) {
            $overload_value = array_get($this->card->getTrigger(), TriggerTypes::$OVERLOAD);
            $this->card->getOwner()->addLockedManaCrystalCount($overload_value);
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
        $this->validateRace($trigger, $targets);

        /* Destroy */
        $this->resolveDestroyTrigger($trigger, $targets);

        /* Silence */
        $this->resolveSilenceTrigger($trigger, $targets);

        /* Spell */
        $this->resolveSpellTrigger($trigger, $targets);

        /* Enchantment */
        $this->resolveEnchantmentTrigger($trigger, $targets);

        /* Discard */
        $this->resolveDiscardTrigger($trigger, $targets);

        /* Draw */
        $this->resolveDrawTrigger($trigger, $targets);

        /* Summon */
        $this->resolveSummonTrigger($trigger);
    }

    /**
     * @param Card $trigger_card
     * @param $target_type
     * @return array
     * @throws DumbassDeveloperException
     */
    protected function getTargets(Card $trigger_card, $target_type, $target_race = null) {

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
            case TargetTypes::$OTHER_FRIENDLY_MINIONS_WITH_RACE:
                unset($player_minions[$trigger_card->getId()]);
                $targets = [];
                /** @var Minion $player_minion */
                foreach ($player_minions as $player_minion) {
                    if ($player_minion->getRace() == $target_race) {
                        $targets[] = $player_minion;
                    }
                }
                break;
            case TargetTypes::$All_OTHER_MINIONS_WITH_RACE:
                unset($player_minions[$trigger_card->getId()]);
                $targets = [];
                foreach ($player_minions as $player_minion) {
                    if ($player_minion->getRace() == $target_race) {
                        $targets[] = $player_minion;
                    }
                }
                foreach ($opponent_minions as $opponent_minion) {
                    if ($opponent_minion->getRace() == $target_race) {
                        $targets[] = $opponent_minion;
                    }
                }
                break;
            case TargetTypes::$ADJACENT_MINIONS:
                /** @var Minion $trigger_card */
                $adjacent_positions = [
                    ($trigger_card->getPosition() - 1),
                    ($trigger_card->getPosition() + 1)
                ];

                $targets = [];
                foreach ($player_minions as $minion) {
                    if (in_array($minion->getPosition(), $adjacent_positions)) {
                        $targets[] = $minion;
                    }
                }

                break;
            case TargetTypes::$SELF:
                $targets = [$trigger_card];
                break;
            default:
                throw new DumbassDeveloperException('Unknown target type ' . $target_type);
        }

        return $targets;
    }

    /**
     * @param $trigger
     * @param $targets
     * @return int
     */
    private function resolveDrawTrigger($trigger, $targets) {
        $draw = array_get($trigger, 'draw');

        if (is_null($draw)) {
            return;
        }

        /** @var Player $target */
        foreach ($targets as $target) {
            for ($i = 0; $i < $draw; $i++) {
                $target->drawCard();
            }
        }
    }

    /**
     * @param $trigger
     */
    private function resolveSummonTrigger($trigger) {
        $summon_name     = array_get($trigger, 'summon.name');
        $summon_quantity = array_get($trigger, 'summon.quantity');

        if (is_null($summon_name)) {
            return;
        }

        /** @var Player $target */
        for ($i = 0; $i < $summon_quantity; $i++) {
            $tmp_minion = app('Minion', [$this->card->getOwner()]);
            $tmp_minion->load($summon_name);
            $this->card->getOwner()->play($tmp_minion);
        }
    }

    /**
     * @param $trigger
     * @param $targets
     * @throws DumbassDeveloperException
     */
    private function resolveDiscardTrigger($trigger, $targets) {
        $discard = array_get($trigger, 'discard');

        if (is_null($discard)) {
            return;
        }

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

    /**
     * @param $trigger
     * @param Minion[] $targets
     * @throws DumbassDeveloperException
     */
    private function resolveEnchantmentTrigger($trigger, $targets) {
        $enchantment = array_get($trigger, 'enchantment');
        if (is_null($enchantment)) {
            return;
        }

        foreach ($targets as $target) {
            $target->setMechanics(array_get($enchantment, 'mechanics', []));

            $delta_attack = array_get($enchantment, 'attack', 0);
            $target->setAttack($target->getAttack() + $delta_attack);

            $delta_health = array_get($enchantment, 'health', 0);
            $target->setMaxHealth($target->getHealth() + $delta_health);
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
        }
    }

    /**
     * @param $trigger
     * @param Minion[] $targets
     * @throws DumbassDeveloperException
     */
    private function resolveSpellTrigger($trigger, $targets) {
        $spell = array_get($trigger, 'spell');

        if (is_null($spell)) {
            return;
        }

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

    /**
     * @param $trigger
     * @param Minion[] $targets
     */
    private function resolveSilenceTrigger($trigger, $targets) {
        $silence = array_get($trigger, 'silence');

        if (is_null($silence)) {
            return;
        }

        foreach ($targets as $target) {
            $target->removeAllMechanics();
        }
    }

    /**
     * @param $trigger
     * @param $targets
     */
    private function resolveDestroyTrigger($trigger, $targets) {
        $destroy = array_get($trigger, 'destroy');

        if (is_null($destroy)) {
            return;
        }

        /** @var AbstractHero $target */
        foreach ($targets as $target) {
            // todo may need to have other types of things to destroy.
            $target->destroyWeapon();
        }
    }

    /**
     * @param $trigger
     * @param $targets
     * @throws InvalidTargetException
     */
    private function validateRace($trigger, $targets) {
        $required_race = array_get($trigger, 'targets.race');

        if (!$required_race) {
            return;
        }

        /** @var Minion $target */
        foreach ($targets as $target) {
            if (strtolower($target->getRace()) != strtolower($required_race)) {
                throw new InvalidTargetException('Target must be a ' . $required_race . ' ' . $target->getRace() . ' given');
            }
        }
    }

    public function recalculateAura() {
        $trigger = array_get($this->card->getTrigger(), $this->phase_name);

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
    }

    /**
     * @param Minion $card
     */
    public function setCard($card) {
        $this->card = $card;
    }

    /**
     * @param mixed $targets
     */
    public function setTargets($targets) {
        $this->targets = $targets;
    }

    /**
     * @param mixed $phase_name
     */
    public function setPhaseName($phase_name) {
        $this->phase_name = $phase_name;
    }

}
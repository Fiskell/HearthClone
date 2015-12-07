<?php namespace App\Game\Cards\TargetTypes;

use App\Game\Cards\Card;
use App\Game\Cards\Minion;

class BoardTargetGroups
{
    private $player;
    private $opponent;
    private $opponent_minions;
    private $opponent_minions_with_hero;
    private $player_minions;
    private $player_minions_with_hero;
    private $all_minions_with_heroes;
    private $provided_targets;
    private $alive_provided_targets;

    /**
     * @return mixed
     */
    public function getPlayer() {
        return $this->player;
    }

    /**
     * @return mixed
     */
    public function getOpponent() {
        return $this->opponent;
    }

    /**
     * @return mixed
     */
    public function getOpponentMinions() {
        return $this->opponent_minions;
    }

    /**
     * @return mixed
     */
    public function getOpponentMinionsWithHero() {
        return $this->opponent_minions_with_hero;
    }

    /**
     * @return mixed
     */
    public function getPlayerMinions() {
        return $this->player_minions;
    }

    /**
     * @return mixed
     */
    public function getPlayerMinionsWithHero() {
        return $this->player_minions_with_hero;
    }

    /**
     * @return mixed
     */
    public function getAllMinionsWithHeroes() {
        return $this->all_minions_with_heroes;
    }

    /**
     * @return mixed
     */
    public function getProvidedTargets() {
        return $this->provided_targets;
    }

    /**
     * @return mixed
     */
    public function getAliveProvidedTargets() {
        return $this->alive_provided_targets;
    }

    public function setTriggerCard(Card $card) {
        $this->player   = $card->getOwner();
        $this->opponent = $this->player->getOtherPlayer();

        $this->opponent_minions           = $this->opponent->getMinionsInPlay();
        $this->opponent_minions_with_hero = $this->opponent_minions;

        $this->player_minions           = $this->player->getMinionsInPlay();
        $this->player_minions_with_hero = $this->player_minions;

        $this->opponent_minions_with_hero[$this->player->getHero()->getId()]       = $this->opponent->getHero();
        $this->player_minions_with_hero[$this->player->getHero()->getId()] = $this->player->getHero();

        $this->all_minions_with_heroes = $this->opponent_minions_with_hero + $this->player_minions_with_hero;

        $this->player_minions   = self::removeDeadMinions($this->player_minions);
        $this->opponent_minions = self::removeDeadMinions($this->opponent_minions);
    }

    public function setProvidedTargets($provided_targets = null) {
        $this->provided_targets = [];
        if (!is_null($provided_targets)) {
            $this->provided_targets = $provided_targets;
        }
    }

    /**
     * Minions being targeted are not allowed to be dead.
     *
     * @param array $targets
     * @return array
     */
    public static function removeDeadMinions(array $targets) {
        /** @var Minion $target */
        foreach ($targets as $index => $target) {
            if (!$target->isAlive()) {
                unset($targets[$index]);
            }
        }

        return $targets;
    }
}
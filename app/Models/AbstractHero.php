<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/30/15
 * Time: 3:34 PM
 */

namespace App\Models;

abstract class AbstractHero
{
    protected $hero_class;

    protected $hero_power;

    protected $health = 30;

    /**
     * @return mixed
     */
    public function getHeroClass() {
        return $this->hero_class;
    }

    /**
     * @return mixed
     */
    public function getHeroPower() {
        return $this->hero_power;
    }

    /**
     * @param mixed $hero_power
     */
    public function setHeroPower($hero_power) {
        $this->hero_power = $hero_power;
    }

    /**
     * Hero takes damage
     * @param $damage
     */
    public function takeDamage($damage) {
        $this->health -= $damage;
    }

    /**
     * Use the heroes ability
     *
     * @param Player $active_player
     * @param Player $defending_player
     * @param array $targets
     */
    abstract function useAbility(Player $active_player, Player $defending_player, array $targets);

    /**
     * @return int
     */
    public function getHealth() {
        return $this->health;
    }

}
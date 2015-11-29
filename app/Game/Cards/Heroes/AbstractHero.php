<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/30/15
 * Time: 3:34 PM
 */

namespace App\Game\Cards\Heroes;

use App\Game\Cards\Minion;
use App\Game\Cards\Weapon;
use App\Game\Player;

abstract class AbstractHero extends Minion
{
    public static $MAX_LIFE = 30;

    protected $hero_class;

    protected $hero_power;

    protected $health;

    protected $armor = 0;

    protected $alive = true;

    protected $flipped = false;

    protected $play_order_id = 0;

    /** @var Weapon $weapon */
    protected $weapon;


    public function __construct(Player $player, $name = null) {
        parent::__construct($player, $name);
        $this->health = array_get($this->card_json, 'health');
    }

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
        if($this->armor >= $damage) {
            $this->armor -= $damage;
            return;
        }

        $damage -= $this->armor;
        $this->armor = 0;

        $this->health -= $damage;
        if($this->health <= 0) {
            $this->killed();
        }
    }

    /**
     * Use the heroes ability
     *
     * @param array $targets
     */
    abstract public function useAbility(array $targets);

    /**
     * @return int
     */
    public function getHealth() {
        return $this->health;
    }

    public function isAlive() {
        return $this->alive;
    }

    public function killed() {
        $this->alive = false;
    }

    public function resetHeroPower() {
        $this->flipped = false;
    }

    public function flipHeroPower() {
        $this->flipped = true;
    }

    /**
     * @return boolean
     */
    public function powerIsFlipped() {
        return $this->flipped;
    }

    public function getArmor() {
        return $this->armor;
    }

    /**
     * @param $armor_gained
     */
    public function gainArmor($armor_gained) {
        $this->armor += $armor_gained;
    }

    /**
     * Equip a weapon to the hero.
     *
     * @param Weapon $weapon
     */
    public function equipWeapon(Weapon $weapon) {
        $weapon->setHero($this);
        $this->weapon = $weapon;
    }

    /**
     * Remove the weapon from the hero.
     */
    public function destroyWeapon() {
        $this->weapon = null;
    }

    /**
     * @return Weapon
     */
    public function getWeapon() {
        return $this->weapon;
    }

}
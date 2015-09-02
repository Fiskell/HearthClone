<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/1/15
 * Time: 11:12 PM
 */

namespace App\Models;

class Weapon extends Card
{
    public $attack;
    public $durability;

    public function load($name = null) {
        parent::load($name);
        $this->attack     = array_get($this->card_json, 'attack', 0);
        $this->durability = array_get($this->card_json, 'durability', 0);
    }

    /**
     * @return mixed
     */
    public function getAttack() {
        return $this->attack;
    }

    /**
     * @param mixed $attack
     */
    public function setAttack($attack) {
        $this->attack = $attack;
    }

    /**
     * @return mixed
     */
    public function getDurability() {
        return $this->durability;
    }

    /**
     * Decrement durability by one.
     */
    public function decrementDurability() {
        $this->durability--;
    }

}
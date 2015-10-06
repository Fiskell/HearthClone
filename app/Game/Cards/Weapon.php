<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/1/15
 * Time: 11:12 PM
 */

namespace App\Game\Cards;

use App\Game\Cards\Heroes\AbstractHero;
use App\Game\Player;

class Weapon extends Card
{
    public $attack;
    public $durability;

    /** @var AbstractHero $hero */
    public $hero;

    public function __construct(Player $player, $name = null) {
        parent::__construct($player, $name);
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

    /**
     * @param $durability
     */
    public function setDurability($durability) {
        $this->durability = $durability;
    }

    /**
     * @param $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return AbstractHero
     */
    public function getHero() {
        return $this->hero;
    }

    /**
     * @param AbstractHero $hero
     */
    public function setHero($hero) {
        $this->hero = $hero;
    }

}
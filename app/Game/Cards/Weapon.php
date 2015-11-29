<?php namespace App\Game\Cards;

use App\Game\Cards\Heroes\AbstractHero;
use App\Game\Interfaces\ExportableInterface;
use App\Game\Player;

class Weapon extends Card implements ExportableInterface
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

    /**
     * @return string
     */
    public function export() {
        $export = json_decode(parent::export(), true);

        $export_weapon_fields = [
            "attack"     => $this->attack,
            "durability" => $this->durability
        ];

        $fields = array_merge($export['Card'], $export_weapon_fields);

        return json_encode(['Weapon' => $fields]);
    }

}
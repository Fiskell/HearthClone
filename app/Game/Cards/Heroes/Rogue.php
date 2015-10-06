<?php namespace App\Game\Cards\Heroes;

use App\Game\Cards\Heroes\AbstractHero;
use App\Game\Cards\Weapon;
use App\Game\Player;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/30/15
 * Time: 3:36 PM
 */
class Rogue extends AbstractHero
{
    private $hero_weapon_name       = "Wicked Knife";
    private $hero_weapon_attack     = 1;
    private $hero_weapon_durability = 2;

    protected $name = "Valeera Sanguinar";

    public function __construct(Player $player) {
        parent::__construct($player, $this->name);
        $this->hero_class = HeroClass::$ROGUE;
        $this->hero_power = HeroPower::$ROGUE;
    }


    /**
     * Use the heroes ability
     *
     * @param array $targets
     */
    function useAbility(array $targets) {
        $active_player = $this->getOwner();
        /** @var Weapon $weapon */
        $weapon = app('Weapon', [$active_player, $this->hero_weapon_name]);
        $weapon->setDurability($this->hero_weapon_durability);
        $this->equipWeapon($weapon);
    }
}
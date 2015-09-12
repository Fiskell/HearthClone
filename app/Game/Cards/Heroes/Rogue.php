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
        parent::__construct($player);
        $this->hero_class = HeroClass::$ROGUE;
        $this->hero_power = HeroPower::$ROGUE;
    }

    /**
     * Use the heroes ability
     *
     * @param Player $active_player
     * @param Player $defending_player
     * @param array $targets
     */
    function useAbility(Player $active_player, Player $defending_player, array $targets) {
        /** @var Weapon $weapon */
        $weapon = app('Weapon', [$active_player]);
        $weapon->setName($this->hero_weapon_name);
        $weapon->setAttack($this->hero_weapon_attack);
        $weapon->setDurability($this->hero_weapon_durability);
        $this->equipWeapon($weapon);
    }
}
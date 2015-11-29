<?php namespace App\Game\Cards\Heroes;

use App\Game\Cards\Weapon;
use App\Game\Player;

class Rogue extends AbstractHero
{
    private $hero_weapon_name = "Wicked Knife";

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
        $this->equipWeapon($weapon);
    }
}
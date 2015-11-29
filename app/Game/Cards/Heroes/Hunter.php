<?php namespace App\Game\Cards\Heroes;

use App\Game\Player;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/30/15
 * Time: 3:36 PM
 */
class Hunter extends AbstractHero
{
    protected $hero_damage = 2;
    protected $name        = "Rexxar";

    public function __construct(Player $player) {
        parent::__construct($player, $this->name);
        $this->hero_class = HeroClass::$HUNTER;
        $this->hero_power = HeroPower::$HUNTER;
    }

    /**
     * Use the heroes ability
     *
     * @param array $targets
     */
    public function useAbility(array $targets) {
        $defending_player = $this->getOwner()->getOtherPlayer();
        $defending_player->getHero()->takeDamage($this->hero_damage);
    }
}
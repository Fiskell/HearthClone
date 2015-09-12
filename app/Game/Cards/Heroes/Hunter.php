<?php namespace App\Game\Card\Heroes;
use App\Game\Cards\Heroes\AbstractHero;
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
        parent::__construct($player);
        $this->hero_class = HeroClass::$HUNTER;
        $this->hero_power = HeroPower::$HUNTER;
    }


    /**
     * Use the heroes ability
     *
     * @param Player $active_player
     * @param Player $defending_player
     * @param array $targets
     */
    function useAbility(Player $active_player, Player $defending_player, array $targets) {
        $defending_player->getHero()->takeDamage($this->hero_damage);
    }
}
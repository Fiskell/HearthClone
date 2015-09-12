<?php namespace App\Game\Cards\Heroes;

use App\Game\Player;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/30/15
 * Time: 3:36 PM
 */
class Warlock extends AbstractHero
{
    private $life_lost = 2;

    protected $name = "Gul'dan";

    public function __construct(Player $player) {
        parent::__construct($player);
        $this->hero_class = HeroClass::$WARLOCK;
        $this->hero_power = HeroPower::$WARLOCK;
    }

    /**
     * Use the heroes ability
     *
     * @param Player $active_player
     * @param Player $defending_player
     * @param array $targets
     */
    function useAbility(Player $active_player, Player $defending_player, array $targets) {
        $active_player->drawCard();
        $active_player->getHero()->takeDamage($this->life_lost);
    }
}
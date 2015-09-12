<?php namespace App\Game\Cards\Heroes;

use App\Game\Cards\Minion;
use App\Game\Player;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/30/15
 * Time: 3:36 PM
 */
class Warrior extends AbstractHero
{
    private $armor_gained = 2;

    protected $name = "Garrosh Hellscream";

    public function __construct(Player $player) {
        parent::__construct($player);
        $this->hero_class = HeroClass::$WARRIOR;
        $this->hero_power = HeroPower::$WARRIOR;
    }

    /**
     * Use the heroes ability
     *
     * @param array $targets
     */
    function useAbility(array $targets) {
        $active_player = $this->getOwner();
        $active_player->getHero()->gainArmor($this->armor_gained);
    }
}
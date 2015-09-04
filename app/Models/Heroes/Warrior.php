<?php namespace App\Models\Heroes;
use App\Models\AbstractHero;
use App\Models\HeroClass;
use App\Models\HeroPower;
use App\Models\Minion;
use App\Models\Player;

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

    public function __construct() {
        $this->hero_class = HeroClass::$WARRIOR;
        $this->hero_power = HeroPower::$WARRIOR;
    }

    /**
     * Use the heroes ability
     *
     * @param Player $active_player
     * @param Player $defending_player
     * @param Minion[] $targets
     */
    function useAbility(Player $active_player, Player $defending_player, array $targets) {
        $active_player->getHero()->gainArmor($this->armor_gained);
    }
}
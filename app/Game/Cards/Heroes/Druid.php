<?php namespace App\Game\Cards\Heroes;
use App\Game\Cards\Heroes\HeroClass;
use App\Game\Cards\Heroes\HeroPower;
use App\Game\Cards\Weapon;
use App\Game\Player;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/30/15
 * Time: 3:36 PM
 */
class Druid extends AbstractHero
{
    private $hero_power_armor  = 1;
    private $hero_power_attack = 1;

    protected $name = "Malfurion Stormrage";

    public function __construct(Player $player) {
        parent::__construct($player, $this->name);
        $this->hero_class = HeroClass::$DRUID;
        $this->hero_power = HeroPower::$DRUID;
    }

    /**
     * Use the heroes ability
     *
     * @param array $targets
     */
    function useAbility(array $targets) {
        $this->gainArmor($this->hero_power_armor);

        /** @var Weapon $weapon */
        $this->setAttack($this->hero_power_attack);
    }
}
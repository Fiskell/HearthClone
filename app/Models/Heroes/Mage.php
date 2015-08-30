<?php namespace App\Models\Heroes;
use App\Exceptions\InvalidTargetException;
use App\Models\AbstractHero;
use App\Models\Card;
use App\Models\HeroClass;
use App\Models\HeroPower;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/30/15
 * Time: 3:36 PM
 */
class Mage extends AbstractHero
{
    protected $hero_damage = 2;

    public function __construct() {
        $this->hero_class = HeroClass::$MAGE;
        $this->hero_power = HeroPower::$MAGE;
    }

    /**
     * Use the heroes ability
     *
     * @param Card[] $targets
     * @throws InvalidTargetException
     */
    function useAbility(array $targets) {
        if(count($targets) != 1) {
            throw new InvalidTargetException('Must select one target');
        }

        /** @var Card $target */
        $target = current($targets);
        $target->takeDamage($this->hero_damage);
    }
}
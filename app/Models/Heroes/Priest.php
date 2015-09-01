<?php namespace App\Models\Heroes;
use App\Exceptions\InvalidTargetException;
use App\Models\AbstractHero;
use App\Models\Card;
use App\Models\HeroClass;
use App\Models\HeroPower;
use App\Models\Player;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/30/15
 * Time: 3:36 PM
 */
class Priest extends AbstractHero
{
    protected $heal_value = 2;
    public function __construct() {
        $this->hero_class = HeroClass::$PRIEST;
        $this->hero_power = HeroPower::$PRIEST;
    }

    /**
     * Use the heroes ability
     *
     * @param Player $active_player
     * @param Player $defending_player
     * @param array $targets
     * @throws InvalidTargetException
     */
    public function useAbility(Player $active_player, Player $defending_player, array $targets) {
        if(count($targets) != 1) {
            throw new InvalidTargetException('Must select one target');
        }

        /** @var Card $target */
        $target = current($targets);
        $target->heal($this->heal_value);

        // todo hero needs to be a card so i can heal it
        // todo should not be able to heal past max modified health
    }
}
<?php namespace App\Models\Heroes;
use App\Exceptions\InvalidTargetException;
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
     * @param Minion[] $targets
     * @throws InvalidTargetException
     */
    public function useAbility(Player $active_player, Player $defending_player, array $targets) {
        if(count($targets) != 1) {
            throw new InvalidTargetException('Must select one target');
        }

        /** @var Minion $target */
        $target = current($targets);

        if($target instanceof AbstractHero) {
            $amount_to_heal = $this->heal_value;
            $heal_result = $target->getHealth() + $this->heal_value;

            if($heal_result > AbstractHero::$MAX_LIFE) {
                $amount_to_heal -= $heal_result - AbstractHero::$MAX_LIFE;
            }

            $target->heal($amount_to_heal);
            return;
        }

        $target->heal($this->heal_value);
    }
}
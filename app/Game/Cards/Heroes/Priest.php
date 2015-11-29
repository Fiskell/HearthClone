<?php namespace App\Game\Cards\Heroes;

use App\Exceptions\InvalidTargetException;
use App\Game\Cards\Minion;
use App\Game\Player;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/30/15
 * Time: 3:36 PM
 */
class Priest extends AbstractHero
{
    protected $heal_value = 2;
    protected $name = "Anduin Wrynn";

    public function __construct(Player $player) {
        parent::__construct($player, $this->name);
        $this->hero_class = HeroClass::$PRIEST;
        $this->hero_power = HeroPower::$PRIEST;
    }

    /**
     * Use the heroes ability
     *
     * @param array $targets
     * @throws InvalidTargetException
     */
    public function useAbility(array $targets) {
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
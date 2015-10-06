<?php namespace App\Game\Cards\Heroes;

use App\Game\Cards\Minion;
use App\Game\Player;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/30/15
 * Time: 3:36 PM
 */
class Paladin extends AbstractHero
{
    private $hero_power_minion_name = 'Silver Hand Recruit';

    protected $name = "Uther Lightbringer";

    public function __construct(Player $player) {
        parent::__construct($player, $this->name);
        $this->hero_class = HeroClass::$PALADIN;
        $this->hero_power = HeroPower::$PALADIN;
    }

    /**
     * Use the heroes ability
     *
     * @param array $targets
     */
    function useAbility(array $targets) {
        $active_player = $this->getOwner();
        /** @var Minion $card */
        $card = app('Minion', [$active_player, $this->hero_power_minion_name]);
        $active_player->play($card);
    }
}
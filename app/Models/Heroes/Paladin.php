<?php namespace App\Models\Heroes;
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
class Paladin extends AbstractHero
{
    private $hero_power_minion_name = 'Silver Hand Recruit';

    protected $name = "Uther Lightbringer";
    public function __construct() {
        $this->hero_class = HeroClass::$PALADIN;
        $this->hero_power = HeroPower::$PALADIN;
    }

    /**
     * Use the heroes ability
     *
     * @param Player $active_player
     * @param Player $defending_player
     * @param array $targets
     */
    function useAbility(Player $active_player, Player $defending_player, array $targets) {
        /** @var Card $card */
        $card = app('Minion');
        $card->load($this->hero_power_minion_name);

        $active_player->play($card);
    }
}
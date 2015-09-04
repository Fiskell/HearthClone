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
class Shaman extends AbstractHero
{
    protected $name = 'Thrall';

    private $totems = [
        'Healing Totem',
        'Searing Totem',
        'Stoneclaw Totem',
        'Wrath of Air Totem'
    ];

    public function __construct() {
        $this->hero_class = HeroClass::$SHAMAN;
        $this->hero_power = HeroPower::$SHAMAN;
    }

    /**
     * Use the heroes ability
     *
     * @param Player $active_player
     * @param Player $defending_player
     * @param array $targets
     */
    function useAbility(Player $active_player, Player $defending_player, array $targets) {
        /** @var Minion $card */
        $card = app('Minion');
        $card->load($this->totems[0]);

        $active_player->play($card);
    }

    /**
     * @return array
     */
    public function getTotems() {
        return $this->totems;
    }
}
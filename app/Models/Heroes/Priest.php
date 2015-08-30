<?php namespace App\Models\Heroes;
use App\Models\AbstractHero;
use App\Models\HeroClass;
use App\Models\HeroPower;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/30/15
 * Time: 3:36 PM
 */
class Priest extends AbstractHero
{
    public function __construct() {
        $this->hero_class = HeroClass::$PRIEST;
        $this->hero_power = HeroPower::$PRIEST;
    }
}
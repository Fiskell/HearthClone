<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/30/15
 * Time: 3:34 PM
 */

namespace App\Models;

abstract class AbstractHero
{
    protected $hero_class;

    protected $hero_power;

    /**
     * @return mixed
     */
    public function getHeroClass() {
        return $this->hero_class;
    }

    /**
     * @return mixed
     */
    public function getHeroPower() {
        return $this->hero_power;
    }

    /**
     * @param mixed $hero_power
     */
    public function setHeroPower($hero_power) {
        $this->hero_power = $hero_power;
    }

    /**
     * Use the heroes ability
     *
     * @param array $targets
     */
    abstract function useAbility(array $targets);

}
<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/30/15
 * Time: 3:16 PM
 */

namespace App\Models;

class Deck
{
    /** @var AbstractHero $hero */
    protected $hero;

    /** @var  Card[] $deck_list */
    protected $deck_list;

    public function __construct($hero, array $deck_list) {
        $this->hero = $hero;
        $this->deck_list = $deck_list;
    }

    /**
     * @return HeroClass
     */
    public function getHero() {
        return $this->hero;
    }

    /**
     * @param AbstractHero $hero
     */
    public function setHero(AbstractHero $hero) {
        $this->hero = $hero;
    }

    /**
     * @return Card[]
     */
    public function getDeckList() {
        return $this->deck_list;
    }

}
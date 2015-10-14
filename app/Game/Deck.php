<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/30/15
 * Time: 3:16 PM
 */

namespace App\Game;

use App\Game\Cards\Heroes\HeroClass;
use App\Game\Cards\Card;
use App\Game\Cards\Heroes\AbstractHero;

class Deck
{
    /** @var AbstractHero $hero */
    protected $hero;

    /** @var  Card[] $deck_list */
    protected $deck_list;

    /** @var  int $remaining_count */
    protected $remaining_count = 30;

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

    /**
     * @return int
     */
    public function getRemainingCount() {
        return $this->remaining_count;
    }

    public function draw() {
        $this->remaining_count--;
    }

}
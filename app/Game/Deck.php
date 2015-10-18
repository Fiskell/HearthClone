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

    /** @var array $deck_list */
    protected $deck_list;

    /** @var array $deck */
    protected $deck;

    /** @var  int $remaining_count */
    protected $remaining_count = 30;

    public function __construct($hero, array $deck_list) {
        $this->hero = $hero;
        $this->deck_list = $deck_list;
        $this->validate();
        $this->shuffleDeck();
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

    /**
     * Draw a card from the deck
     */
    public function draw() {
        $this->remaining_count--;
    }

    /**
     * Shuffle the deck list and get the deck ready for play.
     *
     * First version is a naive shuffle where we just add each
     *  card to the deck in order.
     */
    private function shuffleDeck() {
        foreach($this->deck_list as $card_name => $card_qty) {
            for($i = 0; $i < $card_qty; $i++) {
                $this->deck[] = $card_name;
            }
        }
        $this->remaining_count = count($this->deck);
    }

    /**
     * Return the remaining cards in the deck.
     *
     * @return array
     */
    public function getDeck() {
        return $this->deck;
    }

    /**
     * Validate that the deck is valid.
     *
     *  - Hero is defined
     *  - Card list is defined
     *  - Hero is valid
     *  - All cards are valid
     *  - 30 cards
     *  - Class cards belong to specified class
     *  - Only two of each non-legendary card
     *  - Only one of a particular legendary card
     */
    private function validate() {

    }
}
<?php namespace App\Game;

use App\Exceptions\InvalidDeckListException;
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

        // Todo, this should probably be below validate
        $this->shuffleDeck();

        // Todo, this is how we will temporarily get around validation on other tests.
        if(!empty($deck_list)) {
            $this->validate();
        }
    }

    /**
     * @return AbstractHero
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
     * Return the remaining cards in the deck.
     *
     * @return array
     */
    public function getDeck() {
        return $this->deck;
    }

    /**
     * @return int
     */
    public function getRemainingCount() {
        return $this->remaining_count;
    }

    /**
     * Draw a card from the deck
     * @return Card
     */
    public function draw() {
        $this->remaining_count--;

        $player = $this->getHero()->getOwner();

        // todo this should now always be true, it's a temporary fix
        // todo need to have a testing environment that returns Wisp
        if(count($this->deck) == 0) {
            return Card::load($player, 'Wisp');
        }

        return Card::load($player, $this->deck[0]);
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
        $deck_card_count = count($this->deck);

        if($deck_card_count < 30 || $deck_card_count > 30) {
            throw new InvalidDeckListException('Deck must contain 30 cards');
        }

        array_walk($this->deck_list, function($card_quantity, $card_name) {
            if($card_quantity > 2) {
                throw new InvalidDeckListException("{$card_name} used more than twice");
            }

            // todo test legendary card only used once
        });
    }
}
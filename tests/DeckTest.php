<?php namespace tests;

use App\Game\Cards\Card;
use App\Game\Cards\Heroes\HeroClass;
use App\Game\Deck;
use App\Models\HearthCloneTest;

class DeckTest extends HearthCloneTest
{
    public function test_draw_card_removes_one_card_from_deck() {
        $deck_card_count = $this->game->getPlayer1()->getDeck()->getRemainingCount();
        $this->game->getPlayer1()->drawCard();
        $this->assertEquals(($deck_card_count - 1), $this->game->getPlayer1()->getDeck()->getRemainingCount());
    }

    public function test_single_deck_load() {
        $hunter_deck_json = file_get_contents(base_path() . "/resources/deck_lists/basic_only_hunter.json");
        $hero = app(HeroClass::$HUNTER, [$this->game->getPlayer1()]);
        $cards = array_get(json_decode($hunter_deck_json, true), 'Cards', []);

        /** @var Deck $player1_deck */
        $player1_deck = app('Deck', [$hero, $cards]);
        $deck = $player1_deck->getDeck();
        $this->assertEquals(30, count($deck));
    }

    /** @expectedException \App\Exceptions\InvalidDeckListException */
    public function test_deck_can_not_have_less_than_30_cards() {
        $hunter_deck_json = file_get_contents(base_path() . "/resources/deck_lists/test_validation/less_than_thirty.json");
        $hero = app(HeroClass::$HUNTER, [$this->game->getPlayer1()]);
        $cards = array_get(json_decode($hunter_deck_json, true), 'Cards', []);

        app('Deck', [$hero, $cards]);
    }

    /** @expectedException \App\Exceptions\InvalidDeckListException */
    public function test_deck_can_not_have_more_than_30_cards() {
        $hunter_deck_json = file_get_contents(base_path() . "/resources/deck_lists/test_validation/more_than_thirty.json");
        $hero = app(HeroClass::$HUNTER, [$this->game->getPlayer1()]);
        $cards = array_get(json_decode($hunter_deck_json, true), 'Cards', []);

        app('Deck', [$hero, $cards]);
    }

    /** @expectedException \App\Exceptions\InvalidDeckListException */
    public function test_deck_can_not_have_more_than_two_copies_of_one_card() {
        $hunter_deck_json = file_get_contents(base_path() . "/resources/deck_lists/test_validation/more_than_two_copies.json");
        $hero = app(HeroClass::$HUNTER, [$this->game->getPlayer1()]);
        $cards = array_get(json_decode($hunter_deck_json, true), 'Cards', []);

        app('Deck', [$hero, $cards]);
    }

    public function test_drawing_card_adds_card_objecto_to_hand() {
        $hunter_deck_json = file_get_contents(base_path() . "/resources/deck_lists/basic_only_hunter.json");
        $hero = app(HeroClass::$HUNTER, [$this->game->getPlayer1()]);
        $cards = array_get(json_decode($hunter_deck_json, true), 'Cards', []);
        app('Deck', [$hero, $cards]);

        $this->assertEquals(0, count($this->game->getPlayer1()->getHand()));
        $this->game->getPlayer1()->drawCard();
        $hand = $this->game->getPlayer1()->getHand();
        $this->assertEquals(1, count($hand));
        $this->assertTrue($hand[0] instanceof Card);
    }

}
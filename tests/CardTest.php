<?php

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/23/15
 * Time: 3:01 PM
 */
class CardTest extends TestCase
{
    public $card;

    public function setUp() {
        parent::setUp();
        $this->card = $this->app->make('Card');
    }

    /** @expectedException \App\Exceptions\MissingCardHandleException */
    public function test_card_load_throws_when_no_card_name_specified() {
        $this->card->load();
    }

    /** @expectedException \App\Exceptions\UnknownCardHandleException */
    public function test_card_load_throws_when_unknown_handle_is_given() {
        $this->card->load('NOT_A_REAL_CARD_HANDLE');
    }

    public function test_card_handle_is_set_when_handle_passed_into_load() {
        $card_name = 'argent-squire';
        $this->card->load($card_name);
        $this->assertTrue($this->card->getHandle() == $card_name);
    }

    public function test_card_attack_is_set_on_load_argent_squire() {
        $card_name = 'argent-squire';
        $this->card->load($card_name);
        $this->assertTrue($this->card->getAttack() == 1);
    }

    public function test_card_defense_is_set_on_load_argent_squire() {
        $card_name = 'argent-squire';
        $this->card->load($card_name);
        $this->assertTrue($this->card->getDefense() == 1);
    }

    public function test_card_attack_is_set_on_load_knife_juggler() {
        $card_name = 'knife-juggler';
        $this->card->load($card_name);
        $this->assertTrue($this->card->getAttack() == 3);
    }

    public function test_card_defense_is_set_on_load_knife_juggler() {
        $card_name = 'knife-juggler';
        $this->card->load($card_name);
        $this->assertTrue($this->card->getDefense() == 2);
    }


}
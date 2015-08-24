<?php
use App\Models\Card;
use App\Models\CardType;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/23/15
 * Time: 3:01 PM
 */
class CardTest extends TestCase
{
    /** @var  Card $card */
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

    public function test_argent_squire_is_a_creature() {
        $card_name = 'argent-squire';
        $this->card->load($card_name);
        $this->assertTrue($this->card->getType() == CardType::$CREATURE);
    }

    public function test_consecrate_is_a_spell() {
        $card_name = 'consecrate';
        $this->card->load($card_name);
        $this->assertTrue($this->card->getType() == CardType::$SPELL);
    }

    public function test_knife_juggler_attack_kills_argent_squire_without_divine_shield() {
        $argent_squire = $this->app->make('Card');
        $argent_squire->load('argent-squire');

        $knife_juggler = $this->app->make('Card');
        $knife_juggler->load('knife-juggler');

        $knife_juggler->attack($argent_squire);
        $this->assertTrue($argent_squire->isAlive() == false);
    }

    public function test_knife_juggler_defense_is_1_after_attacking_argent_squire() {
        /** @var Card $argent_squire */
        $argent_squire = $this->app->make('Card');
        $argent_squire->load('argent-squire');

        /** @var Card $knife_juggler */
        $knife_juggler = $this->app->make('Card');
        $knife_juggler->load('knife-juggler');

        $knife_juggler->attack($argent_squire);
        $this->assertTrue($knife_juggler->getDefense() == 1);
    }

}
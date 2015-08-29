<?php
use App\Models\Card;
use App\Models\CardType;
use App\Models\Game;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/23/15
 * Time: 3:01 PM
 */
class CardTest extends TestCase
{

    /**
     * Creatures
     */
    public $argent_squire_handle = 'argent-squire';
    public $knife_juggler_handle = 'knife-juggler';
    public $dread_corsair_handle = 'Dread Corsair';

    /**
     * Spells
     */
    public $consecrate_handle = 'Consecrate';

    /** @var  Card $card */
    public $card;

    /** @var  Game $game */
    public $game;

    public function setUp() {
        parent::setUp();
        $this->card = app('Card');
        $this->game = app('Game');
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
        $this->card->load($this->argent_squire_handle);
        $this->assertTrue($this->card->getHandle() == $this->argent_squire_handle);
    }

    public function test_card_attack_is_set_on_load_argent_squire() {
        $this->card->load($this->argent_squire_handle);
        $this->assertTrue($this->card->getAttack() == 1);
    }

    public function test_card_defense_is_set_on_load_argent_squire() {
        $this->card->load($this->argent_squire_handle);
        $this->assertTrue($this->card->getDefense() == 1);
    }

    public function test_card_attack_is_set_on_load_knife_juggler() {
        $this->card->load($this->knife_juggler_handle);
        $this->assertTrue($this->card->getAttack() == 3);
    }

    public function test_card_defense_is_set_on_load_knife_juggler() {
        $this->card->load($this->knife_juggler_handle);
        $this->assertTrue($this->card->getDefense() == 2);
    }

    public function test_argent_squire_is_a_creature() {
        $this->card->load($this->argent_squire_handle);
        $this->assertTrue($this->card->getType() == CardType::$CREATURE);
    }

    public function test_consecrate_is_a_spell() {
        $this->card->load($this->consecrate_handle);
        $this->assertTrue($this->card->getType() == CardType::$SPELL);
    }

    public function test_knife_juggler_attack_kills_argent_squire_without_divine_shield() {
        $argent_squire = app('Card');
        $argent_squire->load($this->argent_squire_handle);
        $this->game->getPlayer1()->play($argent_squire);

        $knife_juggler = app('Card');
        $knife_juggler->load($this->knife_juggler_handle);
        $this->game->getPlayer2()->play($knife_juggler);

        $knife_juggler->attack($argent_squire);
        $this->assertTrue($argent_squire->getDefense() == 0);
        $this->assertTrue($argent_squire->isAlive() == false);
    }

    public function test_knife_juggler_defense_is_1_after_attacking_argent_squire() {
        /** @var Card $argent_squire */
        $argent_squire = app('Card');
        $argent_squire->load($this->argent_squire_handle);
        $this->game->getPlayer1()->play($argent_squire);

        /** @var Card $knife_juggler */
        $knife_juggler = app('Card');
        $knife_juggler->load($this->knife_juggler_handle);
        $this->game->getPlayer2()->play($knife_juggler);

        $knife_juggler->attack($argent_squire);
        $this->assertTrue($knife_juggler->getDefense() == 1);
    }

    /** @expectedException \App\Exceptions\InvalidTargetException */
    public function test_knife_juggler_cannot_attack_argent_squire_when_dread_corsair_is_on_the_field() {
         /** @var Card $argent_squire */
        $argent_squire = app('Card');
        $argent_squire->load($this->argent_squire_handle);
        $this->game->getPlayer1()->play($argent_squire);

        /** @var Card $dread_corsair */
        $dread_corsair = app('Card');
        $dread_corsair->load($this->dread_corsair_handle);
        $this->game->getPlayer1()->play($dread_corsair);

        /** @var Card $knife_juggler */
        $knife_juggler = app('Card');
        $knife_juggler->load($this->knife_juggler_handle);
        $this->game->getPlayer2()->play($knife_juggler);

        $knife_juggler->attack($argent_squire);
    }

    public function test_knife_juggler_can_attack_argent_squire_after_dread_corsair_is_killed() {
         /** @var Card $argent_squire */
        $argent_squire = app('Card');
        $argent_squire->load($this->argent_squire_handle);
        $this->game->getPlayer1()->play($argent_squire);

        /** @var Card $dread_corsair */
        $dread_corsair = app('Card');
        $dread_corsair->load($this->dread_corsair_handle);
        $this->game->getPlayer1()->play($dread_corsair);

        /** @var Card $knife_juggler */
        $knife_juggler = app('Card');
        $knife_juggler->load($this->knife_juggler_handle);
        $this->game->getPlayer2()->play($knife_juggler);

        /** @var Card $knife_juggler */
        $knife_juggler2 = app('Card');
        $knife_juggler2->load($this->knife_juggler_handle);
        $this->game->getPlayer2()->play($knife_juggler2);


        $knife_juggler->attack($dread_corsair);
        $knife_juggler2->attack($argent_squire);
    }

}
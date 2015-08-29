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
    public $argent_squire_handle = 'Argent Squire';
    public $dread_corsair_handle = 'Dread Corsair';
    public $knife_juggler_handle = 'Knife Juggler';
    public $spellbreaker_handle = 'Spellbreaker';
    public $wisp_handle = 'Wisp';
    public $worgen_infiltrator_handle = 'Worgen Infiltrator';

    /**
     * Spells
     */
    public $consecrate_handle = 'Consecrate';

    /** @var  Card $card */
    public $card;

    /** @var  Game $game */
    public $game;

    public function setUp()
    {
        parent::setUp();
        $this->card = app('Card');
        $this->game = app('Game');
    }

    /** @expectedException \App\Exceptions\MissingCardHandleException */
    public function test_card_load_throws_when_no_card_name_specified()
    {
        $this->card->load();
    }

    /** @expectedException \App\Exceptions\UnknownCardHandleException */
    public function test_card_load_throws_when_unknown_handle_is_given()
    {
        $this->card->load('NOT_A_REAL_CARD_HANDLE');
    }

    public function test_card_handle_is_set_when_handle_passed_into_load()
    {
        $this->card->load($this->wisp_handle);
        $this->assertTrue($this->card->getHandle() == $this->wisp_handle);
    }

    public function test_card_attack_is_set_on_load_wisp()
    {
        $this->card->load($this->wisp_handle);
        $this->assertTrue($this->card->getAttack() == 1);
    }

    public function test_card_defense_is_set_on_load_wisp()
    {
        $this->card->load($this->wisp_handle);
        $this->assertTrue($this->card->getDefense() == 1);
    }

    public function test_card_attack_is_set_on_load_knife_juggler()
    {
        $this->card->load($this->knife_juggler_handle);
        $this->assertTrue($this->card->getAttack() == 3);
    }

    public function test_card_defense_is_set_on_load_knife_juggler()
    {
        $this->card->load($this->knife_juggler_handle);
        $this->assertTrue($this->card->getDefense() == 2);
    }

    public function test_wisp_is_a_creature()
    {
        $this->card->load($this->wisp_handle);
        $this->assertTrue($this->card->getType() == CardType::$CREATURE);
    }

    public function test_consecrate_is_a_spell()
    {
        $this->card->load($this->consecrate_handle);
        $this->assertTrue($this->card->getType() == CardType::$SPELL);
    }

    public function test_knife_juggler_attack_kills_wisp_without_divine_shield()
    {
        $wisp = app('Card');
        $wisp->load($this->wisp_handle);
        $this->game->getPlayer1()->play($wisp);

        $knife_juggler = app('Card');
        $knife_juggler->load($this->knife_juggler_handle);
        $this->game->getPlayer2()->play($knife_juggler);

        $knife_juggler->attack($wisp);
        $this->assertTrue($wisp->getDefense() == 0);
        $this->assertTrue($wisp->isAlive() == false);
    }

    public function test_knife_juggler_defense_is_1_after_attacking_wisp()
    {
        /** @var Card $wisp */
        $wisp = app('Card');
        $wisp->load($this->wisp_handle);
        $this->game->getPlayer1()->play($wisp);

        /** @var Card $knife_juggler */
        $knife_juggler = app('Card');
        $knife_juggler->load($this->knife_juggler_handle);
        $this->game->getPlayer2()->play($knife_juggler);

        $knife_juggler->attack($wisp);
        $this->assertTrue($knife_juggler->getDefense() == 1);
    }

    /** @expectedException \App\Exceptions\InvalidTargetException */
    public function test_knife_juggler_cannot_attack_wisp_when_dread_corsair_is_on_the_field()
    {
        /** @var Card $wisp */
        $wisp = app('Card');
        $wisp->load($this->wisp_handle);
        $this->game->getPlayer1()->play($wisp);

        /** @var Card $dread_corsair */
        $dread_corsair = app('Card');
        $dread_corsair->load($this->dread_corsair_handle);
        $this->game->getPlayer1()->play($dread_corsair);

        /** @var Card $knife_juggler */
        $knife_juggler = app('Card');
        $knife_juggler->load($this->knife_juggler_handle);
        $this->game->getPlayer2()->play($knife_juggler);

        $knife_juggler->attack($wisp);
    }

    public function test_knife_juggler_can_attack_wisp_after_dread_corsair_is_killed()
    {
        /** @var Card $wisp */
        $wisp = app('Card');
        $wisp->load($this->wisp_handle);
        $this->game->getPlayer1()->play($wisp);

        /** @var Card $dread_corsair */
        $dread_corsair = app('Card');
        $dread_corsair->load($this->dread_corsair_handle);
        $this->game->getPlayer1()->play($dread_corsair);

        /** @var Card $knife_juggler */
        $knife_juggler = app('Card');
        $knife_juggler->load($this->knife_juggler_handle);
        $this->game->getPlayer2()->play($knife_juggler);

        /** @var Card $knife_juggler2 */
        $knife_juggler2 = app('Card');
        $knife_juggler2->load($this->knife_juggler_handle);
        $this->game->getPlayer2()->play($knife_juggler2);


        $knife_juggler->attack($dread_corsair);
        $knife_juggler2->attack($wisp);
    }

    public function test_wisp_is_added_to_player_1_graveyard()
    {
        /** @var Card $wisp */
        $wisp = app('Card');
        $wisp->load($this->wisp_handle);
        $this->game->getPlayer1()->play($wisp);

        /** @var Card $knife_juggler */
        $knife_juggler = app('Card');
        $knife_juggler->load($this->knife_juggler_handle);
        $this->game->getPlayer2()->play($knife_juggler);

        $knife_juggler->attack($wisp);
        $player1_graveyard = $this->game->getPlayer1()->getGraveyard();

        /** @var Card $first_dead_card */
        $first_dead_card = array_get($player1_graveyard, '0');

        $this->assertTrue($first_dead_card->getHandle() === $this->wisp_handle);
    }

    public function test_argent_squire_does_not_die_from_attack_if_divine_shield_active()
    {
        /** @var Card $argent_squire */
        $argent_squire = app('Card');
        $argent_squire->load($this->argent_squire_handle);
        $this->game->getPlayer1()->play($argent_squire);

        /** @var Card $knife_juggler */
        $knife_juggler = app('Card');
        $knife_juggler->load($this->knife_juggler_handle);
        $this->game->getPlayer2()->play($knife_juggler);

        $knife_juggler->attack($argent_squire);

        $this->assertTrue($argent_squire->isAlive());
    }

    public function test_argent_squire_dies_from_attack_after_being_silenced()
    {
        /** @var Card $argent_squire */
        $argent_squire = app('Card');
        $argent_squire->load($this->argent_squire_handle);
        $this->game->getPlayer1()->play($argent_squire);

        /** @var Card $spellbreaker */
        $spellbreaker = app('Card');
        $spellbreaker->load($this->spellbreaker_handle);
        $this->game->getPlayer2()->play($spellbreaker, [$argent_squire]);

        /** @var Card $knife_juggler */
        $knife_juggler = app('Card');
        $knife_juggler->load($this->knife_juggler_handle);
        $this->game->getPlayer2()->play($knife_juggler);

        $knife_juggler->attack($argent_squire);

        $this->assertTrue(!$argent_squire->isAlive());
    }

    /** @expectedException \App\Exceptions\InvalidTargetException */
    public function test_attacking_stealth_worgen_infiltrator_throws() {

        /** @var Card $worgen_infiltrator */
        $worgen_infiltrator = app('Card');
        $worgen_infiltrator->load($this->worgen_infiltrator_handle);
        $this->game->getPlayer1()->play($worgen_infiltrator);

        /** @var Card $knife_juggler */
        $knife_juggler = app('Card');
        $knife_juggler->load($this->knife_juggler_handle);
        $this->game->getPlayer2()->play($knife_juggler);

        $knife_juggler->attack($worgen_infiltrator);
    }

}
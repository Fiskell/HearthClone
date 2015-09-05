<?php
use App\Models\Game;
use App\Models\HearthCloneTest;
use App\Models\HeroClass;
use App\Models\Heroes\Hunter;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/23/15
 * Time: 9:45 PM
 */
class GameTest extends HearthCloneTest
{
    public function setUp() {
        parent::setUp();
        $this->game = $this->app->make('Game');
    }

    public function test_game_initializes_with_two_players() {
        $player1 = $this->game->getPlayer1();
        $player2 = $this->game->getPlayer2();
        $this->assertTrue(!!$player1 && !!$player2);
    }

    public function test_game_initialization_chooses_turn_order() {
        $active_player = $this->game->getActivePlayer();

        $this->assertNotNull($active_player);
    }

    public function test_active_player_switches_when_turn_is_passed() {
        $active_player = $this->game->getActivePlayer();

        $expected_active_player = $this->game->getPlayer1();
        if($active_player->getPlayerId() == 1) {
            $expected_active_player = $this->game->getPlayer2();
        }

        $active_player->passTurn();

        $new_active_player = $this->game->getActivePlayer();

        $this->assertTrue($expected_active_player->getPlayerId() == $new_active_player->getPlayerId());
    }

    public function test_game_is_initialized_with_a_hunter_and_a_mage() {
        $player1_deck = app('Deck', [app('Hunter', [$this->game->getPlayer1()]), []]);
        $player2_deck = app('Deck', [app('Mage', [$this->game->getPlayer2()]), []]);

        $this->game->init($player1_deck, $player2_deck);

        $player1_hero = $this->game->getPlayer1()->getHero();
        $player2_hero = $this->game->getPlayer2()->getHero();

        $this->assertEquals(HeroClass::$HUNTER, $player1_hero->getHeroClass());
        $this->assertEquals(HeroClass::$MAGE, $player2_hero->getHeroClass());
    }
}
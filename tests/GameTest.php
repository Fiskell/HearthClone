<?php
use App\Models\Game;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/23/15
 * Time: 9:45 PM
 */
class GameTest extends TestCase
{
    /** @var  Game $game */
    protected $game;
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
}
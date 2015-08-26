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
}
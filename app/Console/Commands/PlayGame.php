<?php

namespace App\Console\Commands;

use App\Game\Cards\Heroes\HeroClass;
use App\Game\Deck;
use Illuminate\Console\Command;

class PlayGame extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'play:game';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Play a game of Hearthstone!';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $game = app('Game');

        $player1 = $game->getPlayer1();
        $player2 = $game->getPlayer2();

        $deck1 = $this->getPlayer1Deck($player1);
        $deck2 = $this->getPlayer2Deck($player2);

        $game->init($deck1, $deck2);

        $this->waitForUserAction();
    }

    public function waitForUserAction() {
        $action = $this->ask('hey there');
    }

    /**
     * @param $player
     * @return Deck
     */
    public function getPlayer1Deck($player) {
        $hunter_deck_json = file_get_contents(base_path() . "/resources/deck_lists/basic_only_hunter.json");
        $hero = app(HeroClass::$HUNTER, [$player]);
        $cards = array_get(json_decode($hunter_deck_json, true), 'Cards', []);

        return app('Deck', [$hero, $cards]);
    }

    /**
     * @param $player
     * @return Deck
     */
    public function getPlayer2Deck($player) {
        return $this->getPlayer1Deck($player);
    }
}

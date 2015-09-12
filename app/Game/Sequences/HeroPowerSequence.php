<?php namespace App\Game\Sequences;

use App\Exceptions\HeroPowerAlreadyFlippedException;
use App\Game\Cards\Heroes\AbstractHero;
use App\Game\Game;
use App\Game\Player;

class HeroPowerSequence extends AbstractSequence
{
    /** @var Player $player */
    private $player;

    /** @var  AbstractHero $hero */
    private $hero;

    /** @var Game $game */
    private $game;

    public function resolve(AbstractHero $hero, $targets = []) {
        $this->hero   = $hero;
        $this->player = $hero->getOwner();
        $this->game   = $this->player->getGame();

        $this->flipHeroPower();
        $this->resolveHeroPower($targets);
        $this->game->resolveDeaths();
        $this->game->checkForGameOver();
    }

    /**
     * @param $targets
     */
    private function resolveHeroPower($targets) {
        $defending_player = $this->player->getOtherPlayer();

        $this->hero->useAbility($targets);

        if (!$defending_player->getHero()->isAlive()) {
            $defending_player->killed();
        }

        if (!$this->hero->isAlive()) {
            $this->player->killed();
        }
    }

    /**
     * Phase which flips the hero power so it cannot be used again
     * @throws HeroPowerAlreadyFlippedException
     */
    private function flipHeroPower() {
        if ($this->hero->powerIsFlipped()) {
            throw new HeroPowerAlreadyFlippedException('You have already used your ability this turn');
        }

        $this->hero->flipHeroPower();
    }

}
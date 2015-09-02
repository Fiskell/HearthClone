<?php namespace App\Models;
use TestCase;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/30/15
 * Time: 8:09 PM
 */
class HearthCloneTest extends TestCase
{

    /**
     * Minions
     */
    public $argent_squire_name       = 'Argent Squire';
    public $amani_berserker_name     = 'Amani Berserker';
    public $bluegill_warrior_name    = 'Bluegill Warrior';
    public $chillwind_yeti_name      = 'Chillwind Yeti';
    public $dread_corsair_name       = 'Dread Corsair';
    public $earth_elemental_name     = 'Earth Elemental';
    public $keeper_of_the_grove_name = 'Keeper of the Grove';
    public $knife_juggler_name       = 'Knife Juggler';
    public $loot_hoarder_name        = 'Loot Hoarder';
    public $ogre_magi_name           = 'Ogre Magi';
    public $si7_agent                = 'SI:7 Agent';
    public $silver_hand_recruit_name = 'Silver Hand Recruit';
    public $spellbreaker_name        = 'Spellbreaker';
    public $thrallmar_farseer_name   = 'Thrallmar Farseer';
    public $water_elemental_name     = 'Water Elemental';
    public $wisp_name                = 'Wisp';
    public $worgen_infiltrator_name  = 'Worgen Infiltrator';

    /**
     * Spells
     */
    public $consecrate_name = 'Consecration';

    /** @var  Minion $card */
    public $card;

    /** @var  Game $game */
    public $game;

    public function setUp() {
        parent::setUp();
        $this->game = app('Game');
        $this->initPlayers();
        $this->card = app('Minion');
    }

    /**
     * @param $name
     * @param int $player_id
     * @param array $targets
     * @param bool|false $summoning_sickness
     * @param null $choose_mechanic
     * @return Minion
     * @throws \App\Exceptions\MissingCardNameException
     * @throws \App\Exceptions\NotEnoughManaCrystalsException
     */
    public function playCard($name, $player_id = 1, $targets = [], $summoning_sickness = false, $choose_mechanic = null) {

        /** @var Card $card */
        $card = app('Minion');
        $card->load($name);

        $this->game->getPlayer1()->setManaCrystalCount(1000);
        $this->game->getPlayer2()->setManaCrystalCount(1000);

        /** @var Player $player */
        $player = $this->game->getPlayer1();
        if ($player_id == 2) {
            $player = $this->game->getPlayer2();
        }

        $player->play($card, $targets, $choose_mechanic);

        if (!$summoning_sickness) {
            $active_player = $this->game->getActivePlayer();
            $active_player->passTurn();

            $active_player = $this->game->getActivePlayer();
            $active_player->passTurn();
        }

        return $card;
    }

    public function playCardStrict($name, $player_id = 1, $turn = 1, $targets = [], $choose_mechanic = null) {
        /** @var Card $card */
        $card = app('Minion');
        $card->load($name);

        /** @var Player $player */
        $player = $this->game->getPlayer1();
        if ($player_id == 2) {
            $player = $this->game->getPlayer2();
        }

        if ($turn > 1) {
            $player_a = $this->game->getActivePlayer();
            $player_b = $this->game->getDefendingPlayer();

            for ($i = 1; $i <= ($turn - 1); $i++) {
                $player_a->passTurn();
                $player_b->passTurn();
            }
        }

        $player->play($card, $targets, $choose_mechanic);

        return $card;
    }

    public function initPlayers($player1_class='Hunter', $player1_deck=[], $player2_class='Mage', $player2_deck=[]) {
        $player1_deck = app('Deck', [app($player1_class), $player1_deck]);
        $player2_deck = app('Deck', [app($player2_class), $player2_deck]);

        $this->game->init($player1_deck, $player2_deck);
    }

    public function getActivePlayerId() {
        return $this->game->getActivePlayer()->getPlayerId();
    }

    public function getDefendingPlayerId() {
        return $this->game->getDefendingPlayer()->getPlayerId();
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/1/15
 * Time: 9:36 PM
 */

namespace App\Game\Cards;

use App\Exceptions\MissingCardNameException;
use App\Game\CardSets\CardSets;
use App\Game\Game;
use App\Game\Player;
use App\Listeners\BattlecryPhase;

class Card
{
    protected $id;

    protected $set;

    protected $name;

    protected $cost;

    protected $type;

    protected $mechanics = [];

    protected $sub_mechanics = [];

    protected $card_json;

    protected $trigger;

    protected $choose_option;

    /** @var  Game $game */
    protected $game;

    /** @var  Player $owner */
    protected $owner = null;

    protected $play_order_id;

    // todo this should not even be here =( all random numbers default to 0
    protected $random_number = 0;

    public function __construct(Player $player) {
        $this->owner = $player;
    }

    /**
     * Load a card from json into object.
     *
     * @param null $name
     * @throws MissingCardNameException
     * @throws \App\Exceptions\UnknownCardNameException
     */
    public function load($name = null) {
        if (is_null($name)) {
            throw new MissingCardNameException();
        }

        /** @var CardSets $card_sets */
        $card_sets       = app('CardSets');
        $this->card_json = $card_sets->findCard($name);

        $this->owner->getGame()->incrementCardsPlayedThisGame();
        $this->play_order_id = $this->owner->getGame()->getCardsPlayedThisGame();
        $this->id            = $this->play_order_id;
        $this->set           = array_get($this->card_json, 'set');

        $trigger_array = App('CardSetTriggers')->getSetTriggers();

        $trigger       = array_get($trigger_array, $name);
        $this->trigger = $trigger;

        $this->name      = $name;
        $this->cost      = array_get($this->card_json, 'cost', 0);
        $this->type      = array_get($this->card_json, 'type', CardType::$MINION);
        $this->mechanics = array_get($this->card_json, 'mechanics', []);
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return null
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return Player
     */
    public function getOwner() {
        return $this->owner;
    }

    /**
     * @param Player|null $owner
     */
    public function setOwner(Player $owner) {
        $this->owner = $owner;
    }

    /**
     * @return mixed
     */
    public function getGame() {
        return $this->game;
    }

    /**
     * @return mixed
     */
    public function getCost() {
        return $this->cost;
    }

    /**
     * @param mixed $cost
     */
    public function setCost($cost) {
        $this->cost = $cost;
    }

    /**
     * @return mixed
     */
    public function getPlayOrderId() {
        return $this->play_order_id;
    }

    /**
     * @param mixed $play_order_id
     */
    public function setPlayOrderId($play_order_id) {
        $this->play_order_id = $play_order_id;
    }

    /**
     * @return array
     */
    public function getMechanics() {
        return $this->mechanics;
    }

    public function setMechanics($mechanics = []) {
        $this->mechanics = array_merge($this->mechanics, $mechanics);
    }

    /**
     * @return array
     */
    public function getSubMechanics() {
        return $this->sub_mechanics;
    }

    /**
     * @param string $mechanic
     * @return bool
     */
    public function hasMechanic($mechanic) {
        return array_search($mechanic, $this->mechanics) !== false;
    }

    public function removeMechanic($mechanic) {
        $this->mechanics = array_diff($this->mechanics, [$mechanic]);
    }

    public function removeAllMechanics() {
        $this->mechanics = [];
    }

    /**
     * @return mixed
     */
    public function getRandomNumber() {
        return $this->random_number;
    }

    /**
     * @param mixed $random_number
     */
    public function setRandomNumber($random_number) {
        $this->random_number = $random_number;
    }

    /**
     * @return mixed
     */
    public function getSet() {
        return $this->set;
    }

    /**
     * @return mixed
     */
    public function getTrigger() {
        return $this->trigger;
    }

    /**
     * @return mixed
     */
    public function getChooseOption() {
        return $this->choose_option;
    }

    /**
     * @param mixed $choose_option
     */
    public function setChooseOption($choose_option) {
        $this->choose_option = $choose_option;
    }

}
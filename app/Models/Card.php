<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/1/15
 * Time: 9:36 PM
 */

namespace App\Models;

use App\Exceptions\MissingCardNameException;

class Card
{
    protected $id;

    protected $name;

    protected $cost;

    protected $type;

    protected $mechanics = [];

    protected $sub_mechanics = [];
    
    protected $card_json;

    /** @var  Game $game */
    protected $game;

    /** @var  Player $owner */
    protected $owner = null;

    protected $play_order_id;


    public function __construct(Game $game) {
        $this->game = $game;
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
        $card_sets = app('CardSets');
        $this->card_json = $card_sets->findCard($name);

        $this->id        = rand(1, 1000000);
        $this->name      = $name;
        $this->cost      = array_get($this->card_json, 'cost', 0);
        $this->type      = array_get($this->card_json, 'type', CardType::$MINION);
        $this->mechanics = array_get($this->card_json, 'mechanics', []);

        // TODO fix this jank
        if (strpos(array_get($this->card_json, 'text', ''), 'Choose One')) {
            $this->mechanics[] = Mechanics::$CHOOSE;
        }

        if (strpos(array_get($this->card_json, 'text', ''), 'Overload')) {
            $this->mechanics[] = Mechanics::$OVERLOAD;
        }

        switch ($this->card_json['name']) {
            case 'Spellbreaker':
                $this->sub_mechanics = [Mechanics::$BATTLECRY => [Mechanics::$SILENCE]];
        }
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

}
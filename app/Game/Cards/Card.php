<?php namespace App\Game\Cards;

use App\Exceptions\MissingCardNameException;
use App\Game\Game;
use App\Game\Interfaces\ExportableInterface;
use App\Game\Player;

class Card implements ExportableInterface
{
    protected $id;

    protected $set;

    protected $name;

    protected $cost;

    protected $type;

    protected $mechanics;

    protected $sub_mechanics;

    protected $card_json;

    protected $trigger;

    protected $choose_option;

    protected $text;

    /** @var  Game $game */
    protected $game;

    /** @var  Player $owner */
    protected $owner = null;

    protected $play_order_id;

    // todo this should not even be here =( all random numbers default to 0
    protected $random_number = 0;

    public function __construct(Player $player, $name = null) {
        if (is_null($name)) {
            throw new MissingCardNameException();
        }

        $this->owner = $player;

        $this->card_json = app('CardSets')->findCard($name);

        $this->owner->getGame()->incrementCardsPlayedThisGame();
        $this->play_order_id = $this->owner->getGame()->getCardsPlayedThisGame();
        $this->id            = $this->play_order_id;
        $this->set           = array_get($this->card_json, 'set');
        $this->text          = array_get($this->card_json, 'text');

        $trigger_array = App('CardSetTriggers')->getSetTriggers();

        $trigger       = array_get($trigger_array, $name);
        $this->trigger = $trigger;

        $this->name      = $name;
        $this->cost      = array_get($this->card_json, 'cost', 0);
        $this->type      = array_get($this->card_json, 'type', CardType::$MINION);
        $this->mechanics = array_get($this->card_json, 'mechanics', []);
    }

    public static function load(Player $player, $name) {
        // Todo this is not super efficient since card constructor calls as well
        $card_json = app('CardSets')->findCard($name);
        $card_type = array_get($card_json, 'type');

        $available_types = ['Minion', 'Weapon', 'Enchantment'];

        $load_type = 'Card';
        if (in_array($card_type, $available_types)) {
            $load_type = $card_type;
        }

        return app($load_type, [$player, $name]);
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

    /**
     * @return mixed
     */
    public function getText() {
        return $this->text;
    }

    /**
     * @return string
     */
    public function export() {
        return json_encode([
            "Card" => [
                "cost"          => $this->cost,
                "name"          => $this->name,
                "play_order_id" => $this->play_order_id
            ]]);
    }
}
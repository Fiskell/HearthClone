<?php namespace App\Models;

use App\Exceptions\InvalidTargetException;
use App\Exceptions\MissingCardHandleException;
use App\Exceptions\UnknownCardHandleException;

/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 8/23/15
 * Time: 3:07 PM
 */
class Card
{
    protected $id;
    protected $handle;
    protected $attack;
    protected $defense;
    protected $type;
    protected $alive;
    protected $mechanics     = [];
    protected $sub_mechanics = [];
    protected $owner         = null;
    protected $game;
    protected $sleeping;

    public function __construct(Game $game) {
        $this->game = $game;
    }

    public function load($handle = null) {
        if (is_null($handle)) {
            throw new MissingCardHandleException();
        }

        $this->id = rand(1, 1000000);

        switch ($handle) {
            case 'Argent Squire':
                $this->attack    = 1;
                $this->defense   = 1;
                $this->type      = CardType::$CREATURE;
                $this->mechanics = [Mechanics::$DIVINE_SHIELD];
                break;
            case 'Consecrate':
                $this->type = CardType::$SPELL;
                break;
            case 'Dread Corsair':
                $this->attack    = 3;
                $this->defense   = 3;
                $this->type      = CardType::$CREATURE;
                $this->mechanics = [Mechanics::$TAUNT];
                break;
            case 'Knife Juggler':
                $this->attack  = 3;
                $this->defense = 2;
                $this->type    = CardType::$CREATURE;
                break;
            case 'Spellbreaker':
                $this->attack        = 4;
                $this->defense       = 3;
                $this->type          = CardType::$CREATURE;
                $this->mechanics     = [Mechanics::$BATTLECRY];
                $this->sub_mechanics = [Mechanics::$BATTLECRY => [Mechanics::$SILENCE]];
                break;
            case 'Wisp':
                $this->attack  = 1;
                $this->defense = 1;
                $this->type    = CardType::$CREATURE;
                break;
            case 'Worgen Infiltrator':
                $this->attack    = 2;
                $this->defense   = 1;
                $this->type      = CardType::$CREATURE;
                $this->mechanics = [Mechanics::$STEALTH];
                break;

            default:
                throw new UnknownCardHandleException();
        }

        $this->sleeping = !$this->hasMechanic(Mechanics::$CHARGE);
        $this->handle   = $handle;
        $this->alive    = true;
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
    public function getHandle() {
        return $this->handle;
    }

    /**
     * @return mixed
     */
    public function getAttack() {
        return $this->attack;
    }

    /**
     * @return mixed
     */
    public function getDefense() {
        return $this->defense;
    }

    /**
     * @param mixed $new_defense
     */
    public function setDefense($new_defense) {
        $this->defense = $new_defense;
        if ($this->defense <= 0) {
            $this->defense = 0;
            $this->killed();
        }
    }

    /**
     * @return mixed
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function isAlive() {
        return $this->alive;
    }

    /**
     * Kill the card and remove it from the board.
     */
    public function killed() {
        $this->alive = false;
        $player      = $this->getOwner();
        $player->removeFromBoard($this->getId());
        $player->recalculateActiveMechanics();
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

    /**
     * Card instance attacks the target, dealing damage and potentially killing.
     *
     * @param Card $target
     * @throws InvalidTargetException
     */
    public function attack(Card $target) {

        if($this->isSleeping()) {
            throw new InvalidTargetException('This creature cannot attack because it is asleep');
        }

        $attacking_player = $this->getOwner();
        $defending_player = Player::getDefendingPlayer($attacking_player);

        /* Taunt */
        $target_has_taunt = $target->hasMechanic(Mechanics::$TAUNT);
        $player_has_taunt = $defending_player->hasMechanic(Mechanics::$TAUNT);

        if (!$target_has_taunt && $player_has_taunt) {
            throw new InvalidTargetException('You may only attack a creature with taunt');
        }

        /* Stealth */
        if ($target->hasMechanic(Mechanics::$STEALTH)) {
            throw new InvalidTargetException('You cannot attack a stealth minion');
        }

        /* Divine Shield */
        $target_has_divine_shield = $target->hasMechanic(Mechanics::$DIVINE_SHIELD);
        if ($target_has_divine_shield) {
            $target->removeMechanic(Mechanics::$DIVINE_SHIELD);

            return;
        }

        $this->setDefense($this->getDefense() - $target->getAttack());

        $target->setDefense($target->getDefense() - $this->getAttack());
    }

    public function isSleeping() {
        return $this->sleeping;
    }

    public function wakeUp() {
        $this->sleeping = false;
    }

}
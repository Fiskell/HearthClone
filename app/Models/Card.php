<?php namespace App\Models;
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
    protected $handle;
    protected $attack;
    protected $defense;
    protected $type;
    protected $alive;

    public function load($handle=null) {
        if(is_null($handle)) {
           throw new MissingCardHandleException();
        }

        switch($handle) {
            case 'argent-squire':
                $this->attack = 1;
                $this->defense = 1;
                $this->type = CardType::$CREATURE;
                break;
            case 'knife-juggler':
                $this->attack = 3;
                $this->defense = 2;
                $this->type = CardType::$CREATURE;
                break;
            case 'consecrate':
                $this->type = CardType::$SPELL;
                break;
            default:
                throw new UnknownCardHandleException();
        }

        $this->handle = $handle;
        $this->alive  = true;
    }

    /**
     * @return null
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @return mixed
     */
    public function getAttack()
    {
        return $this->attack;
    }

    /**
     * @return mixed
     */
    public function getDefense()
    {
        return $this->defense;
    }

    /**
     * @param mixed $new_defense
     */
    public function setDefense($new_defense)
    {
        $this->defense = $new_defense;
        if($this->defense <= 0) {
            $this->defense = 0;
            $this->killed();
        }
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Card instance attacks the target, dealing damage and potentially killing.
     *
     * @param Card $target
     */
    public function attack(Card $target) {

        $this->setDefense($this->getDefense() - $target->getAttack());

        $target->setDefense($target->getDefense() - $this->getAttack());
    }

    /**
     * @return mixed
     */
    public function isAlive()
    {
        return $this->alive;
    }

    public function killed()
    {
        $this->alive = false;
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: Kegimaro
 * Date: 9/13/15
 * Time: 2:17 PM
 */

namespace app\Game\Cards;

class Enchantment
{
    protected $id;
    protected $name;
    protected $source_card;
    protected $modified_attack;
    protected $modified_health;

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getSourceCard() {
        return $this->source_card;
    }

    /**
     * @return mixed
     */
    public function getModifiedAttack() {
        return $this->modified_attack;
    }

    /**
     * @return mixed
     */
    public function getModifiedHealth() {
        return $this->modified_health;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: stephenfiskell
 * Date: 1/25/15
 * Time: 5:20 PM
 */

namespace App\LaraHearthClone\Card;

use App\LaraHearthClone\Action\Attack;

class AbstractCreature extends AbstractCard
{
	protected $id;
	protected $attack;
	protected $health;
	protected $alive;
	protected $deathrattle;
	protected $attackAction;

	public function __construct(Attack $attack)
	{
		$this->attackAction = $attack;
		$this->id           = str_random(20);
		$this->alive        = false;
		$this->deathrattle  = false;
	}

	public function attack($target = null)
	{
		/** @var Attack $action */
		$this->attackAction->value  = $this->attack;
		$this->attackAction->target = $target;
		$this->attackAction->run();
	}

	/**
	 * @return mixed
	 */
	public function getAttack()
	{
		return $this->attack;
	}

	/**
	 * @param mixed $attack
	 */
	public function setAttack($attack)
	{
		$this->attack = $attack;
	}

	/**
	 * @return mixed
	 */
	public function getHealth()
	{
		return $this->health;
	}

	/**
	 * @param mixed $health
	 */
	public function setHealth($health)
	{
		$this->health = $health;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return boolean
	 */
	public function isAlive()
	{
		return $this->alive;
	}

	public function summon()
	{
		$this->alive = 1;
	}

	public function kill()
	{
		$this->alive = 0;
	}

	/**
	 * @return boolean
	 */
	public function isDeathrattle()
	{
		return $this->deathrattle;
	}

	/**
	 * @param boolean $deathrattle
	 */
	public function setDeathrattle($deathrattle)
	{
		$this->deathrattle = $deathrattle;
	}

}
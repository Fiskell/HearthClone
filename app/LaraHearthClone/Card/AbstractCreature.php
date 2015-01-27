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
	protected $attack;
	protected $defense;
	protected $attackAction;

	public function __construct(Attack $attack) {
		$this->attackAction = $attack;
	}

	public function attack()
	{
		/** @var Attack $action */
		$this->attackAction->value  = $this->attack;
		$this->attackAction->target = null;
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
	public function getDefense()
	{
		return $this->defense;
	}

	/**
	 * @param mixed $defense
	 */
	public function setDefense($defense)
	{
		$this->defense = $defense;
	}


}
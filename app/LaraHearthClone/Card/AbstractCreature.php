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

}
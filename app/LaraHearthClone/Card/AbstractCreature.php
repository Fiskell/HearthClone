<?php
/**
 * Created by PhpStorm.
 * User: stephenfiskell
 * Date: 1/25/15
 * Time: 5:20 PM
 */

namespace Card;

use Action\Attack;
use Processor\Stack;

class AbstractCreature extends AbstractCard
{
	protected $attack;
	protected $defense;

	public function attack(Stack $stack)
	{
		/** @var Attack $action */
		$action         = App::make('Attack');
		$action->value  = $this->attack;
		$action->target = null;
		$action->run();
	}

}
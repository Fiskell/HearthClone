<?php
/**
 * Created by PhpStorm.
 * User: stephenfiskell
 * Date: 1/25/15
 * Time: 6:53 PM
 */

namespace App\LaraHearthClone\Action;

use App\LaraHearthClone\Card\AbstractCreature;
use Player\Player;

class Attack extends AbstractAction
{
	public $value;

	/** @var  AbstractCreature|Player */
	public $target;

	public function resolve() {
		$health = $this->target->getHealth();

		if(is_null($this->target)) {
			//TODO attack player
			return;
		}

		if($health < $this->value) {
			$this->target->setAlive(false);
		}
	}

}
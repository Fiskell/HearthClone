<?php
/**
 * Created by PhpStorm.
 * User: stephenfiskell
 * Date: 1/28/15
 * Time: 10:48 PM
 */

namespace App\LaraHearthClone\Card\Library\Classic;

use App\LaraHearthClone\Card\AbstractCreature;

class BloodfenRaptor extends AbstractCreature
{

	public function init()
	{
		$this->cost   = 2;
		$this->name   = "Bloodfen Raptor";
		$this->attack = 3;
		$this->health = 2;
	}
} 
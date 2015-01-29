<?php
/**
 * Created by PhpStorm.
 * User: stephenfiskell
 * Date: 1/28/15
 * Time: 10:42 PM
 */

namespace App\LaraHearthClone\Card\Library\Classic;

use App\LaraHearthClone\Card\AbstractCreature;

class BoulderfistOgre extends AbstractCreature
{

	public function init()
	{
		$this->name   = "Boulderfist Ogre";
		$this->cost   = 6;
		$this->attack = 6;
		$this->health = 7;
	}
}
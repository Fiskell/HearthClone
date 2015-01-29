<?php
/**
 * Created by PhpStorm.
 * User: stephenfiskell
 * Date: 1/28/15
 * Time: 11:08 PM
 */

namespace App\LaraHearthClone\Card\Library\Classic;

use App\LaraHearthClone\Card\AbstractCreature;

class LeperGnome extends AbstractCreature
{
	public function init()
	{
		$this->name        = "Leper Gnome";
		$this->cost        = 2;
		$this->attack      = 2;
		$this->health      = 1;
		$this->deathrattle = true;
	}

	public function deathrattle() {
		echo 'minus 2 life';
	}

} 
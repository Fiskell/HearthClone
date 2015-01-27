<?php
/**
 * Created by PhpStorm.
 * User: stephenfiskell
 * Date: 1/25/15
 * Time: 5:23 PM
 */

namespace App\LaraHearthClone\Action;


use App\LaraHearthClone\Processor\Stack;

abstract class AbstractAction {

	public function run() {
		Stack::push($this);
	}
}
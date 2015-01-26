<?php
/**
 * Created by PhpStorm.
 * User: stephenfiskell
 * Date: 1/25/15
 * Time: 5:23 PM
 */

namespace Action;

use Processor\Stack;

class AbstractAction {

	public function run(Stack $stack) {
		$stack->push($this);
	}
}
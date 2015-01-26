<?php
/**
 * Created by PhpStorm.
 * User: stephenfiskell
 * Date: 1/25/15
 * Time: 5:23 PM
 */

namespace Action;

use Processor\Stack;

abstract class AbstractAction {

	public function run() {
		/** @var Stack $stack */
		$stack = App::make('Stack');
		$stack->push($this);
	}
}
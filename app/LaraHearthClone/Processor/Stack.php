<?php
/**
 * Created by PhpStorm.
 * User: stephenfiskell
 * Date: 1/25/15
 * Time: 5:28 PM
 */

namespace Processor;

use Action\AbstractAction;

class Stack {
	protected $stack;

	public function __construct() {
		$this->stack = [];
	}

	public function push(AbstractAction $action) {
		//TODO some cards will have multiple options for actions.
		$this->stack[] = $action;
	}

	public function pop() {
		$count = count($this->stack);
		$action = $this->stack[$count-1];
		unset($this->stack[$count-1]);
		return $action;
	}
} 
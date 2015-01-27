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
	protected $stack;

	public function __construct(Stack $stack) {
		$this->stack = $stack;
	}

	public function run() {
		$this->stack->push($this);
		dd($this->stack);
	}
}
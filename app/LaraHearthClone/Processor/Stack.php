<?php
/**
 * Created by PhpStorm.
 * User: stephenfiskell
 * Date: 1/25/15
 * Time: 5:28 PM
 */

namespace App\LaraHearthClone\Processor;

use App\LaraHearthClone\Action\AbstractAction;

class Stack {
	public static $stack;

	public function __construct() {
		static::$stack = [];
	}

	public static function push(AbstractAction $action) {
		//TODO some cards will have multiple options for actions.
		if(!isset(static::$stack)) {
			static::$stack = [];
		}

		static::$stack[] = $action;
	}

	public static function pop() {
		if(!isset(static::$stack)) {
			static::$stack = [];
		}
		$count = count(static::$stack);
		$action = static::$stack[$count-1];
		unset(static::$stack[$count-1]);
		return $action;
	}
} 
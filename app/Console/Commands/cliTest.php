<?php namespace App\Console\Commands;

use App\LaraHearthClone\Card\AbstractCreature;
use App\LaraHearthClone\Processor\Stack;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class cliTest extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'test:sandbox';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

	/** @var AbstractCreature */
	protected $creature;

	/** @var Stack  */
	protected $stack;

	/**
	 * Create a new command instance.
	 *
	 * @param AbstractCreature $creature
	 * @param Stack            $stack
	 *
	 * @return \App\Console\Commands\cliTest
	 */
	public function __construct(AbstractCreature $creature, Stack $stack)
	{
		parent::__construct();

		$this->creature = $creature;
		$this->stack = $stack;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->creature->attack();

		print_r($this->stack);
	}

}

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

	/**
	 * Create a new command instance.
	 *
	 * @param AbstractCreature $creature
	 *
	 * @return \App\Console\Commands\cliTest
	 */
	public function __construct(AbstractCreature $creature)
	{
		parent::__construct();

		$this->creature = $creature;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$creature1 = App::make('App\LaraHearthClone\Card\Library\Classic\BloodfenRaptor');
		$creature1->init();
		$creature1->setAlive(true);
		print_r($creature1);

		$creature2 = App::make('App\LaraHearthClone\Card\Library\Classic\BoulderfistOgre');
		$creature2->init();
		$creature2->setAlive(true);
		$creature2->attack($creature1);

		$action = Stack::pop();
		$action->resolve();

//		$this->creature->attack();

		print_r($creature1);
	}

}

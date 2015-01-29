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
		$creature0 = App::make('App\LaraHearthClone\Card\Library\Classic\LeperGnome');
		$creature0->init();
		$creature0->summon(true);
		print_r($creature0);

		$creature1 = App::make('App\LaraHearthClone\Card\Library\Classic\BloodfenRaptor');
		$creature1->init();
		$creature1->summon(true);
		print_r($creature1);

		$creature2 = App::make('App\LaraHearthClone\Card\Library\Classic\BoulderfistOgre');
		$creature2->init();
		$creature2->summon(true);
		$creature2->attack($creature0);
		print_r($creature2);

		$action = Stack::pop();
		$action->resolve();

//		$this->creature->attack();

		print_r($creature1);
	}

}

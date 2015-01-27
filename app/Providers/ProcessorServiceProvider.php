<?php namespace App\Providers;

use App\LaraHearthClone\Processor\Stack;
use Illuminate\Support\ServiceProvider;

class ProcessorServiceProvider extends ServiceProvider
{

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton(
			'App\LaraHearthClone\Processor\Stack',
			function () {
				return new Stack();
			}
		);
	}

}

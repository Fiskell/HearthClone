<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Processor\Stack;

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
			'LaraHearthClone\Processor\Stack',
			function () {
				return new Stack();
			}
		);
	}

}

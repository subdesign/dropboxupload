<?php namespace Bszalai\Dropboxupload;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class DropboxuploadServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Boot time settings
	 * 
	 * @return void
	 */
	public function boot()
	{
		// place of config folder
		$this->package('bszalai/dropboxupload', null, __DIR__.'/../..');

		// register the autoload
		AliasLoader::getInstance()->alias('Dropboxupload', 'Bszalai\Dropboxupload\DropboxuploadCommand');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['dropbox.upload'] = $this->app->share(function($app) 
		{

			return new DropboxuploadCommand;

		});

		$this->commands('dropbox.upload');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('dropboxupload');
	}

}

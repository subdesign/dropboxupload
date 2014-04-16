<?php namespace Bszalai\Dropboxupload;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Config, 
	File, 
	Log, 
	ZipArchive, 
	Crypt, 
	Dropbox\Client;

class DropboxuploadCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'dropbox:upload';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Upload database dump to Dropbox';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		// check if dumps path is modified
		$dumpPath = Config::get('database.backup.path');

		// if not, use the default one
		if ( ! $dumpPath) 
		{ 
			$dumpPath = app_path().'/storage/dumps';
		}

		$files = File::files($dumpPath);

		// if nothing in the folder, drop error
		if (empty($files))
		{
			Log::error('[Dropboxupload]: Dump file not found in '.$dumpPath);

		    $this->error('[Dropboxupload] Dump file not found in '.$dumpPath);

		    exit;
		}

		$filename = basename($files[0]);

		// if encryption is enabled
		if( Config::get('dropboxupload::encrypt.enabled'))
		{
			// read the plain file
			$plain = File::get($dumpPath.'/'.$filename);

			// if no encryption key specified drop/log error
			if( strlen(Config::get('dropboxupload::encrypt.key')) == 0)
			{
				$this->error('[Dropboxupload] Error: No encryption key specified');

				Log::error('[Dropboxupload] Error: No encryption key specified');

				exit;
			}

			// set the key for encryption
			Crypt::setKey( Config::get('dropboxupload::encrypt.key'));

			// encrypt the source file
			$encrypted = Crypt::encrypt($plain);

			// write back to the same place
			File::put($dumpPath.'/'.$filename, $encrypted);

			$this->info('[Dropboxupload] Dump file encrypted successfully');
		}

		// if zip compression is enabled
		if( Config::get('dropboxupload::compress'))				
		{						
			$zip = new ZipArchive;

			// create a new zip filename
			$zipname = basename($filename, '.sql').'.zip';

			// create a zip file
			if ($zip->open($dumpPath.'/'.$zipname, ZIPARCHIVE::CREATE) === TRUE) 
			{
				// add the dump to zip file
			    $zip->addFile($dumpPath.'/'.$filename, $filename);
			    
			    // save zip file			    
			    $zip->close();

			    // filename is now the zipped filename
			    $filename = $zipname;

			    $this->info('[Dropboxupload] Dump file zipped successfully');
			} 
			// if error happened by zipping drop/log error
			else 
			{
			    Log::error('[Dropboxupload] Error zipping dump file!');

			    $this->error('[Dropboxupload] Error zipping dump file!');

			    exit;
			}	
		}

		// get the access token from file
		$token = json_decode(File::get(storage_path().'/token.json'));

		$accessToken = $token->access_token;

		// get a Dropbox instance
	    $client = new Client($accessToken, 'Dropbox-backup', null, null);

	    // open the file that needs upload
	    $fp = fopen($dumpPath.'/'.$filename, "rb");

	    // set the prefix for filename if it exists
	    $dropboxname = (strlen(Config::get('dropboxupload::prefix')) > 0) ? Config::get('dropboxupload::prefix').'-'.$filename : $filename;

	    // add trailing slashes to the folder name
	    if( strlen(Config::get('dropboxupload::uploadfolder')) > 0)
	    {
	    	$foldername = "/".trim( Config::get('dropboxupload::uploadfolder'), "/")."/";	
	    }
	    else
	    {
	    	$foldername = "/";
	    }	    
	    
	    // upload the file to Dropbox
	    $result = $client->uploadFile($foldername.$dropboxname, \Dropbox\WriteMode::add(), $fp);

	    // close source file
	    fclose($fp);

	    // log the result of the upload
	    Log::debug('[Dropboxupload] Upload result: '.json_encode($result));

	    $this->info('[Dropboxupload] Dump file uploaded successfully');

	    // if we have a zip file, delete the original sql file, too
	    if (Config::get('dropboxupload::compress')) File::delete($dumpPath.'/'.basename($files[0]));

	    // delete the uploaded file
	    File::delete($dumpPath.'/'.$filename);

	    $this->info('[Dropboxupload] Temporary files deleted');
		
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

}

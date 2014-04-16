# Database dump Dropbox upload

Dropbox upload is a Laravel 4 package, which uploads a previously dumped database file to a Dropbox account. The database backup is made by the package [schickling/backup](https://github.com/schickling/laravel-backup). Thanks Johannes!

The big advantage of this package is that it doesn't ask you to enter any input, because uses a permanent token key, your uploads will happen in the background..

## Usage

### Generate a permanent token for the package

- Go to [https://dropbox.com/developers/apps](https://dropbox.com/developers/apps) and generate api keys.

- Install Dropboxupload

    `composer require bszalai/dropboxupload:dev-master`

- Create a file __keys.json__ and fill it with your keys

```
{  
   	"key" : "Dropbox app key",  
   	"secret": "Dropbox app secret"  
}    
```

- Copy this file into the Dropbox package's __examples__ folder (_vendor/dropbox/dropbox-sdk/examples_).

- Open a terminal, go to the above folder and enter 

    `php authorize.php keys.json token.json`

- Click on the url in the terminal, it'll open a browser window, _Allow_ the app to access your folder, and copy the provided key (you have to log in to Dropbox if you aren't logged in)

- Go back to terminal, and paste the above key, and press Enter.

- You'll have a __token.json__ file created.

- Copy this file into the __app/storage__ folder. You are done.

### Using the package

- Edit your `app.php` in the __app/config__ folder, and add the following lines to the service providers

	`Schickling\Backup\BackupServiceProvider`,    
    `Bszalai\Dropboxupload\DropboxuploadServiceProvider`

- Publish the package config file

    `php artisan config:publish bszalai/dropboxupload`

- Edit the config file (_app/config/packages/bszalai/dropboxupload/config.php_), you have several options

    * prefix - name-prefix of the source file

    * uploadfolder - where to upload the file

    * compress - if set to true, it'll create a zip file

    * encrypt - encrypt the source file, set the encryption key so you can decrypt your file later

- Create a CRON job for the Backup package

```
Example:

/usr/local/bin/php53 /path/to/your/app/artisan db:backup
```

- Create another CRON job for the Dropbox upload, set the run time after the backup surely finished

```
Example:

/usr/local/bin/php53 /path/to/your/app/artisan dropbox:upload
```

### License

Dropboxbackup is under [MIT License](http://opensource.org/licenses/MIT)

### Copyright

&copy; 2014 [Barna Szalai](mailto:szalai.b@gmail.com)
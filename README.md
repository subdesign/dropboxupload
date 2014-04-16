# Dropbox upload

Dropbox upload is a Laravel 4 package, which uploads a previously dumped database file to a Dropbox account. The database backup is made by the package [schickling/backup](https://github.com/schickling/laravel-backup). It uses a permanent token key, so it doesn't need to log in to Dropbox every time.

## Usage

### Generate a permanent token for the package

1. Go to [https://dropbox.com/developers/apps](https://dropbox.com/developers/apps) and generate api keys.

2. Install Dropboxupload

    composer require bszalai/dropboxupload:dev-master

3. Create a file __keys.json__ and fill it with your keys

    {  
        "key" : "Dropbox app key",  
        "secret": "Dropbox app secret"  
    }    

4. Copy this file into the Dropbox packages's __examples__ folder (_vendor/dropbox/dropbox-sdk/examples_).

5. Go to the terminal and Enter

    php authorize.php keys.json token.json

6. Click on the url in the terminal, it'll open a browser window, _Allow_ the app to access your folder, and copy the provided key

7. Go back to terminal, and paste the above key, and press Enter.

8. You'll have a __token.json__ file created.

9. Copy this file into the __app/storage__ folder.

### Using the package

1. Edit your app.php, and add the following line to the service providers

    Bszalai\Dropboxupload\DropboxuploadServiceProvider

2. Publish the package config file

    php artisan config:publish bszalai/dropboxupload 

3. Edit the config file, you have several options

    * prefix - name prefix of the source file

    * uploadfolder - where to upload the file

    * compress - if set to true, it'll create a zip file

    * encrypt - encrypt the source file, set the encryption key so you can decrypt your file later

4. Create a CRON job for the Backup package

	Example:

    /usr/local/bin/php53 /folder/to/your/app/artisan db:backup

5. Create another CRON job for the Dropbox upload, set the run time after the backup surely finished

	Example:

    /usr/local/bin/php53 /folder/to/your/app/artisan dropbox:upload

### License

Dropboxbackup is under [MIT License](http://opensource.org/licenses/MIT)

### Copyright

&copy; 2014 [Barna Szalai](mailto:szalai.b@gmail.com)
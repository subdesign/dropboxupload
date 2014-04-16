<?php

return array(

	// name prefix of the uploaded file
	'prefix' => 'db-backup',

	// upload folder in your Dropbox account
	'uploadfolder' => '/backups/',

	// make a zip file from plain sql
	'compress' => true,

	// encrypt the sql file
	'encrypt' => array(

		'enabled' => true,

		// key for encryption
		'key' => 'abcd1234'
	)	

);
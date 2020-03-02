<?php 
include ('dumper.php');
include ('dropbox_api.php');

try {
	$world_dumper = Shuttle_Dumper::create(array(
		'host' => 'jlg7sfncbhyvga14.cbetxkdyhwsb.us-east-1.rds.amazonaws.com',
		'username' => 'ryiliw2382461i0c',
		'password' => 'qamifustgsstng86',
		'db_name' => 'yg1wwu54z7ur2jc3',
	));

	// dump the database to plain text file
	$world_dumper->dump('voice.sql');
	print_r(upload('voice.sql'));

} catch(Shuttle_Exception $e) {
	echo "Couldn't dump database: " . $e->getMessage();
}
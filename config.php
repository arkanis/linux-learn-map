<?php

$_CONFIG = array(
	'tasks_dir' => $ROOT_DIR . '/exercises',
	'ssh' => array(
		'user_to_server_list' => $ROOT_DIR . '/user_to_server.csv'
	),
	'server_auth' => array(
		'user' => 'root',
		'public_key_file' => $ROOT_DIR . '/keys/test.pub',
		'private_key_file' => $ROOT_DIR . '/keys/test'
	)
);

?>

<?php

$linuxvm_list = $ROOT_DIR . '/include/linuxvm-liste.csv';

$row = 1;
if (($handle = fopen($linuxvm_list, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $row++;
	if ($data[6] == $_SERVER['PHP_AUTH_USER']) {
		$server_target = $data[0];
		break;
	}
    }
    fclose($handle);
}

$_CONFIG = array(
	'tasks_dir' => $ROOT_DIR . '/exercises',
	'server_auth' => array(
		'server' => $server_target,
		'user' => 'root',
		'public_key_file' => $ROOT_DIR . '/keys/test.pub',
		'private_key_file' => $ROOT_DIR . '/keys/test'
	)
);

?>

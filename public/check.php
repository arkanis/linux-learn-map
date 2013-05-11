<?php

$ROOT_DIR = dirname(__FILE__) . '/..';
require_once($ROOT_DIR . '/config.php');

function run_script_on_server($script, $server, $user, $public_key_file, $private_key_file){
	$con = @ssh2_connect($server);
	if ($con === false)
		return array(false, false);
	
	$result = ssh2_auth_pubkey_file($con, $user, $public_key_file, $private_key_file);
	if (!$result)
		return array(false, false);
	
	$remote_script = basename($script);
	$result = ssh2_scp_send($con, $script, $remote_script, 0700);
	if (!$result)
		return array(false, false);
	
	$io_stream = ssh2_exec($con, "( ./$remote_script ) 2>&1; echo $?");
	if ($io_stream) {
		stream_set_blocking($io_stream, true);
		
		$output = '';
		while(true){
			$line = fgets($io_stream);
			if ($line === false)
				break;
			$output .= $line;
		}
		
		fclose($io_stream);
		
		$output = rtrim($output);
		$last_newline_index = strrpos($output, "\n");
		$status_code = intval(substr($output, $last_newline_index + 1));
		$output = substr($output, 0, $last_newline_index);
	} else {
		$output = false;
		$status_code = false;
	}
	
	$sftp_con = ssh2_sftp($con);
	if ($sftp_con)
		ssh2_sftp_unlink($sftp_con, $remote_script);
	
	return array($output, $status_code);
}

// TODO: SANITIZE input!
$exercise = $_GET['exercise'];
$script = $_CONFIG['tasks_dir'] . '/' . $exercise . '/check';
$logged_in_user = $_SERVER['PHP_AUTH_USER'];
$server_name = null;

// Lookup the server in the user to server list
$fd = fopen($_CONFIG['ssh']['user_to_server_list'], 'r');
if ($fd) {
	while ( ($fields = fgetcsv($fd)) !== false ) {
		@list($user, $server) = $fields;
		if ($user === $logged_in_user) {
			$server_name = $server;
			break;
		}
	}
	fclose($fd);
}

if (!$server_name) {
	header('Content-Type: text/plain;charset=utf-8', true, 500);
	echo("Sorry, couldn't check your exercise result. Don't know which virtual machine name belongs to your user name.");
}

list($output, $status) = run_script_on_server($script, $server_name, $_CONFIG['server_auth']['user'], $_CONFIG['server_auth']['public_key_file'], $_CONFIG['server_auth']['private_key_file']);

if ($output === false or $status === false) {
	// Couldn't run check script, return 500
	header('Content-Type: text/plain;charset=utf-8', true, 500);
	echo("Sorry, couldn't check your exercise result. Unable to open an SSH connection to your virtual machine $server_name.");
} else {
	$http_status = ($status === 0) ? 200 : 418;
	header('Content-Type: text/plain;charset=utf-8', true, $http_status);
	echo($output);
}

?>

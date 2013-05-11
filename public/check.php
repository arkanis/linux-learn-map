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

$exercise = $_GET['exercise'];
$script = $_CONFIG['tasks_dir'] . '/' . $exercise . '/check';
list($output, $status) = run_script_on_server($script, $_CONFIG['server_auth']['server'], $_CONFIG['server_auth']['user'], $_CONFIG['server_auth']['public_key_file'], $_CONFIG['server_auth']['private_key_file']);

if ($output === false or $status === false) {
	// Couldn't run check script, return 500
	header('Content-Type: text/plain;charset=utf-8', true, 500);
	echo("Sorry, couldn't check your exercise result. Unable to open an SSH connection to your virtual machine.");
} else {
	$http_status = ($status === 0) ? 200 : 418;
	header('Content-Type: text/plain;charset=utf-8', true, $http_status);
	echo($output);
}

?>

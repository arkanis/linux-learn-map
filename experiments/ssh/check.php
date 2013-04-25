<?php

function run_script_on_server($script, $server, $user, $public_key_file, $private_key_file){
	$con = ssh2_connect($server);
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

$exercise = basename($_GET['exercise']);
$script = 'webserver/' . $exercise . '/check';
list($output, $status) = run_script_on_server($script, 'linuxvm196.mi.hdm-stuttgart.de', 'root', 'test.pub', 'test');
echo(json_encode(array(
	'passed' => ($status == 0),
	'status' => $status,
	'output' => $output
)));

?>
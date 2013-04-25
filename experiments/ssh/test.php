<?php

// Dependencies: libssh2-php package
require_once('ssh.php');

$ssh = SSH::connect('linuxvm196.mi.hdm-stuttgart.de', 'root', 'test.pub', 'test');
$ssh->upload('check', 'check', 0700)->exec('./check', $output, $status)->rm('check');
unset($con);
var_dump($output, $status);

/*
$con = ssh2_connect('linuxvm196.mi.hdm-stuttgart.de');
var_dump($con);

$result = ssh2_auth_pubkey_file($con, 'root', 'test.pub', 'test');
var_dump($result);


$result = ssh2_scp_send($con, 'check', 'check', 0700);
var_dump($result);

$io_stream = ssh2_exec($con, '( ./check ) 2>&1; echo $?');
var_dump($io_stream);
stream_set_blocking($io_stream, true);

$output = '';
while(true){
	$stdout_line = fgets($io_stream);
	$output .= $stdout_line;
	if ($stdout_line === false)
		break;
}

fclose($io_stream);

$output = rtrim($output);
$exit_code = (int)substr($output, strrpos($output, "\n")+1);
$output = substr($output, 0, strrpos($output, "\n"));
var_dump($output, $exit_code);


$sftp_con = ssh2_sftp($con);
$result = ssh2_sftp_unlink($sftp_con, 'check');
var_dump($result);

unset($con);
*/

?>
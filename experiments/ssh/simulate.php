<?php

require_once('ssh.php');
$ssh = SSH::connect('linuxvm196.mi.hdm-stuttgart.de', 'root', 'test.pub', 'test');

foreach( glob('*', GLOB_ONLYDIR) as $dir ) {
	foreach( glob($dir . '/*', GLOB_ONLYDIR) as $exercise ) {
		echo($exercise . "...\n");
		$ssh->upload($exercise . '/check', 'check', 0700)->exec('./check', $output, $status);
		echo("### check status: $status\n### $output\n");
		
		$ssh->upload($exercise . '/solve', 'solve', 0700)->exec('./solve', $output, $status);
		echo("### solve status: $status\n### $output\n");
		
		$ssh->upload($exercise . '/check', 'check', 0700)->exec('./check', $output, $status);
		echo("### check status: $status\n### $output\n");
		
		$ssh->upload($exercise . '/undo', 'undo', 0700)->exec('./undo', $output, $status);
		echo("### undo status: $status\n### $output\n");
		
		$ssh->upload($exercise . '/check', 'check', 0700)->exec('./check', $output, $status);
		echo("### check status: $status\n### $output\n");
	}
}

?>
<?php

// Dependencies: libssh2-php debian package
class SSH {
	static function connect($server, $user, $public_key_file, $private_key_file){
		$con = ssh2_connect($server);
		if ($con === false)
			return null;
		
		$result = ssh2_auth_pubkey_file($con, $user, $public_key_file, $private_key_file);
		if (!$result)
			return null;
		
		return new static($con);
	}
	
	private $con, $sftp_con;
	
	function __construct($connection){
		$this->con = $connection;
		$this->sftp_con = ssh2_sftp($this->con);
	}
	
	function upload($local_path, $remote_path, $permissions){
		ssh2_scp_send($this->con, $local_path, $remote_path, $permissions);
		return $this;
	}
	
	function rm($remote_path){
		ssh2_sftp_unlink($this->sftp_con, $remote_path);
		return $this;
	}
	
	function exec($command, &$output, &$status){
		$io_stream = ssh2_exec($this->con, "( $command ) 2>&1; echo $?");
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
			$status = intval(substr($output, $last_newline_index + 1));
			$output = substr($output, 0, $last_newline_index);
		} else {
			$output = false;
			$status = false;
		}
		
		return $this;
	}
}

?>
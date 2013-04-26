<?php

require_once('entry.php');

/**
 * Adds:
 * 	- `id` property with the parameterized name of the task. This is save to be used in URLs.
 */
class Task extends Entry
{
	function __construct($header, $content, $path)
	{
		parent::__construct($header, $content, $path);
		$this->id = self::parameterize(pathinfo(dirname($path), PATHINFO_FILENAME));
	}
}

?>
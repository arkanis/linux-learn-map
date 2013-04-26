<?php

$ROOT_DIR = dirname(__FILE__) . '/..';
require_once($ROOT_DIR . '/include/config.php');

require_once($ROOT_DIR . '/include/entry.php');
require_once($ROOT_DIR . '/include/markdown.php');
require_once($ROOT_DIR . '/include/view_helpers.php');

$tasks_dir = realpath($_CONFIG['tasks_dir']);
$dir_iterator = new RecursiveDirectoryIterator($tasks_dir);
$files = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);

$tasks = array();
foreach($files as $file) {
	// Skip everything that is not a check script
	if ( $file->getBasename() != 'check' )
		continue;
	
	$check_script = $file->getRealpath();
	$task_dir = dirname($check_script);
	$id = substr($task_dir, strlen($tasks_dir) + 1);
	$infos = Entry::load($task_dir . '/task.txt');
	if ($infos) {
		$title = $infos->title;
		$description = Markdown($infos->content);
		$points = intval($infos->points);
	} else {
		$title = basename($id);
		$description = null;
		$points = 0;
	}
	
	$tasks[] = array(
		'id' => $id,
		'check_script' => $check_script,
		'title' => $title,
		'description' => $description,
		'points' => $points
	);
}

// Sort tasks ascending by their id (directory and exercise name)
uasort($tasks, function($a, $b){
	if ( $a['id'] == $b['id'] )
		return 0;
	return ($a['id'] < $b['id']) ? -1 : 1;
});

?>
<!DOCTYPE html>
<html lang="de">
<head>
	<meta charset="utf-8">
	<title>Linux Learn Map</title>
	<script src="jquery.js"></script>
	<script>
		$(document).ready(function(){
			$('a.check').click(function(){
				var article = $(this).closest('article');
				var output = article.find('> div.console');
				output.html('').addClass('loading').removeClass('empty');
				$.ajax( $(this).attr('href'), { dataType: 'text' } ).done(function(check){
					output.html(check);
					article.addClass('passed').removeClass('failed');
				}).fail(function(xhr){
					output.html(xhr.responseText);
					article.addClass('failed').removeClass('passed');
				}).always(function(){
					output.removeClass('loading');
				});
				
				return false;
			});
		});
	</script>
	<link rel="stylesheet" href="reset.css">
	<style>
		body { font-family: sans-serif; background: hsl(0, 0%, 90%); }
		article { position: relative; overflow: hidden; margin: 1em 1em 2em 1em; padding: 0.5em; width: 40em;
			color: hsl(0, 0%, 25%); background: hsl(0, 0%, 97.5%); border: 1px solid white;
			border-radius: 5px; box-shadow: 1px 1px 5px hsl(0, 0%, 35%); }
		header { margin: 0 0 0.5em 0; padding: 0 0 0 47.5px; }
		header h1 { font-size: 1.5em; }
		header h1 + p { font-size: 0.77em; margin: 0.33em 0 0 0; color: gray; }
		header p.points { position: absolute; top: 0.5em; left: 0.5em; width: 40px; height: 40px;
			background: hsl(0, 0%, 90%); border: 1px solid hsl(0, 0%, 80%); border-radius: 5px; }
		header p.points span { display: block; margin: 1px 0 -1px 0; text-align: center; font-size: 10px; }
		header p.points span:first-child { display: block; text-align: center; font-size: 27.5px; }
		
		ul, ol { margin: 0.5em 0 0.5em 1.5em; }
		li { margin: 0.25em 0; }
		ul { list-style: circle; }
		ol { list-style: decimal; }
		
		a.check { float: left; width: 5em; padding: 0.5em 0.25em;
			color: hsl(0, 0%, 25%); background-color: hsl(0, 0%, 90%);
			border: 1px solid hsl(0, 0%, 65%); border-radius: 5px;
			box-shadow: inset 0 1px 2px hsla(0, 0%, 100%, 0.5);
			background-image: linear-gradient(to bottom, hsl(0, 0%, 100%), hsl(0, 0%, 90%));
			text-align: center; text-decoration: none; }
		a.check:active { background-image: linear-gradient(to top, hsl(0, 0%, 100%), hsl(0, 0%, 90%)) }
		a.check:focus { border: 2px solid gray; margin: -1px; }
		
		div.console { position: relative; margin: 0 0 0 100px; padding: 0.5em;
			font-family: monospace; color: #aaa; background: black 0.5em 0.5em no-repeat;
			border-radius: 5px; }
		/*
		div.console::before { content: ''; position: absolute; top: 1px; right: 1px; bottom: 1px; left: 1px;
			border: 1px solid #777; border-radius: 4px; }
		*/
		div.console.empty { display: none; }
		div.console.loading { padding-left: 30px; background-image: url(loading.gif); }
		div.console.loading::after { content: 'checking exercise...'; }
		
		article.passed { border-color: hsl(120, 50%, 40%); background: hsl(120, 50%, 85%); }
		article.failed { border-color: hsl(0, 50%, 40%); background: hsl(0, 50%, 85%); }
	</style>
</head>
<body>

<? foreach($tasks as $task): ?>
<article>
	<header>
		<h1><?= h($task['title']) ?></h1>
		<p>Task: <?= h($task['id']) ?></p>
		<p class="points"><span><?= h($task['points']) ?></span><span>Punkte</span></p>
	</header>
	
	<div><?= Markdown($task['description']) ?></div>
	
	<a tabindex="0" class="check" href="check.php?exercise=<?= urlencode($task['id']) ?>">check exercise</a>
	<div class="console empty"></div>
</article>
<? endforeach ?>

</body>
</html>
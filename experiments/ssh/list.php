<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Exercise Map</title>
	<script src="jquery.js"></script>
	<script>
		$(document).ready(function(){
			$('a').click(function(){
				var output = $(this).parent().find('> div');
				output.html('').attr('class', '').addClass('loading');
				$.getJSON(
					$(this).attr('href')
				).done(function(check){
					output.html(check.output);
					if (output.passed)
						output.addClass('passed');
					else
						output.addClass('failed');
				}).fail(function(){
					output.addClass('error');
				}).always(function(){
					output.removeClass('loading');
				});
				
				return false;
			});
		});
	</script>
	<style>
		div { padding: 2px; }
		div.loading { border: 1px solid hsl(240, 100%, 50%); padding-left: 22px; background: url(loading.gif) 2px 50% no-repeat; }
		div.loading::after { content: 'checking...'; }
		div.error { border: 1px solid hsl(0, 100%, 50%); }
		div.passed { border: 1px solid hsl(120, 100%, 50%); }
		div.failed { border: 1px solid hsl(0, 100%, 25%); }
	</style>
</head>
<body>

<ul>
<? foreach( glob('*', GLOB_ONLYDIR) as $dir ): ?>
	<li>
		<?= $dir ?>
		<ul>
<?		foreach( glob($dir . '/*', GLOB_ONLYDIR) as $exercise ): ?>
			<li>
				<?= basename($exercise) ?>
				<a href="check.php?exercise=<?= basename($exercise) ?>">check</a>
				<div></div>
			</li>
<?		endforeach ?>
		</ul>
	</li>
<? endforeach ?>
</ul>

</body>
</html>
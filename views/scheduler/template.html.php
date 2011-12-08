<html>
<head>
  <title>Scheduler</title>
<?php
	foreach ($styles as $style)
	{
		echo html::style($style);
	}
	foreach ($scripts as $script)
	{
		echo html::script($script);
	}
?>
</head>
<body>
  <div class="wrapper">
    <?php echo $content ?>
  </div>
</body>
</html>

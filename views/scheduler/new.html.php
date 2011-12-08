<div class="block">
<?php
echo View::factory('scheduler/_form.html', array(
		'task' => $task,
		'errors' => $errors,
	));
?>
</div>

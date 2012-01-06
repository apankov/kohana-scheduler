<?php

$statuses = array(
	'active'    => 'Active',
	'suspended' => 'Suspended',
	'disabled'  => 'Disabled',
);

if ($task->id)
{
	$action = '/scheduler/update';
}
else
{
	$action = '/scheduler/create';
}

if (empty($task->next_scheduled_at))
{
	$task->next_scheduled_at = '';
}

echo form::open($action, array('method' => 'POST'));
if ($task->id)
{
	echo form::hidden('_method', 'PUT');
	echo form::hidden('id', $task->id);
}
else
{
	echo form::hidden('_method', 'POST');
}

?>

<p>
<?php
$label = 'Task name';
if ($error = Arr::get($errors, 'name'))
{
	$label .= '<span class="label-error"> - ' . $error . '</span>';
}
echo form::label('name', $label);
echo form::input('name', $task->name, array(
		'class' => 'text ' . (isset($errors['name']) ? 'error' : ''),
	));
?>
</p>

<p>
<?php
$label = 'Task queue';
if ($error = Arr::get($errors, 'queue'))
{
	$label .= '<span class="label-error"> - ' . $error . '</span>';
}
echo form::label('queue', $label);
echo form::input('queue', $task->queue, array(
		'class' => 'text ' . (isset($errors['queue']) ? 'error' : ''),
		'placeholder' => 'default',
	));
?>
</p>

<p>
<?php
$label = 'Task args';
if ($error = Arr::get($errors, 'args'))
{
	$label .= '<span class="label-error"> - ' . $error . '</span>';
}
echo form::label('args', $label);
echo form::textarea('args', $task->args, array(
		'class' => 'text ' . (isset($errors['args']) ? 'error' : ''),
		'placeholder' => '{}',
	));
?>
</p>

<p>
  <label>Status:</label>
  <?php echo form::select('status', $statuses, $task->status, array('class' => 'styled')) ?>
</p>

<table>
  <tr>
    <td>Run once at</td>
    <td rowspan="2">- OR -</td>
    <td>Run recurring</td>
  </tr>
  <tr>
    <td>
      <label for="right_now">
        <?php echo form::checkbox('right_now', 1, array('checked' => 'checked')) ?> right now
      </label>
      <?php
	if (is_object($task->next_scheduled_at))
	{
		$task->next_scheduled_at = time();
	}
	else if (empty($task->next_scheduled_at))
	{
		$task->next_scheduled_at = '+ 5 minutes';
	}
	else {
		$task->next_scheduled_at = date('Y-m-d H:i:s', $task->next_scheduled_at);
	}

	echo form::input(
		'next_scheduled_at',
		$task->next_scheduled_at,
		array(
			'class' => 'styled',
			'placeholder' => date('Y-m-d H:i:s'),
		));
      ?>
    </td>

    <td>
      <label for="crontab">crontab like syntax</label>
      <?php echo form::input('crontab', $task->crontab, array('class' => 'styled', 'placeholder' => '* * * * *')) ?>
    </td>
  </tr>
</table>

<hr />

<p>
  <?php echo form::submit('submit', 'submit', array('class' => 'submit small')) ?>
  <?php echo html::anchor('/scheduler', 'Cancel', array('class' => 'cancel')) ?>
</p>

<?php echo form::close() ?>

<div class="block">
   <div class="block_content">
<?php
if ($tasks->count())
{
?>
<table cellpadding="0" cellspacing="0" width="100%">
  <thead>
  <tr>
    <th>Task</th>
    <th>Queue</th>
    <th>Recurring</th>
    <th>Next run</th>
    <th>&nbsp;</th>
  </tr>
  </thead>
  <tbody>
<?php
	foreach ($tasks as $task)
	{
		$next_run = '';
		if (empty($task->next_scheduled_at))
		{
			$next_run = 'NOT SET ';
			$next_run .= html::anchor('/scheduler/run_now/' . $task->id, 'Run now');
		}
		else
		{
			$next_run = date::fuzzy_span($task->next_scheduled_at);
			if ((time() > $task->next_scheduled_at) && $task->status == 'active')
			{
				$next_run = '<span class="datetime-error">' . $next_run . '</span>';
			}
		}
?>
  <tr class="task <?php echo $task->status ?>" taskid="<?php echo $task->id ?>">
    <td class="name"><?php echo $task->name ?></td>
    <td><?php echo $task->queue ?></td>
    <td><?php echo $task->crontab ?></td>
    <td><?php echo $next_run ?></td>
    <td>
      <?php echo html::anchor('/scheduler/edit/' . $task->id, 'Edit') ?>
      |
      <?php echo html::anchor('/scheduler/delete/' . $task->id, 'Delete') ?>
    </td>
  </tr>
  <tr class="task_details <?php echo $task->status ?> task_<?php echo $task->id ?>" class="hidden">
    <td colspan="5" class="hidden">
      <div class="clear">

        <div class="general">
          <div class="param">
            <span class="title">task id</span>
            <span class="value"><?php echo $task->id ?></span>
          </div>
          <div class="param">
            <span class="title">last ran at</span>
            <span class="value">
<?php
		if (empty($task->last_ran_at))
		{
			echo 'never';
		}
		else
		{
			echo date::fuzzy_span($task->last_ran_at);
			echo ' (<span class="smaller">' . date('Y-m-d H:i:s', $task->last_ran_at) . '</span>)';
		}
?>
            </span>
          </div>
          <div class="param">
            <span class="title">last ran job id</span>
            <span class="value"><?php echo $task->last_job_id ?></span>
          </div>
          <div class="param">
            <span class="title">next run at</span>
            <span class="value"><?php echo empty($task->next_scheduled_at) ? 'NOT SET' : date('Y-m-d H:i:s', $task->next_scheduled_at) ?></span>
          </div>
          <div class="param">
            <span class="title">status</span>
            <span class="value"><?php echo $task->status ?></span>
          </div>
          <div class="clear"></div>
        </div>

        <div class="args">
          <div class="param">
            <span class="title">args</span>
            <span class="value"><?php echo debug::vars($task->get_args()) ?></span>
          </div>
          <div class="param">
            <span class="title">args (text)</span>
            <span class="value"><?php echo debug::vars($task->args) ?></span>
          </div>
          <div class="clear"></div>
        </div>

      </div>
    </td>
  </tr>
<?php
	}
?>
  </tbody>
</table>

<?php
}
else
{
	echo 'No tasks yet.';
}
?>
  <p><a href="/scheduler/new">Add new task</a></p>
  </div>
</div>

<?php defined('SYSPATH') or die('No direct script access.');

class Scheduler {

	private static $instance = null;

	public static function instance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new Scheduler(Kohana::$config->load('scheduler'));
		}
		return self::$instance;
	}

	public function __construct($config)
	{
		$this->config = $config;
	}

	/**
	 * Schedules to run $task once at $time
	 *
	 * @param  string   $time        time when to fire the task, should be parsable by strtotime()
	 * @param  string   $task        task name, name of PHP class
	 * @param  string   $queue       task queue
	 * @param  array    $args        task args
	 */
	public function at($time, $task, $queue = 'default', $args = array())
	{
		$task = ORM::factory('scheduler_task')
			->values(array(
					'name' => $task,
					'queue' => $queue,
					'next_scheduled_at' => strtotime($time),
				));
		$task->set_args($args);
		$task->save();
	}

	/**
	 * Schedules to run recurring $task once at $time
	 *
	 * @param  string   $cron_line   crontab line, see 'man 5 crontab' for details
	 * @param  string   $task        task name, name of PHP class
	 * @param  string   $queue       task queue
	 * @param  array    $args        task args
	 * @param  string   $first_at    first run time for task, should be parsable by strtotime()
	 */
	public function cron($cron_line, $task, $queue = 'default', $args = array(), $first_at = null)
	{
		$task = ORM::factory('scheduler_task')
			->values(array(
					'name' => $task,
					'queue' => $queue,
					'crontab' => $cron_line,
					'next_scheduled_at' => ($first_at ? strtotime($first_at) : null),
				));
		$task->set_args($args);
		$task->save();
	}

	/**
	 * Run the task
	 *
	 * Creates in job for the PHP-Resque
	 *
	 * @param    ORM   $task
	 */
	public function run_task(ORM $task)
	{
		// Create new message in redis queue
		Resque::setBackend($this->config['redis']['backend']);
		$task->last_job_id = Resque::enqueue($task->queue, $task->name, $task->get_args(), true);
		$task->last_ran_at = $task->next_scheduled_at;
		$task->next_scheduled_at = 0;
		$task->save();
	}

	/**
	 * Get all active tasks and run them
	 */
	public function run()
	{
		$next_tasks = ORM::factory('scheduler_task')
			->where('status', '=', 'active')
			->where(DB::expr('ABS(UNIX_TIMESTAMP() - next_scheduled_at))'), '<=', $this->config->check_interval)
			->find_all();

		foreach ($next_tasks as $task)
		{
			$this->run_task($task);
		}
	}
}
<?php defined('SYSPATH') or die('No direct script access.');

Route::set('cron_scheduler_run', 'cron/scheduler/run')
	->defaults(array(
			'controller' => 'scheduler',
			'directory' => 'cron',
			'action' => 'run',
		));

Route::set('scheduler_ui', 'scheduler(/<action>(/<id>(/<arg1>)))', array('id' => '\d+'))
	->defaults(array(
			'controller' => 'scheduler',
			'action' => 'index',
		));

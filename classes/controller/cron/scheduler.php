<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Cron_Scheduler extends Controller {

	public function before()
	{
		parent::before();

		if (! Kohana::$is_cli)
		{
			// Deny none CLI access
			throw new Kohana_Exception('The requested route does not exist: :route', array(':route' => $this->request->uri));
		}
	}

	public function action_run()
	{
		Scheduler::instance()->run();
	}
}
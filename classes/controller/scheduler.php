<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Scheduler extends Controller_Template {

	public $template = 'scheduler/template.html';

	private $task = null;

	public function before()
	{
		parent::before();
		if (! Auth::instance()->logged_in())
		{
			$this->request->redirect('/');
		}
		$config = Kohana::$config->load('scheduler.assets');
		$this->template->styles = $config['stylesheets'];
		$this->template->scripts = $config['scripts'];
	}

	public function action_index()
	{
		$tasks = ORM::factory('scheduler_task')->find_all();
		$this->template->content = View::factory('scheduler/index.html');
		$this->template->content->tasks = $tasks;
	}

	public function action_show($id = null)
	{
		$this->task = $this->load_task();
		$this->template->content = View::factory('scheduler/show.html');
		$this->template->content->task = $this->task;
	}

	public function action_new($errors = null)
	{
		if (empty($this->task))
		{
			$this->task = ORM::factory('scheduler_task');
		}
		$this->template->content = View::factory('scheduler/new.html');
		$this->template->content->task = $this->task;
		$this->template->content->errors = $errors;
	}

	public function action_create()
	{
		if ($this->request->method() != 'POST')
		{
			throw new HTTP_Exception_404();
		}
		$this->task = ORM::factory('scheduler_task');
		$this->task->values(array(
				'name' => Arr::get($_POST, 'name'),
				'queue' => Arr::get($_POST, 'queue'),
				'args' => Arr::get($_POST, 'args'),
				'status' => Arr::get($_POST, 'status'),
				'created_at' => DB::expr('UNIX_TIMESTAMP()'),
		));

		if ($crontab = Arr::get($_POST, 'crontab'))
		{
			$this->task->crontab = $crontab;
		}
		else if (Arr::get($_POST, 'right_now'))
		{
			$this->task->next_scheduled_at = DB::expr('UNIX_TIMESTAMP()');
		}
		else if (Arr::get($_POST, 'next_scheduled_at'))
		{
			$this->task->next_scheduled_at = strtotime(Arr::get($_POST, 'next_scheduled_at'));
		}
		else
		{
			$this->task->status = 'suspended';
		}

		try
		{
			$this->task->save();
		}
		catch (ORM_Validation_Exception $e)
		{
			return $this->action_new($e->errors('scheduler'));
		}

		if (Arr::get($_POST, 'right_now'))
		{
			Scheduler::instance()->run_task($this->task);
		}

		$this->request->redirect('/scheduler');
	}

	public function action_edit($errors = null)
	{
		if (empty($this->task))
		{
			$this->task = $this->load_task();
		}
		$this->template->content = View::factory('scheduler/edit.html');
		$this->template->content->task = $this->task;
		$this->template->content->errors = $errors;
	}

	public function action_update()
	{
		if (! in_array($this->request->method(), array('POST', 'PUT')))
		{
			throw new HTTP_Exception_404();
		}
		$this->task = $this->load_task(Arr::get($_POST, 'id'));
		$this->task->values(array(
				'name' => Arr::get($_POST, 'name'),
				'queue' => Arr::get($_POST, 'queue', 'default'),
				'args' => Arr::get($_POST, 'args'),
				'status' => Arr::get($_POST, 'status'),
				'crontab' => Arr::get($_POST, 'crontab'),
		));

		if (Arr::get($_POST, 'right_now'))
		{
			$this->task->next_scheduled_at = DB::expr('UNIX_TIMESTAMP()');
		}
		else
		{
			$this->task->next_scheduled_at = strtotime(Arr::get($_POST, 'next_scheduled_at'));
		}

		try
		{
			$this->task->save();
		}
		catch (ORM_Validation_Exception $e)
		{
			return $this->action_edit($e->errors('scheduler'));
		}

		if (Arr::get($_POST, 'right_now'))
		{
			Scheduler::instance()->run_task($this->task);
		}

		$this->request->redirect('/scheduler');
	}

	public function action_run_now()
	{
		$this->task = $this->load_task();
		Scheduler::instance()->run_task($this->task);
		$this->request->redirect('/scheduler');
	}

	public function action_delete()
	{
		$this->task = $this->load_task(Arr::get($_POST, 'id'));
		$this->task->delete();
		$this->request->redirect('/scheduler');
	}


	/* Private methods */

	private function load_task($id = null)
	{
		if (empty($id))
		{
			$id = $this->request->param('id');
		}

		$task = ORM::factory('scheduler_task', $id);

		if (! $task->loaded())
		{
			throw new HTTP_Exception_404();
		}

		return $task;
	}
}
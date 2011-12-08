<?php defined('SYSPATH') or die('No direct script access.');

class Model_Scheduler_Task extends ORM {

	protected $_table_name = 'scheduler_tasks';

	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty'),
				array('max_length', array(':value', 100)),
			),
			'queue' => array(
				array('not_empty'),
				array('max_length', array(':value', 100)),
			),
			'args' => array(
				array('not_empty'),
				array('Model_Scheduler_Task::array_in_json_rule', array(':validation', ':field')),
			),
			'status' => array(
				array('not_empty'),
				array('in_array', array(':value', array('active', 'suspended', 'disabled'))),
			),
			'crontab' => array(
				array('max_length', array(':value', 100)),
			),
		);
	}

	public function save(Validation $validation = NULL)
	{
		$this->before_save();
		return parent::save($validation);
	}

	protected function before_save()
	{
		if (empty($this->next_scheduled_at) && !empty($this->crontab) && $this->status != 'disabled')
		{
			$this->next_scheduled_at = Cron_Manager::getNextOccurrence($this->crontab);
		}
		if (empty($this->next_scheduled_at) && empty($this->crontab))
		{
			$this->status = 'suspended';
		}
		if ($this->status == 'disabled')
		{
			$this->next_scheduled_at = 0;
		}
		if (empty($this->queue))
		{
			$this->queue = 'default';
		}
		if (empty($this->args))
		{
			$this->args = '[]';
		}
	}

	public function set_args($args = array())
	{
		$this->args = json_encode($args);
	}

	public function get_args()
	{
		return json_decode($this->args, true);
	}

	public static function array_in_json_rule(Validation $array, $field)
	{
		$value = $array[$field];

		try
		{
			$value = json_decode($value);
		}
		catch (Exception $ex)
		{
			return $array->error($field, 'not_valid_json');
		}

		if (is_null($value))
		{
			return $array->error($field, 'not_valid_json');
		}

		if (! is_array($value))
		{
			return $array->error($field, 'not_array');
		}
	}
}
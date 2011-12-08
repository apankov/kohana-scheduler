<?php defined('SYSPATH') OR die('Kohana bootstrap needs to be included before tests run');

/**
 * More tests could be found at...
 *
 * @group module
 * @group module.scheduler
 * @group module.scheduler.cron
 */
class SchedulerCronEntryTest extends UnitTest_TestCase {

	public function test_cron_entry_can_parse()
	{
		$crontab = '*/15 */6 */6 1,5 mon,wed,fri';
		$manager = new Cron_Manager();
		$parsed = Cron_Entry::parse($crontab);

		$expected = array(
			array(
				0, 15, 30, 45,
			),
			array(
				0, 6, 12, 18,
			),
			array(
				1, 7, 13, 19, 25, 31,
			),
			array(
				1, 5,
			),
			array(
				1, 3, 5,
			),
		);
		$this->assertEquals($expected, $parsed);
	}
}
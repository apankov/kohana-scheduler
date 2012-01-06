<?php

return array(
	'check_interval' => 60, // in seconds
	'redis' => array(
		'backend' => 'localhost:6379',
	),
	'assets' => array(
		'stylesheets' => array(
			'media/styles/scheduler.css',
		),
		'scripts' => array(
			'jquery' => 'media/vendor/jquery-1.6.4.min.js',
			'media/javascript/scheduler.js',
		),
	),
);

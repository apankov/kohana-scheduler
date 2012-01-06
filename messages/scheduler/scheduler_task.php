<?php

return array(
	'name' => array(
		'not_empty' => 'cannot be empty',
	),
	'queue' => array(
		'not_empty' => 'cannot be empty',
	),
	'args' => array(
		'not_empty' => 'cannot be empty',
		'not_valid_json' => 'should be valid JSON (parsable by PHP json_decode())',
		'not_array' => 'should be JSON encoded hash ( { ... } )',
	),
);

<?php

/*$arr = [
	[
		'message' => 'xxxxxxxxxxxxxxxx',
		'from' => 'aaa',
		'time' => '2017-06-09'
	],
];*/

$arr = [
	[
		'schedule' => 'xxxxxxxxxxxxxxxx',
		'time' => '2017-06-09'
	],
	[
		'schedule' => 'aaaaaaaaaaaa',
		'time' => '2017-06-10'
	],
	[
		'schedule' => 'aaaaaaaaaaaa',
		'time' => '2017-06-10 12:10'
	],
];


echo serialize($arr);
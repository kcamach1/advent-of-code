<?php

$options = getopt('d::y::t', [
	'day::',
	'year::',
	'test'
]);

// if no options provided, give instructions
$current_year = date('Y');
if (empty($options)) {
	echo 'Required parameters:';
	echo PHP_EOL;
	echo '--day (-d)  Day of puzzle. Value should be between 1 and 25.';
	echo PHP_EOL;
	echo "--year (-y) Year of puzzle. Value should be between 2015 and $current_year.";
	echo PHP_EOL;
	echo 'Optional flags:';
	echo '--test (-t) Toggle test mode';
	die;
}

// merge to avoid array key errors
$options = array_merge([
	'd' => null,
	'y' => null,
	'day' => null,
	'year' => null,
	't' => null,
	'test' => null,
], $options);

$day = $options['day'] ?? $options['d'];
$year = $options['year'] ?? $options['y'];

// flags with no value input are weird. if input
// includes the flag, value from getopt() is always false.
// $test is true if either flag is included in input,
// false otherwise.
$test = !($options['test'] ?? $options['t'] ?? true);

// day validation
if ($day === null) {
	echo 'Missing option -d or --day. Value should be between 1 and 25.';
	die;
}
$day_int = (int) $day;
if ($day_int < 1 ||  $day_int > 25) {
	echo "Invalid day value $day. Value should be between 1 and 25.";
	die;
}

// year validation
if ($year === null) {
	echo "Missing option -y or --year. Value should be between 2015 and $current_year.";
	die;
}
$year_int = (int) $year;
if ($year_int < 2015 || $year_int > (int) $current_year) {
	echo "Invalid year value $year. Value should be between 2015 and $current_year";
	die;
}

require_once __DIR__ . '/AdventOfCode.php';

// load php files from year folder
foreach (glob(__DIR__ . '/' . $year . '/*.php') as $file) {
	include $file;
}

// get subclasses of AdventOfCode
$classes = array_filter(get_declared_classes(), function (string $class) {
	return is_subclass_of($class, 'AdventOfCode');
});

// turn strings into classes
$classes = array_map(function(string $class) {
	return (new $class());
}, $classes);

$days = array_map(function (AdventOfCode $class) {
	return $class->get_day();
}, $classes);

// int $day => AdventOfCode $class
$classes = array_combine($days, $classes);

if (!array_key_exists($day_int, $classes)) {
	echo "No puzzle found for Advent of Code $year day $day.";
	die;
}

// get object with appropriate $day value
$puzzle = $classes[$day_int];
if ($test) {
	$puzzle->test = true;
}
$puzzle->solve();




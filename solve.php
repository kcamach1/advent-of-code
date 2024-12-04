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
$class_names = array_filter(get_declared_classes(), function (string $class) {
	return is_subclass_of($class, 'AdventOfCode');
});

// turn strings into classes
$classes = array_map(function(string $class) {
	return (new $class());
}, $class_names);

$days = array_map(function (AdventOfCode $class) {
	return $class->get_day();
}, $classes);

$day_keys = array_keys($days, $day_int);

// no class for day/year combo
if (count($day_keys) === 0) {
	echo "No puzzle found with \$day = $day in $year directory.";
	die;
}

// two classes with same day/year
if (count($day_keys) > 1) {
	echo "Multiple classes found in $year directory with \$day = $day. Remove one and try again.";
	foreach ($day_keys as $key) {
		echo PHP_EOL;
		echo $class_names[$key];
	}
	die;
}

// key to get correct values from $days, $classes, and $class_names
$key = array_shift($day_keys);

// get object with appropriate $day value
$puzzle = $classes[$key];

// handle if $year property doesn't match folder where the class lives.
// ex: class is in 2024 folder but has $year = 2023
$actual_year = $puzzle->get_year();
if ($actual_year !== $year_int) {
	$class_name = $class_names[$key];
	echo "$class_name in $year folder has \$year = $actual_year.";
	echo PHP_EOL;
	echo "Either move this class to the $actual_year directory or update to set \$year = $year.";
	die;
}

if ($test) {
	$puzzle->test = true;
}
$puzzle->solve();




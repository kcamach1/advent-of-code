<?php

$options = getopt('d::y::t::', [
	'day::',
	'year::',
	'title::'
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
	echo "--title (-t) Title of puzzle. Value should not start with a number.";
	die;
}

// merge to avoid array key errors
$options = array_merge([
	'd' => null,
	'y' => null,
	't' => null,
	'day' => null,
	'year' => null,
	'title' => null,
], $options);

$day = $options['d'] ?? $options['day'];
$year = $options['y'] ?? $options['year'];
$title = $options['t'] ?? $options['title'];

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

// title validation
if ($title === null) {
	echo 'Missing option -t or --title. Title cannot start with a number';
	die;
}
if (is_numeric($title[0])) {
	echo "Invalid title value $title. Title cannot start with a number.";
	die;
}

$filepath =  __DIR__ . '/' . $year;

require_once __DIR__ . '/AdventOfCode.php';
// load php files from year folder
// need to check that class for year/day doesn't already exist
foreach (glob($filepath . '/*.php') as $file) {
	include $file;
}

// get subclasses of AdventOfCode
$class_names = array_filter(get_declared_classes(), function (string $class) {
	return is_subclass_of($class, 'AdventOfCode');
});

// stop if class with year/day combo already exists
foreach ($class_names as $class_name) {
	if ((new $class_name())->get_day() === $day_int) {
		echo "Class $class_name with \$day = $day already exists in $year directory";
		die;
	}
}

// remove non-alphanumeric characters from title to get class name
$class_name = preg_replace('/[^a-zA-Z0-9]/', '', $title);

// use template to build class file
$template = file_get_contents(__DIR__ . '/template.txt');
$template = str_replace('<day>', $day, $template);
$template = str_replace('<year>', $year, $template);
$template = str_replace('<title>', $title, $template);
$template = str_replace('<class_name>', $class_name, $template);

$filename = $filepath . '/' . $class_name . '.php';

// make sure directory for file exists
if (!is_dir($filepath)) {
	mkdir($filepath, 0777, true);
}

// create puzzle class file
file_put_contents($filename, $template);

echo 'New puzzle file created at ' . str_replace(__DIR__ . '/', '', $filename);

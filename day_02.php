<?php
// https://adventofcode.com/2024/day/2

function get_reports(): array
{

	$ch = curl_init();

	$url = 'https://adventofcode.com/2024/day/2/input';

	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 80);

	// get session from developer console in browser
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
	'Cookie: session=' . getenv('AOC_SESSION')
	]);

	$response = curl_exec($ch);

	if (curl_error($ch)) {
	echo 'Request Error:' . curl_error($ch);
	curl_close($ch);
	die;
	}

	curl_close($ch);

	// turn puzzle input into array of strings like "8 11 13 14 15 18 17"
	$inputs = explode("\n", trim($response));

	// split each string into array of integers
	return array_map(function(string $input) {
		$array = explode(' ', $input);
		return array_map('intval', $array);
	}, $inputs);

}

function is_safe(array $levels): bool
{
	$level_diffs = [];
	foreach($levels as $key => $level) {
		if ($key !== 0) {
			$level_diffs[] = $level - $levels[$key - 1];
		}
	}

	$direction = null;

	foreach ($level_diffs as $level_diff) {
		$abs_level_diff = abs($level_diff);

		// diff is at least 1 and at most 3
		if ($abs_level_diff < 1 || $abs_level_diff > 3 ) {
			return false;
		}

		// levels all increasing or all decreasing
		if ($direction === null) {
			// set $direction to 1 or -1 based on 1st level diff
			$direction = $level_diff / $abs_level_diff;
		}

		// either $level_diff or $direction are neg, but not both
		if ($level_diff * $direction < 0) {
			return false;
		}
	}

	return true;
}

function is_safe_dampened(array $report): bool
{
	$is_safe = is_safe($report);

	// if it's already safe, do nothing
	if ($is_safe) {
		return true;
	}

	$alt_reports = [];

	foreach ($report as $key => $level)
	{
		$new_report = $report;
		unset($new_report[$key]);
		$alt_reports[] = array_values($new_report);
	}

	$safe_alt_reports = array_filter($alt_reports, 'is_safe');

	// 0 = false, otherwise true
	return (bool) count($safe_alt_reports);
}

function total_safe_reports(array $reports, $dampened = false): int
{
	$callback = $dampened ? 'is_safe_dampened' : 'is_safe';

	return count(array_filter($reports, $callback));
}

function red_nosed_reports(): void
{
	$reports = get_reports();

	$total_safe_reports = total_safe_reports($reports);
	$total_safe_reports_dampened = total_safe_reports($reports, true);
	echo $total_safe_reports;
	echo "\n";
	echo $total_safe_reports_dampened;
}

red_nosed_reports();
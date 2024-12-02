<?php

// https://adventofcode.com/2024/day/1

function get_lists(): array
{

	$ch = curl_init();

	$url = 'https://adventofcode.com/2024/day/1/input';

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

	// turn puzzle input into array of strings like "84283   63343"
	$pairs = explode("\n", trim($response));

	// split array of strings into two arrays of integers
	$lists = [];
	foreach ($pairs as $pair ) {
		$pair = explode('   ', $pair);

		$lists[0][] = (int) $pair[0];
		$lists[1][] = (int) $pair[1];
	}

	return $lists;
}

function total_distance($list_one, $list_two): int
{
	sort($list_one, SORT_NUMERIC);
	sort($list_two, SORT_NUMERIC);

	$total_distance = 0;

	array_map(function(int $location_one, int $location_two) use (&$total_distance) {
		$total_distance += abs($location_one - $location_two);
	}, $list_one, $list_two);

	return $total_distance;
}

function total_similarity_score($list_one, $list_two)
{
	// keys are locations, values are frequencies
	$frequencies = array_count_values($list_two);

	$total_similarity_score = 0;

	array_map(function(int $location) use ($frequencies, &$total_similarity_score) {
		if (array_key_exists($location, $frequencies)) {
			$total_similarity_score += ($location * $frequencies[$location]);
		}
		// if array key doesn't exist, do nothing (similarity score is zero)
	}, $list_one);

	return $total_similarity_score;
}

function historian_hysteria(): void
{
	$lists = get_lists();

	echo total_distance(...$lists);
	echo "\n";
	echo total_similarity_score(...$lists);
}

historian_hysteria();
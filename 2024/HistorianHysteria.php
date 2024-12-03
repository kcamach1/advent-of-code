<?php

require_once dirname(__DIR__) . '/AdventOfCode.php';

class HistorianHysteria extends AdventOfCode
{
	protected int $day = 1;
	protected int $year = 2024;
	protected string $title = 'Historian Hysteria';
	protected array $lists = [];

	protected function get_lists(): array
	{
		if (!empty($this->lists)) {
			return $this->lists;
		}

		$puzzle_input = $this->get_input();

		// turn puzzle input into array of strings like "84283   63343"
		$pairs = explode(PHP_EOL, trim($puzzle_input));

		// split array of strings into two arrays of integers
		foreach ($pairs as $pair) {
			$pair = explode('   ', $pair);

			$this->lists[0][] = (int) $pair[0];
			$this->lists[1][] = (int) $pair[1];
		}

		return $this->lists;
	}

	protected function total_distance($list_one, $list_two): int
	{
		sort($list_one, SORT_NUMERIC);
		sort($list_two, SORT_NUMERIC);

		$total_distance = 0;

		array_map(function(int $location_one, int $location_two) use (&$total_distance) {
			$total_distance += abs($location_one - $location_two);
		}, $list_one, $list_two);

		return $total_distance;
	}

	protected function total_similarity_score($list_one, $list_two): int
	{
		// keys are location IDs, values are frequencies
		$frequencies = array_count_values($list_two);

		$total_similarity_score = 0;

		array_map(function(int $location) use ($frequencies, &$total_similarity_score) {
			if (array_key_exists($location, $frequencies)) {
				$total_similarity_score += ($location * $frequencies[$location]);
			}
		}, $list_one);

		return $total_similarity_score;
	}

	protected function solve_part_one(): string
	{
		$this->get_lists();
		return (string) $this->total_distance(...$this->lists);
	}

	protected function solve_part_two(): string
	{
		$this->get_lists();
		return (string) $this->total_similarity_score(...$this->lists);
	}
}

$puzzle = new HistorianHysteria();
$puzzle->solve();
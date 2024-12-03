<?php

require_once dirname(__DIR__) . '/AdventOfCode.php';

class RedNosedReports extends AdventOfCode
{
	protected int $day = 2;
	protected int $year = 2024;
	protected string $title = 'Red-Nosed Reports';
	protected array $reports;

	protected function get_reports(): array
	{
		if (isset($this->reports)) {
			return $this->reports;
		}

		$puzzle_input = $this->get_input();

		// turn puzzle input into array of strings like "8 11 13 14 15 18 17"
		$inputs = explode(PHP_EOL, trim($puzzle_input));

		// split each string into array of integers
		$this->reports = array_map(function (string $input) {
			$array = explode(' ', $input);
			return array_map('intval', $array);
		}, $inputs);

		return $this->reports;
	}

	protected function is_safe(array $report): bool
	{
		$level_diffs = [];

		// need incrementing keys for foreach loop to work
		$report = array_values($report);

		foreach ($report as $key => $level) {
			if ($key !== 0) {
				$level_diffs[] = $level - $report[$key - 1];
			}
		}

		$direction = null;

		// check each level diff for a problem
		foreach ($level_diffs as $level_diff) {
			$abs_level_diff = abs($level_diff);

			// diff is at least 1 and at most 3
			if ($abs_level_diff < 1 || $abs_level_diff > 3) {
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

	protected function is_safe_dampened(array $report): bool
	{
		// if it's already safe, do nothing
		if ($this->is_safe($report)) {
			return true;
		}

		$alt_reports = [];

		foreach ($report as $key => $level) {
			$new_report = $report;
			unset($new_report[$key]);
			$alt_reports[] = $new_report;
		}

		// check if new reports are safe
		$safe_alt_reports = array_filter($alt_reports, [$this, 'is_safe']);

		// 0 = false, greater than 0 = true
		return (bool) count($safe_alt_reports);
	}

	protected function solve_part_one(): string
	{
		$this->get_reports();
		return (string) count(array_filter($this->reports, [$this, 'is_safe']));
	}

	protected function solve_part_two(): string
	{
		$this->get_reports();
		return (string) count(array_filter($this->reports, [$this, 'is_safe_dampened']));
	}
}

$puzzle = new RedNosedReports();
$puzzle->solve();
<?php

require_once dirname(__DIR__) . '/AdventOfCode.php';

class MullItOver extends AdventOfCode
{
	protected int $day = 3;
	protected int $year = 2024;
	protected string $title = 'Mull It Over';

	// array of pairs of integers in mul()
	protected function get_mul_pairs(?string $input = null): array
	{
		if ($input === null) {
			$input = $this->get_input();
		}

		preg_match_all('/mul\((\d*),(\d*)\)/', $input, $matches);

		// convert strings to integers
		$first_params = array_map('intval', $matches[1]);
		$second_params = array_map('intval', $matches[2]);

		return array_map(function (int $first_param, int $second_param) {
			return [$first_param, $second_param];
		}, $first_params, $second_params);

	}

	protected function get_enabled_mul_pairs(): array
	{
		// explicitly flag enabled at start and disabled at end to make regex easier
		$input = 'do()' . $this->get_input() . 'don\'t()';

		// input has newlines between do() and don't(), so .* doesn't work, need [\s\S]*
		preg_match_all('/do\(\)([\s\S]*?)don\'t\(\)/', $input, $matches);
		$enabled_parts = $matches[1];

		$pairs = [];

		// get mul pairs for each part between a do() and a don't()
		foreach ($enabled_parts as $part) {
			array_push($pairs, ...$this->get_mul_pairs($part));
		}

		return $pairs;

	}

	// $pair should be array of 2 integers
	protected function multiply_pair(array $pair): int
	{
		// reset keys just in case
		$pair = array_values($pair);

		return $pair[0] * $pair[1];
	}

	protected function solve_part_one(): string
	{
		$pairs = $this->get_mul_pairs();
		return (string) array_sum(array_map([$this, 'multiply_pair'], $pairs));
	}

	protected function solve_part_two(): string
	{
		$pairs = $this->get_enabled_mul_pairs();
		return (string) array_sum(array_map([$this, 'multiply_pair'], $pairs));
	}
}

$puzzle = new MullItOver();
$puzzle->solve();
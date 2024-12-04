<?php

abstract class AdventOfCode
{
	protected int $day;
	protected int $year;
	protected string $title;
	public bool $test = false;

	abstract protected function solve_part_one(): string;

	// get input from file or website
	protected function get_input(): string
	{
		$type = $this->test ? 'test' : 'puzzle';
		$filepath =  __DIR__ . '/' . $this->year . '/' . $type . '_inputs';
		$filename = $filepath . '/day_' . sprintf('%02d', $this->day) . '.txt';

		// don't re-fetch data if it's already saved
		if (file_exists($filename)) {
			return file_get_contents($filename);
		}

		// if file doesn't exist, get data from website
		$input = $this->test ? $this->get_test_input() : $this->get_puzzle_input();

		// make sure directory for file exists
		if (!is_dir($filepath)) {
			mkdir($filepath, 0777, true);
		}

		// write data to file to avoid unnecessary re-requests
		file_put_contents($filename, $input);

		return $input;
	}

	// get puzzle input with curl
	protected function get_puzzle_input(): string
	{
		if ($this->test) {
			return $this->get_test_input();
		}

		$filepath =  __DIR__ . '/' . $this->year . '/puzzle_inputs';
		$filename = $filepath . '/day_' . sprintf('%02d', $this->day) . '.txt';

		// don't re-fetch data if it's already saved
		if (file_exists($filename)) {
			return file_get_contents($filename);
		}

		// get puzzle input from website
		// using session cookie with curl
		$session = getenv('AOC_SESSION');
		if (!$session) {
			throw new Exception('AOC_SESSION environment variable not found.');
		}

		$ch = curl_init();

		$url = 'https://adventofcode.com/' . $this->year . '/day/' . $this->day . '/input';

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 80);

		// get session from developer console in browser
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Cookie: session=' . $session
		]);

		$response = curl_exec($ch);

		if (curl_error($ch)) {
			echo 'Request Error:' . curl_error($ch);
			curl_close($ch);
			die;
		}

		curl_close($ch);

		return $response;
	}

	// get test input from website
	protected function get_test_input(): string
	{
		// get example inputs from website
		$html_string = file_get_contents('https://adventofcode.com/' . $this->year . '/day/' . $this->day);
		$dom = new DOMDocument();
		// suppress warnings about invalid tags
		@$dom->loadHTML($html_string);

		// assume the example input is the longest <code> block on the page
		$code_nodes = $dom->getElementsByTagName('code');
		$code_contents = [];
		foreach ($code_nodes as $node) {
			$code_contents[] = $node->textContent;
		}

		usort($code_contents, function ($a, $b) {
			return strlen($b)-strlen($a);
		});

		// text content from longest <code>
		return $code_contents[0];
	}

	// need getter because day is protected
	// to prevent it from being changed
	public function get_day(): int
	{
		return $this->day;
	}

	// runs solve_part_one() and, if it exists, solve_part_two()
	public function solve(): void
	{
		echo $this->title;

		if ($this->test) {
			echo ' (test)';
		}

		echo PHP_EOL;

		echo 'Part 1: ' . $this->solve_part_one();

		if (method_exists($this, 'solve_part_two')) {
			echo PHP_EOL;
			echo 'Part 2: ' . $this->solve_part_two();
		}
	}
}
<?php

abstract class AdventOfCode
{
	protected int $day;
	protected int $year;
	protected string $title;
	protected bool $test;

	public function __construct(bool $test = false)
	{
		$this->test = $test;
	}

	abstract protected function setup(): void;
	abstract protected function solve_part_one(): string;
	// solve_part_two() is optional

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

	protected function get_puzzle_input(): string
	{
		// session required for puzzle inputs
		$session = getenv('AOC_SESSION');
		if (!$session) {
			throw new Exception('AOC_SESSION environment variable not found.');
		}

		$url = 'https://adventofcode.com/' . $this->year . '/day/' . $this->day . '/input';

		return $this->aoc_request($url, $session);
	}

	protected function get_test_input(): string
	{
		// get html of puzzle page
		$html_string = $this->aoc_request('https://adventofcode.com/' . $this->year . '/day/' . $this->day);
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

	// curl request to adventofcode.com
	// configured to follow AoC automation guidelines
	protected function aoc_request(string $url, ?string $session = null): string
	{
		$timestamp_filename = __DIR__ . '/timestamp.txt';
		$last_request_time = 0;
		$now = time();
		if (file_exists($timestamp_filename)) {
			$last_request_time = (int) file_get_contents($timestamp_filename);
		}
		$elapsed = $now - $last_request_time;

		// Wait at least 5 minutes between requests. This
		// is required by AoC guidelines. Do not remove
		// timestamp.txt to bypass the throttle.
		if ($elapsed < 300) {
			echo 'Could not complete request to ' . $url;
			echo PHP_EOL;
			echo 'Please wait ' . (300 - $elapsed) . ' seconds before sending another request to adventofcode.com';
			die;
		}

		file_put_contents($timestamp_filename, $now);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 80);

		// required by AoC guidelines
		curl_setopt($ch, CURLOPT_USERAGENT, 'github.com/kcamach1/advent-of-code by contact@kcamach1.dev');

		if ($session) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Cookie: session=' . $session
			]);
		}

		$response = curl_exec($ch);

		if (curl_error($ch)) {
			echo 'Request Error:' . curl_error($ch);
			curl_close($ch);
			die;
		}

		curl_close($ch);

		return $response;
	}

	// need getter because day is protected
	// to prevent it from being changed
	public function get_day(): int
	{
		return $this->day;
	}

	// need getter because year is protected
	// to prevent it from being changed
	public function get_year(): int
	{
		return $this->year;
	}

	// runs solve_part_one() and, if it exists, solve_part_two()
	public function solve(): void
	{
		echo $this->title;

		if ($this->test) {
			echo ' (test)';
		}

		echo PHP_EOL;

		$this->setup();

		echo 'Part 1: ' . $this->solve_part_one();

		if (method_exists($this, 'solve_part_two')) {
			echo PHP_EOL;
			echo 'Part 2: ' . $this->solve_part_two();
		}
	}
}
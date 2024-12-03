<?php

abstract class AdventOfCode
{
	protected int $day;
	protected int $year;
	protected string $title;
	public bool $test = false;

	abstract public function solve_part_one(): void;
	abstract public function solve_part_two(): void;

	public function __construct()
	{
		if (isset($this->title))
		{
			$this->echo_line($this->title);
		}
	}

	// get puzzle input from file or with curl if file doesn't exist
	protected function get_puzzle_input(): string
	{
		$filepath =  __DIR__ . '/puzzle_inputs/' . $this->year;

		if ($this->test) {
			$filepath = str_replace('puzzle', 'test', $filepath);
		}

		$filename = $filepath . '/day_' . sprintf('%02d', $this->day) . '.txt';

		// don't re-fetch data if it's already saved
		if (file_exists($filename)) {
			return file_get_contents($filename);
		}

		// $this->test = true but file doesn't exist
		if ($this->test) {
			throw new Exception('Missing test data. Add test data to file at ' . $filename);
		}

		// get the cookie value from dev tools
		$session = getenv('AOC_SESSION');
		if (!$session) {
			throw new Exception('Error: AOC_SESSION environment variable not found.');
		}

		// if $this->test = false and we don't already have the inputs,
		// use curl to get them
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

		// save data to file to limit unnecessary requests
		if (!is_dir($filepath)) {
			mkdir($filepath, 0777, true);
		}
		file_put_contents($filename, $response);

		return $response;
	}

	// literally just adds PHP_EOL after echo
	protected function echo_line(string $output): void
	{
		echo $output;
		echo PHP_EOL;
	}

}
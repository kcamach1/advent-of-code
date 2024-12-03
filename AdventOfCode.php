<?php

abstract class AdventOfCode
{
	protected int $day;
	protected int $year;
	protected string $title;
	public bool $test = false;

	abstract public function solve_part_one(): void;

	public function __construct()
	{
		if (isset($this->title)) {
			$this->echo_line($this->title);
		}
	}

	// get puzzle input from file or with curl if file doesn't exist
	protected function get_puzzle_input(): string
	{
		$filepath =  __DIR__ . '/' . $this->year . '/puzzle_inputs';

		if ($this->test) {
			$filepath = str_replace('puzzle', 'test', $filepath);
		}

		$filename = $filepath . '/day_' . sprintf('%02d', $this->day) . '.txt';

		// don't re-fetch data if it's already saved
		if (file_exists($filename)) {
			return file_get_contents($filename);
		}

		// make sure directory for file exists
		if (!is_dir($filepath)) {
			mkdir($filepath, 0777, true);
		}

		// if $this->test = true but file doesn't exist,
		// get example inputs from website
		if ($this->test) {
			// don't need session for example inputs
			$html_string = file_get_contents('https://adventofcode.com/' . $this->year . '/day/' . $this->day);
			$dom = new DOMDocument();
			// suppress warnings about invalid tags
			@$dom->loadHTML($html_string);

			// text content of first <code> element on page
			$example_input = $dom->getElementsByTagName('code')
				->item(0)
				->textContent;

			// save data to file to limit unnecessary requests
			file_put_contents($filename, $example_input);
			return $example_input;
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

		// save data to file to limit unnecessary requests
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
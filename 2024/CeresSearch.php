<?php

require_once dirname(__DIR__) . '/AdventOfCode.php';

class CeresSearch extends AdventOfCode
{
	protected int $day = 4;
	protected int $year = 2024;
	protected string $title = "Ceres Search";
	protected array $rows;
	protected int $num_columns;
	protected int $num_rows;

	protected function setup(): void
	{
		// array of strings (each string is a row)
		$this->rows = explode(PHP_EOL, trim($this->get_input()));
		$this->num_columns = strlen($this->rows[0]);
		$this->num_rows = count($this->rows);
	}

	protected function has_xmas($row, $column, $x_direction, $y_direction): bool
	{
		// avoid array key errors
		if ($column > ($this->num_columns - (4 * $x_direction))) {
			return false;
		}
		if ($column < 0 - (3 * $x_direction)) {
			return false;
		}
		if ($row > ($this->num_rows - (4 * $y_direction))) {
			return false;
		}
		if ($row < 0 - (3 * $y_direction)) {
			return false;
		}

		// start at X, find XMAS
		if ($this->rows[$row][$column] !== 'X') {
			return false;
		}
		if ($this->rows[$row + $y_direction][$column + $x_direction] !== 'M') {
			return false;
		}
		if ($this->rows[$row + (2 * $y_direction)][$column + (2 * $x_direction)] !== 'A') {
			return false;
		}
		if ($this->rows[$row + (3 * $y_direction)][$column + (3 * $x_direction)] !== 'S') {
			return false;
		}
		return true;
	}

	// check if an A is the center of an x-mas
	protected function has_x_mas(int $row, int $column): bool
	{
		// only look for A's
		if ($this->rows[$row][$column] !== 'A') {
			return false;
		}

		// A cannot be on any of the edges
		if ($column < 1 || $column > $this->num_columns - 2) {
			return false;
		}
		if ($row < 1 || $row > $this->num_rows - 2) {
			return false;
		}

		$corners = $this->rows[$row - 1][$column - 1];
		$corners .= $this->rows[$row - 1][$column + 1];
		$corners .= $this->rows[$row + 1][$column - 1];
		$corners .= $this->rows[$row + 1][$column + 1];

		$options = ['MMSS', 'SSMM', 'SMSM', 'MSMS'];

		if (!in_array($corners, $options)) {
			return false;
		}

		return true;
	}

	protected function solve_part_one(): string
	{
		$total = 0;

		for ($row = 0; $row < $this->num_rows; $row++) {
			for ($column = 0; $column < $this->num_columns; $column++) {
				// horizontal
				$total += $this->has_xmas($row, $column, 1, 0);
				$total += $this->has_xmas($row, $column, -1, 0);
				// vertical
				$total += $this->has_xmas($row, $column, 0, 1);
				$total += $this->has_xmas($row, $column, 0, -1);
				// diagonal
				$total += $this->has_xmas($row, $column, 1, 1);
				$total += $this->has_xmas($row, $column, -1, -1);
				$total += $this->has_xmas($row, $column, -1, 1);
				$total += $this->has_xmas($row, $column, 1, -1);
			}
		}
	    return (string) $total;
	}

	protected function solve_part_two(): string
	{
		$total = 0;

		for ($row = 0; $row < $this->num_rows; $row++) {
			for ($column = 0; $column < $this->num_columns; $column++) {
				$total += $this->has_x_mas($row, $column);
			}
		}
		return (string) $total;
	}
}
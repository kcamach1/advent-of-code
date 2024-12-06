<?php

require_once dirname(__DIR__) . '/AdventOfCode.php';

class PrintQueue extends AdventOfCode
{
	protected int $day = 5;
	protected int $year = 2024;
	protected string $title = "Print Queue";
	protected array $order_rules;
	protected array $updates;

    // runs before solve_part_one() and solve_part_two()
    protected function setup(): void
    {
        $input = $this->get_input();

		// all the pages are 2-digit numbers
		preg_match_all('/(\d{2})\|(\d{2})/', $input, $matches);

		$this->order_rules = array_map(function (string $before, string $after) {
			return [
				'before' => $before,
				'after' => $after
			];
		}, $matches[1], $matches[2]);

		preg_match_all('/((\d{2}),)+(\d{2})/', $input, $matches);

		// array of strings like '75,47,61,53,29'
		$this->updates = $matches[0];
    }

	protected function is_in_order(string $update): bool
	{
		$pages = explode(',', $update);

		$pages = array_map(function(int $page_key, string $page) use ($pages) {
			$before = array_filter($pages, function (int $key) use ($page_key) {
				return $key < $page_key;
			},ARRAY_FILTER_USE_KEY);

			$after = array_filter($pages, function (int $key) use ($page_key) {
				return $key > $page_key;
			},ARRAY_FILTER_USE_KEY);

			return [
				'page' => $page,
				'before' => $before,
				'after' => $after
			];
		}, array_keys($pages), $pages);

		foreach ($pages as ['page' => $page, 'before' => $before, 'after' => $after]) {
			$page_rules = $this->page_rules($page);

			// pages are before that must be after
			if (!empty(array_intersect($before, $page_rules['after']))) {
				return false;
			}

			// pages are after that must be before
			if (!empty(array_intersect($after, $page_rules['before']))) {
				return false;
			}
		}

		return true;
	}

	protected function needs_swap(string $first, string $second): bool
	{
		if (in_array(['before' => $second, 'after' => $first], $this->order_rules)) {
			return true;
		}
		return false;
	}

	protected function reorder(string $update): string
	{
		if ($this->is_in_order($update)) {
			return $update;
		}

		$pages = explode(',', $update);

		for ($i = 0; $i < count($pages); $i++) {
			for ($j = $i + 1; $j < count($pages); $j++) {
				$first = $pages[$i];
				$second = $pages[$j];
				if ($this->needs_swap($first, $second)) {
					$pages[$i] = $second;
					$pages[$j] = $first;
				}
			}
		}

		return $this->reorder(implode(',', $pages));
	}

	protected function page_rules(string $page): array
	{
		$before_rules = array_filter($this->order_rules, function (array $rule) use ($page) {
			return $rule['after'] === $page;
		});

		$after_rules = array_filter($this->order_rules, function (array $rule) use ($page) {
			return $rule['before'] === $page;
		});

		$before_rules = array_map(function (array $rule) {
			return $rule['before'];
		}, $before_rules);

		$after_rules = array_map(function (array $rule) {
			return $rule['after'];
		}, $after_rules);

		// pages that must be before and pages that must be after
		return [
			'before' => $before_rules,
			'after' => $after_rules
		];
	}

	protected function middle_page(string $update): int
	{
		$pages = explode(',', $update);
		$num_pages = count($pages);
		return (int) $pages[floor($num_pages/2)];
	}

	protected function solve_part_one(): string
	{
	    $updates_in_order = array_filter($this->updates, [$this, 'is_in_order']);

		$middle_pages = array_map([$this, 'middle_page'] ,$updates_in_order);

		return (string) array_sum($middle_pages);
	}

	protected function solve_part_two(): string
	{
		$reordered_middle_pages = array_map(function(string $update) {
			// don't add updates that are already in order
			if ($this->is_in_order($update)) {
				return 0;
			}

			return $this->middle_page($this->reorder($update));

		}, $this->updates);

		return (string) array_sum($reordered_middle_pages);
	}
}
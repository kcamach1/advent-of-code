# Advent of Code
Solutions to puzzles from https://adventofcode.com/.

## System Requirements
I am running this code with PHP 8.3 on a Mac. YMMV with other setups.

## Getting Puzzle Inputs
By default, the puzzle inputs are saved to the filesystem after the initial request to the AoC site. This is done to be kind to Eric Wastl's servers and not re-request data that isn't going to change. 

The request requires a `session` cookie from the AOC website. To configure:
1. Log in to https://adventofcode.com/.
2. Use the developer tools in your browser to get the value of the `session` cookie for the AoC website.
    <img width="630" alt="Screenshot of developer tools Application tab for Chrome. Cookies section is open, showing a cookie named 'session' for domain .adventofcode.com" src="https://github.com/user-attachments/assets/7c4a8663-9bd1-4e29-96f6-d3259afa7130">
3. Save the value of the cookie to an environment variable named `AOC_SESSION`.
   ```bash
   export AOC_SESSION=536....
   ```
If you don't want to set an `AOC_SESSION` environment variable, you can manually create the files in `path/to/repo/<year>/puzzle_inputs`. Filenames should look like `day_01.txt`. 

## Test Mode
Set the `$test` property on an `AdventOfCode` subclass to `true` to test your solution against the example inputs from the AoC website.
```php
$puzzle = new HistorianHysteria(); // day 1 2024
$puzzle->test = true;
$puzzle->solve_part_one();
```
```bash
# output
Historian Hysteria
Part 1: 11
```

Like the puzzle inputs, test inputs are saved to the filesystem after the initial request to the AoC site. Test inputs are assumed to be in the first `<code>` block on the puzzle page. The `AOC_SESSION` environment variable is not necessary for this request.

Alternately, you can manually create the files in `path/to/repo/<year>/test_inputs` and fill with the appropriate data. Filenames should look like `day_01.txt`. 

## Add a Puzzle
Use the `new_puzzle.php` script to generate new puzzle files.
```bash
# example
php new_puzzle.php --year=2024 --day=1 --title="Historian Hysteria"
```
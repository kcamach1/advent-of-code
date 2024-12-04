# Advent of Code
Solutions to puzzles from https://adventofcode.com.

## System Requirements
I am running this code with PHP 8.3 on a Mac. YMMV with other setups.

## Getting Puzzle Inputs
By default, the puzzle inputs are saved to the filesystem after the initial request to the AoC site.

The request requires a `session` cookie from the AOC website. To configure:
1. Log in to https://adventofcode.com.
2. Use the developer tools in your browser to get the value of the `session` cookie for the AoC website.
    <img width="630" alt="Screenshot of developer tools Application tab for Chrome. Cookies section is open, showing a cookie named 'session' for domain .adventofcode.com" src="https://github.com/user-attachments/assets/55ba3ffc-4074-48cc-8fd0-1e43d8b5552f">

3. Save the value of the cookie to an environment variable named `AOC_SESSION`.
   ```bash
   export AOC_SESSION=536....
   ```
If you don't want to set an `AOC_SESSION` environment variable, you can manually create the files in `path/to/repo/<year>/puzzle_inputs`. Filenames should look like `day_01.txt`. 

## Add a Puzzle
Use the `new_puzzle.php` script to generate new puzzle files.
```bash
php new_puzzle.php --year=2024 --day=1 --title="Historian Hysteria"
```

## Run Solvers
Use the `solve.php` script to run the solver for a given day and year.
```bash
php solve.php --year=2024 --day=1       
```

### Test Mode
Use the `--test` flag to test a solver against the example inputs from the AoC website.
```bash
php solve.php --year=2024 --day=1 --test
```
```bash
# output
Historian Hysteria (test)
Part 1: 11
Part 2: 31
```
Like the puzzle inputs, test inputs are saved to the filesystem after the initial request to the AoC site. Test inputs are assumed to be in the largest `<code>` block on the puzzle page. This request does not use the `AOC_SESSION` environment variable.

Alternately, you can manually create the files in `path/to/repo/<year>/test_inputs` and fill them with the appropriate data. Filenames should look like `day_01.txt`. 

## Community Compliance
This repo follows the automation guidelines on the /r/adventofcode [community wiki](https://www.reddit.com/r/adventofcode/wiki/faqs/automation). 
- [x] Outbound calls are throttled to one every 5 minutes 
    - `aoc_request()` in `AdventOfCode`
- [x] Once inputs are downloaded, they are cached locally
    - `get_input()` in `AdventOfCode`
- [x] The User-Agent header includes the url of this GitHub repo and my email address
    - `aoc_request()` in `AdventOfCode`

This repo also follows the redistribution guidelines from https://adventofcode.com/about.
- [x] Does not include puzzle text
- [x] Does not include puzzle inputs

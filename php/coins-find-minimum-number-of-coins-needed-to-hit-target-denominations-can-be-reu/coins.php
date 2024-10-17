<?php
function mincoins(array $coins, int $target): int
{
    // Check the simplest answers first...
    if (count($coins) === 0) {
        return 0;
    }

    if (in_array($target, $coins)) {
        return 1;
    }

    // Strip any duplicate coins, or coins higher than the target value...
    $coins = array_unique($coins);
    $coins = array_filter($coins, fn (int $coin): bool => $coin <= $target);

    // Sort the coins from highest to lowest...
    rsort($coins);

    // Keep adding up the given coins until the target is reached or exceeded...
    // If you changed $used to an array and put each coin in it, the method could return the exact coins needed to give as change...
    $attempt = function (array $coins) use ($target) {
        $used = 0;
        $total = 0;

        foreach ($coins as $coin) {
            while ($total + $coin <= $target) {
                $used += 1;
                $total += $coin;
            }
        }

        return ['used' => $used, 'total' => $total];
    };

    // Work through all the coins, each time removing the highest coin from the array...
    foreach (range(0, count($coins)) as $index) {
        $result = $attempt(array_slice($coins, $index));

        // As soon as we've hit the target we've got the minimum number of coins...
        if ($result['total'] === $target) {
            return $result['used'];
        }
    }

    // Otherwise we can't possibly hit the target...
    return -1;
}

echo mincoins([2, 5, 4], 8) . PHP_EOL; // Should be 2...
echo mincoins([100, 10_000, 5, 10], 115) . PHP_EOL; // Should be 3...
echo mincoins([6], 7) . PHP_EOL; // Should be -1...
echo mincoins([100, 5, 10, 15, 1, 2, 3, 4], 110) . PHP_EOL; // Should be 2...
echo mincoins([10, 3, 50], 115) . PHP_EOL; // Shoud be 7...
echo mincoins([1, 3, 5, 7], 16) . PHP_EOL; // Should be 4...

// This solution doesn't work properly, as some coin combos won't be solved.
// Proper solution is this algorithm: https://www.geeksforgeeks.org/find-minimum-number-of-coins-that-make-a-change/
// Dynamic Programming - Ch11 of Grokking Algorithms (2nd Edition)
<?php
// @see: https://github.com/sebastianbergmann/phpunit/issues/4026
// Use `$this->with(...$this->consecutiveParams($args1, $args2))` instead of `$this->withConsecutive($args1, $args2)`.

use PHPUnit\Framework\TestCase;

class FooTest extends TestCase
{
    use ConsecutiveParams;

    public function testAdd(): void
    {
        $mock = $this->getMockBuilder(Adder::class)
            ->getMock();

        $mock->expects($this->exactly(3))
            ->method('add')
            ->with(...$this->consecutiveParams(
                [1, 1],
                [2, 2],
                [3, 3],
            ));

        $mock->add(1, 1);
        $mock->add(2, 2);
        $mock->add(3, 3);
    }
}

class Adder {
    public function add(int $a, int $b): int
    {
        return $a + $b;
    }
};

trait ConsecutiveParams {
    // @see: https://stackoverflow.com/questions/75389000/replace-phpunit-method-withconsecutive
    // @see: https://stackoverflow.com/questions/21861825/quick-way-to-find-the-largest-array-in-a-multidimensional-array
    public function consecutiveParams(array ...$args): array
    {
        $callbacks = [];
        $count = count(max($args));

        for ($index = 0; $index < $count; $index++) {
            $returns = [];

            foreach ($args as $arg) {
                if (! array_is_list($arg)) {
                    throw new \InvalidArgumentException('Every array must be a list');
                }

                if (! isset($arg[$index])) {
                    throw new \InvalidArgumentException(sprintf('Every array must contain %d parameters', $count));
                }

                $returns[] = $arg[$index];
            }

            $callbacks[] = $this->callback(new class ($returns) {
                public function __construct(protected array $returns)
                {
                }

                public function __invoke(mixed $actual): bool
                {
                    if (count($this->returns) === 0) {
                        return true;
                    }
                
                    return $actual === array_shift($this->returns);
                }
            });
        }

        return $callbacks;
    }
}

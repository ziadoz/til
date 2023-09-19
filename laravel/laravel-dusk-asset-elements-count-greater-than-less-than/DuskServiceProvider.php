<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Assert as PHPUnit;
use PHPUnit\Framework\Constraint\Count;

class DuskServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $assertCount = function (string $selector, string $operator, int $expected) {
            $frequency = match ($operator) {
                '=' => 'exactly',
                '>' => 'greater than',
                '>=' => 'greater than or equal to',
                '<' => 'less than',
                '<=' => 'less than or equal to',
            };

            $assertion = match ($operator) {
                '=' => new Count($expected),
                '>' => PHPUnit::greaterThan($expected),
                '>=' => PHPUnit::greaterThanOrEqual($expected),
                '<' => PHPUnit::lessThan($expected),
                '<=' => PHPUnit::lessThanOrEqual($expected),
            };

            $fullSelector = $this->resolver->format($selector);

            PHPUnit::assertThat(
                $this->resolver->all($selector),
                $assertion,
                "Expected element [{$fullSelector}] {$frequency} {$expected} times."
            );

            return $this;
        };

        Browser::macro('assertCount', fn (string $selector, int $expected) => $assertCount->bindTo($this)($selector, '=', $expected));
        Browser::macro('assertGreaterThan', fn (string $selector, int $expected) => $assertCount->bindTo($this)($selector, '>', $expected));
        Browser::macro('assertGreaterThanOrEqual', fn (string $selector, int $expected) => $assertCount->bindTo($this)($selector, '>=', $expected));
        Browser::macro('lessThan', fn (string $selector, int $expected) => $assertCount->bindTo($this)($selector, '<', $expected));
        Browser::macro('lessThanOrEqual', fn (string $selector, int $expected) => $assertCount->bindTo($this)($selector, '<=', $expected));
    }
}

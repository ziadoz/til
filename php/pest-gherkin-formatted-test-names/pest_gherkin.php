<?php

function feature(string $feature, callable $callable): void
{
    group('Feature: ' . $feature, $callable); // Call Pest's group() method with the feature part of the Gherkin string.
}

function given(string $given)
{
    return new class($given) implements Stringable {
        private array $gherkin = [];

        public function __construct(string $given)
        {
            $this->gherkin[] = 'Given: ' . $given;
        }

        public function and(string $and): self
        {
            $this->gherkin[] = '  And: ' . $and;

            return $this;
        }

        public function when(string $when): self
        {
            $this->gherkin[] = '  When: ' . $when;

            return $this;
        }

        public function then(string $then): self
        {
            $this->gherkin[] = '  Then: ' . $then;

            return $this;
        }

        public function test(callable $callable): void
        {
            test($this->__toString(), $callable); // Call Pest's test()/it() method with the Gherkin string.
        }

        public function __toString()
        {
            return implode("\n", $this->gherkin);
        }
    };
}

feature('User Login', function () {
    given('A user is logged in')
        ->and('The user has admin privileges')
        ->and('The user navigates to the admin page')
        ->when('The user clicks on the dashboard link')
        ->then('The user can access the admin dashboard')
        ->test(function () {
            // Do some testing...
        });
});

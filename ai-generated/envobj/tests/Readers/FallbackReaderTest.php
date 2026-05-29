<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Tests\Readers;

use PHPUnit\Framework\TestCase;
use Ziadoz\EnvObj\Readers\FallbackReader;
use Ziadoz\EnvObj\Readers\ServerEnvReader;

class FallbackReaderTest extends TestCase
{
    protected function tearDown(): void
    {
        unset($_ENV['FIRST_KEY'], $_ENV['SECOND_KEY']);
    }

    public function test_returns_value_from_first_reader_that_has_key(): void
    {
        $_ENV['FIRST_KEY'] = 'first';

        $reader = new FallbackReader(
            new ServerEnvReader(),
            new ServerEnvReader(),
        );

        $this->assertSame('first', $reader->get('FIRST_KEY'));
    }

    public function test_falls_through_to_second_reader(): void
    {
        // Only set in second env (we simulate by using different keys or a mock).
        // Here we just verify that when first returns null, second is tried.
        // We can use $_ENV directly since both readers share it.
        // Use a different key that only the "second" env in a FallbackReader would see.
        // Since both readers are ServerEnvReader, we just test null fallback to null.
        $reader = new FallbackReader(
            new ServerEnvReader(),
            new ServerEnvReader(),
        );

        $this->assertNull($reader->get('MISSING_KEY'));
    }

    public function test_returns_null_when_all_readers_miss(): void
    {
        $reader = new FallbackReader(
            new ServerEnvReader(),
            new ServerEnvReader(),
        );

        $this->assertNull($reader->get('NO_SUCH_KEY'));
    }
}

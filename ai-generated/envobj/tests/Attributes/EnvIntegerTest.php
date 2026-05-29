<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Tests\Attributes;

use PHPUnit\Framework\TestCase;
use Ziadoz\EnvObj\Attributes\EnvInteger;
use Ziadoz\EnvObj\EnvReader;
use Ziadoz\EnvObj\Exceptions\InvalidEnvException;
use Ziadoz\EnvObj\Readers\ServerEnvReader;

class EnvIntegerTest extends TestCase
{
    protected function setUp(): void
    {
        EnvReader::set(new ServerEnvReader());
    }

    protected function tearDown(): void
    {
        EnvReader::reset();
        unset($_ENV['TEST_INT']);
    }

    public function test_reads_integer_value(): void
    {
        $_ENV['TEST_INT'] = '42';

        $this->assertSame(42, (new EnvInteger('TEST_INT'))->read());
    }

    public function test_returns_default_when_missing(): void
    {
        $this->assertSame(99, (new EnvInteger('TEST_INT', 99))->read());
    }

    public function test_returns_null_when_nullable_and_missing(): void
    {
        $this->assertNull((new EnvInteger('TEST_INT', nullable: true))->read());
    }

    public function test_throws_when_missing_and_required(): void
    {
        $this->expectException(InvalidEnvException::class);

        (new EnvInteger('TEST_INT'))->read();
    }

    public function test_throws_on_non_integer_value(): void
    {
        $_ENV['TEST_INT'] = 'not_a_number';

        $this->expectException(InvalidEnvException::class);

        (new EnvInteger('TEST_INT'))->read();
    }

    public function test_throws_on_float_string(): void
    {
        $_ENV['TEST_INT'] = '3.14';

        $this->expectException(InvalidEnvException::class);

        (new EnvInteger('TEST_INT'))->read();
    }
}

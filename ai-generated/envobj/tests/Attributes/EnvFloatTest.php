<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Tests\Attributes;

use PHPUnit\Framework\TestCase;
use Ziadoz\EnvObj\Attributes\EnvFloat;
use Ziadoz\EnvObj\EnvReader;
use Ziadoz\EnvObj\Exceptions\InvalidEnvException;
use Ziadoz\EnvObj\Readers\ServerEnvReader;

class EnvFloatTest extends TestCase
{
    protected function setUp(): void
    {
        EnvReader::set(new ServerEnvReader());
    }

    protected function tearDown(): void
    {
        EnvReader::reset();
        unset($_ENV['TEST_FLOAT']);
    }

    public function test_reads_float_value(): void
    {
        $_ENV['TEST_FLOAT'] = '3.14';

        $this->assertSame(3.14, (new EnvFloat('TEST_FLOAT'))->read());
    }

    public function test_reads_integer_as_float(): void
    {
        $_ENV['TEST_FLOAT'] = '42';

        $this->assertSame(42.0, (new EnvFloat('TEST_FLOAT'))->read());
    }

    public function test_returns_default_when_missing(): void
    {
        $this->assertSame(1.5, (new EnvFloat('TEST_FLOAT', 1.5))->read());
    }

    public function test_returns_null_when_nullable_and_missing(): void
    {
        $this->assertNull((new EnvFloat('TEST_FLOAT', nullable: true))->read());
    }

    public function test_throws_when_missing_and_required(): void
    {
        $this->expectException(InvalidEnvException::class);

        (new EnvFloat('TEST_FLOAT'))->read();
    }

    public function test_throws_on_non_numeric_value(): void
    {
        $_ENV['TEST_FLOAT'] = 'not_a_float';

        $this->expectException(InvalidEnvException::class);

        (new EnvFloat('TEST_FLOAT'))->read();
    }
}

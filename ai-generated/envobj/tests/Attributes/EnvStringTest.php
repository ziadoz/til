<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Tests\Attributes;

use PHPUnit\Framework\TestCase;
use Ziadoz\EnvObj\Attributes\EnvString;
use Ziadoz\EnvObj\EnvReader;
use Ziadoz\EnvObj\Exceptions\InvalidEnvException;
use Ziadoz\EnvObj\Readers\ServerEnvReader;

class EnvStringTest extends TestCase
{
    protected function setUp(): void
    {
        EnvReader::set(new ServerEnvReader());
    }

    protected function tearDown(): void
    {
        EnvReader::reset();
        unset($_ENV['TEST_STRING']);
    }

    public function test_reads_string_value(): void
    {
        $_ENV['TEST_STRING'] = 'hello world';

        $this->assertSame('hello world', (new EnvString('TEST_STRING'))->read());
    }

    public function test_returns_default_when_missing(): void
    {
        $this->assertSame('fallback', (new EnvString('TEST_STRING', 'fallback'))->read());
    }

    public function test_returns_null_when_nullable_and_missing(): void
    {
        $this->assertNull((new EnvString('TEST_STRING', nullable: true))->read());
    }

    public function test_throws_when_missing_and_required(): void
    {
        $this->expectException(InvalidEnvException::class);

        (new EnvString('TEST_STRING'))->read();
    }
}

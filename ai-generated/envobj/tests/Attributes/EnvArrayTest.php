<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Tests\Attributes;

use PHPUnit\Framework\TestCase;
use Ziadoz\EnvObj\Attributes\EnvArray;
use Ziadoz\EnvObj\EnvReader;
use Ziadoz\EnvObj\Exceptions\InvalidEnvException;
use Ziadoz\EnvObj\Readers\ServerEnvReader;

class EnvArrayTest extends TestCase
{
    protected function setUp(): void
    {
        EnvReader::set(new ServerEnvReader());
    }

    protected function tearDown(): void
    {
        EnvReader::reset();
        unset($_ENV['TEST_ARRAY']);
    }

    public function test_reads_comma_separated_values(): void
    {
        $_ENV['TEST_ARRAY'] = 'a,b,c';

        $this->assertSame(['a', 'b', 'c'], (new EnvArray('TEST_ARRAY'))->read());
    }

    public function test_trims_whitespace(): void
    {
        $_ENV['TEST_ARRAY'] = 'a , b , c';

        $this->assertSame(['a', 'b', 'c'], (new EnvArray('TEST_ARRAY'))->read());
    }

    public function test_returns_default_when_missing(): void
    {
        $this->assertSame(['x', 'y'], (new EnvArray('TEST_ARRAY', ['x', 'y']))->read());
    }

    public function test_returns_null_when_nullable_and_missing(): void
    {
        $this->assertNull((new EnvArray('TEST_ARRAY', nullable: true))->read());
    }

    public function test_throws_when_missing_and_required(): void
    {
        $this->expectException(InvalidEnvException::class);

        (new EnvArray('TEST_ARRAY'))->read();
    }

    public function test_custom_separator(): void
    {
        $_ENV['TEST_ARRAY'] = 'a|b|c';

        $this->assertSame(['a', 'b', 'c'], (new EnvArray('TEST_ARRAY', separator: '|'))->read());
    }
}

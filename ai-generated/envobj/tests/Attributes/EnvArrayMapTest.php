<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Tests\Attributes;

use PHPUnit\Framework\TestCase;
use Ziadoz\EnvObj\Attributes\EnvArrayMap;
use Ziadoz\EnvObj\EnvReader;
use Ziadoz\EnvObj\Exceptions\InvalidEnvException;
use Ziadoz\EnvObj\Readers\ServerEnvReader;

class EnvArrayMapTest extends TestCase
{
    protected function setUp(): void
    {
        EnvReader::set(new ServerEnvReader());
    }

    protected function tearDown(): void
    {
        EnvReader::reset();
        unset($_ENV['TEST_MAP']);
    }

    public function test_reads_key_value_pairs(): void
    {
        $_ENV['TEST_MAP'] = 'foo=bar,baz=qux';

        $this->assertSame(['foo' => 'bar', 'baz' => 'qux'], (new EnvArrayMap('TEST_MAP'))->read());
    }

    public function test_trims_whitespace(): void
    {
        $_ENV['TEST_MAP'] = ' foo = bar , baz = qux ';

        $this->assertSame(['foo' => 'bar', 'baz' => 'qux'], (new EnvArrayMap('TEST_MAP'))->read());
    }

    public function test_returns_default_when_missing(): void
    {
        $default = ['a' => '1'];

        $this->assertSame($default, (new EnvArrayMap('TEST_MAP', $default))->read());
    }

    public function test_returns_null_when_nullable_and_missing(): void
    {
        $this->assertNull((new EnvArrayMap('TEST_MAP', nullable: true))->read());
    }

    public function test_throws_when_missing_and_required(): void
    {
        $this->expectException(InvalidEnvException::class);

        (new EnvArrayMap('TEST_MAP'))->read();
    }

    public function test_throws_on_invalid_pair_format(): void
    {
        $_ENV['TEST_MAP'] = 'noequalssign';

        $this->expectException(InvalidEnvException::class);

        (new EnvArrayMap('TEST_MAP'))->read();
    }
}

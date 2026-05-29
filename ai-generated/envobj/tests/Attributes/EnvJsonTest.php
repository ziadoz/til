<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Tests\Attributes;

use PHPUnit\Framework\TestCase;
use Ziadoz\EnvObj\Attributes\EnvJson;
use Ziadoz\EnvObj\EnvReader;
use Ziadoz\EnvObj\Exceptions\InvalidEnvException;
use Ziadoz\EnvObj\Readers\ServerEnvReader;

class EnvJsonTest extends TestCase
{
    protected function setUp(): void
    {
        EnvReader::set(new ServerEnvReader());
    }

    protected function tearDown(): void
    {
        EnvReader::reset();
        unset($_ENV['TEST_JSON']);
    }

    public function test_reads_json_array(): void
    {
        $_ENV['TEST_JSON'] = '["a","b","c"]';

        $this->assertSame(['a', 'b', 'c'], (new EnvJson('TEST_JSON'))->read());
    }

    public function test_reads_json_object_as_assoc_array(): void
    {
        $_ENV['TEST_JSON'] = '{"key":"value"}';

        $this->assertSame(['key' => 'value'], (new EnvJson('TEST_JSON'))->read());
    }

    public function test_returns_default_when_missing(): void
    {
        $default = ['default' => true];

        $this->assertSame($default, (new EnvJson('TEST_JSON', $default))->read());
    }

    public function test_returns_null_when_nullable_and_missing(): void
    {
        $this->assertNull((new EnvJson('TEST_JSON', nullable: true))->read());
    }

    public function test_throws_when_missing_and_required(): void
    {
        $this->expectException(InvalidEnvException::class);

        (new EnvJson('TEST_JSON'))->read();
    }

    public function test_throws_on_invalid_json(): void
    {
        $_ENV['TEST_JSON'] = 'not-json';

        $this->expectException(InvalidEnvException::class);

        (new EnvJson('TEST_JSON'))->read();
    }
}

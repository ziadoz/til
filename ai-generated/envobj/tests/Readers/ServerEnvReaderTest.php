<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Tests\Readers;

use PHPUnit\Framework\TestCase;
use Ziadoz\EnvObj\Readers\ServerEnvReader;

class ServerEnvReaderTest extends TestCase
{
    private ServerEnvReader $reader;

    protected function setUp(): void
    {
        $this->reader = new ServerEnvReader();
    }

    protected function tearDown(): void
    {
        unset($_ENV['TEST_KEY']);
    }

    public function test_returns_value_when_key_exists(): void
    {
        $_ENV['TEST_KEY'] = 'hello';

        $this->assertSame('hello', $this->reader->get('TEST_KEY'));
    }

    public function test_returns_null_when_key_missing(): void
    {
        $this->assertNull($this->reader->get('TEST_KEY'));
    }

    public function test_casts_non_string_values_to_string(): void
    {
        $_ENV['TEST_KEY'] = 42;

        $this->assertSame('42', $this->reader->get('TEST_KEY'));
    }
}

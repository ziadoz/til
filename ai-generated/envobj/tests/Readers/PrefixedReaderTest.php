<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Tests\Readers;

use PHPUnit\Framework\TestCase;
use Ziadoz\EnvObj\Readers\PrefixedReader;
use Ziadoz\EnvObj\Readers\ServerEnvReader;

class PrefixedReaderTest extends TestCase
{
    protected function tearDown(): void
    {
        unset($_ENV['APP_TEST_KEY'], $_ENV['TEST_KEY']);
    }

    public function test_prepends_prefix_to_key(): void
    {
        $_ENV['APP_TEST_KEY'] = 'value';

        $reader = new PrefixedReader('APP_', new ServerEnvReader());

        $this->assertSame('value', $reader->get('TEST_KEY'));
    }

    public function test_returns_null_when_prefixed_key_missing(): void
    {
        $reader = new PrefixedReader('APP_', new ServerEnvReader());

        $this->assertNull($reader->get('TEST_KEY'));
    }

    public function test_does_not_find_unprefixed_key(): void
    {
        $_ENV['TEST_KEY'] = 'value';

        $reader = new PrefixedReader('APP_', new ServerEnvReader());

        $this->assertNull($reader->get('TEST_KEY'));
    }
}

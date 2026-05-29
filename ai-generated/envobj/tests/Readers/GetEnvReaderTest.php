<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Tests\Readers;

use PHPUnit\Framework\TestCase;
use Ziadoz\EnvObj\Readers\GetEnvReader;

class GetEnvReaderTest extends TestCase
{
    private GetEnvReader $reader;

    protected function setUp(): void
    {
        $this->reader = new GetEnvReader();
    }

    protected function tearDown(): void
    {
        putenv('TEST_KEY');
    }

    public function test_returns_value_when_key_exists(): void
    {
        putenv('TEST_KEY=hello');

        $this->assertSame('hello', $this->reader->get('TEST_KEY'));
    }

    public function test_returns_null_when_key_missing(): void
    {
        $this->assertNull($this->reader->get('TEST_KEY'));
    }
}

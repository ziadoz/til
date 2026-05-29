<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Tests\Attributes;

use PHPUnit\Framework\TestCase;
use Ziadoz\EnvObj\Attributes\EnvDsn;
use Ziadoz\EnvObj\EnvReader;
use Ziadoz\EnvObj\Exceptions\InvalidEnvException;
use Ziadoz\EnvObj\Readers\ServerEnvReader;

class EnvDsnTest extends TestCase
{
    protected function setUp(): void
    {
        EnvReader::set(new ServerEnvReader());
    }

    protected function tearDown(): void
    {
        EnvReader::reset();
        unset($_ENV['TEST_DSN']);
    }

    public function test_reads_dsn_as_parsed_array(): void
    {
        $_ENV['TEST_DSN'] = 'mysql://user:pass@127.0.0.1:3306/dbname';

        $result = (new EnvDsn('TEST_DSN'))->read();

        $this->assertSame('mysql', $result['scheme']);
        $this->assertSame('user', $result['user']);
        $this->assertSame('pass', $result['pass']);
        $this->assertSame('127.0.0.1', $result['host']);
        $this->assertSame(3306, $result['port']);
        $this->assertSame('/dbname', $result['path']);
    }

    public function test_returns_default_when_missing(): void
    {
        $default = ['scheme' => 'sqlite'];

        $this->assertSame($default, (new EnvDsn('TEST_DSN', $default))->read());
    }

    public function test_returns_null_when_nullable_and_missing(): void
    {
        $this->assertNull((new EnvDsn('TEST_DSN', nullable: true))->read());
    }

    public function test_throws_when_missing_and_required(): void
    {
        $this->expectException(InvalidEnvException::class);

        (new EnvDsn('TEST_DSN'))->read();
    }
}

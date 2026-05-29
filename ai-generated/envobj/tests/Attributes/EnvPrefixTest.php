<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Tests\Attributes;

use PHPUnit\Framework\TestCase;
use Ziadoz\EnvObj\Attributes\EnvPrefix;
use Ziadoz\EnvObj\Attributes\EnvString;
use Ziadoz\EnvObj\Attributes\EnvInteger;
use Ziadoz\EnvObj\EnvReader;
use Ziadoz\EnvObj\Exceptions\InvalidEnvException;
use Ziadoz\EnvObj\Readers\ServerEnvReader;

class EnvPrefixTest extends TestCase
{
    protected function setUp(): void
    {
        EnvReader::set(new ServerEnvReader());
    }

    protected function tearDown(): void
    {
        EnvReader::reset();
        unset($_ENV['APP_HOST'], $_ENV['APP_PORT']);
    }

    public function test_prepends_prefix_to_key(): void
    {
        $_ENV['APP_HOST'] = 'localhost';

        $result = (new EnvPrefix('APP_', new EnvString('HOST')))->read();

        $this->assertSame('localhost', $result);
    }

    public function test_works_with_integer_attribute(): void
    {
        $_ENV['APP_PORT'] = '8080';

        $result = (new EnvPrefix('APP_', new EnvInteger('PORT')))->read();

        $this->assertSame(8080, $result);
    }

    public function test_throws_when_prefixed_key_missing(): void
    {
        $this->expectException(InvalidEnvException::class);

        (new EnvPrefix('APP_', new EnvString('HOST')))->read();
    }
}

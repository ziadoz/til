<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Tests\Attributes;

use PHPUnit\Framework\TestCase;
use Ziadoz\EnvObj\Attributes\EnvEnum;
use Ziadoz\EnvObj\EnvReader;
use Ziadoz\EnvObj\Exceptions\InvalidEnvException;
use Ziadoz\EnvObj\Readers\ServerEnvReader;
use Ziadoz\EnvObj\Tests\Fixtures\AppEnvEnum;

class EnvEnumTest extends TestCase
{
    protected function setUp(): void
    {
        EnvReader::set(new ServerEnvReader());
    }

    protected function tearDown(): void
    {
        EnvReader::reset();
        unset($_ENV['TEST_ENUM']);
    }

    public function test_reads_valid_enum_value(): void
    {
        $_ENV['TEST_ENUM'] = 'production';

        $result = (new EnvEnum('TEST_ENUM', AppEnvEnum::class))->read();

        $this->assertSame(AppEnvEnum::Production, $result);
    }

    public function test_throws_on_invalid_enum_value(): void
    {
        $_ENV['TEST_ENUM'] = 'invalid_env';

        $this->expectException(InvalidEnvException::class);

        (new EnvEnum('TEST_ENUM', AppEnvEnum::class))->read();
    }

    public function test_throws_when_missing_and_required(): void
    {
        $this->expectException(InvalidEnvException::class);

        (new EnvEnum('TEST_ENUM', AppEnvEnum::class))->read();
    }

    public function test_returns_null_via_try_from_when_nullable_and_invalid(): void
    {
        $_ENV['TEST_ENUM'] = 'unknown';

        $result = (new EnvEnum('TEST_ENUM', AppEnvEnum::class, nullable: true))->read();

        $this->assertNull($result);
    }

    public function test_returns_null_when_nullable_and_missing(): void
    {
        $result = (new EnvEnum('TEST_ENUM', AppEnvEnum::class, nullable: true))->read();

        $this->assertNull($result);
    }
}

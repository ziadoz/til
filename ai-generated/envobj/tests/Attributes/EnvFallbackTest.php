<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Tests\Attributes;

use PHPUnit\Framework\TestCase;
use Ziadoz\EnvObj\Attributes\EnvFallback;
use Ziadoz\EnvObj\Attributes\EnvString;
use Ziadoz\EnvObj\EnvReader;
use Ziadoz\EnvObj\Exceptions\InvalidEnvException;
use Ziadoz\EnvObj\Readers\ServerEnvReader;

class EnvFallbackTest extends TestCase
{
    protected function setUp(): void
    {
        EnvReader::set(new ServerEnvReader());
    }

    protected function tearDown(): void
    {
        EnvReader::reset();
        unset($_ENV['FIRST_VAR'], $_ENV['SECOND_VAR']);
    }

    public function test_returns_first_hit(): void
    {
        $_ENV['FIRST_VAR']  = 'first_value';
        $_ENV['SECOND_VAR'] = 'second_value';

        $result = (new EnvFallback(
            new EnvString('FIRST_VAR'),
            new EnvString('SECOND_VAR'),
        ))->read();

        $this->assertSame('first_value', $result);
    }

    public function test_returns_second_hit_when_first_missing(): void
    {
        $_ENV['SECOND_VAR'] = 'second_value';

        $result = (new EnvFallback(
            new EnvString('FIRST_VAR'),
            new EnvString('SECOND_VAR'),
        ))->read();

        $this->assertSame('second_value', $result);
    }

    public function test_throws_when_all_miss_and_required(): void
    {
        $this->expectException(InvalidEnvException::class);

        (new EnvFallback(
            new EnvString('FIRST_VAR'),
            new EnvString('SECOND_VAR'),
        ))->read();
    }

    public function test_returns_last_attribute_default_when_all_miss(): void
    {
        $result = (new EnvFallback(
            new EnvString('FIRST_VAR'),
            new EnvString('SECOND_VAR', 'the_default'),
        ))->read();

        $this->assertSame('the_default', $result);
    }
}

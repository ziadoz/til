<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Tests\Attributes;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Ziadoz\EnvObj\Attributes\EnvBool;
use Ziadoz\EnvObj\EnvReader;
use Ziadoz\EnvObj\Exceptions\InvalidEnvException;
use Ziadoz\EnvObj\Readers\ServerEnvReader;

class EnvBoolTest extends TestCase
{
    protected function setUp(): void
    {
        EnvReader::set(new ServerEnvReader());
    }

    protected function tearDown(): void
    {
        EnvReader::reset();
        unset($_ENV['TEST_BOOL']);
    }

    #[DataProvider('truthyValuesProvider')]
    public function test_truthy_values(string $value): void
    {
        $_ENV['TEST_BOOL'] = $value;

        $this->assertTrue((new EnvBool('TEST_BOOL'))->read());
    }

    public static function truthyValuesProvider(): array
    {
        return [
            ['true'],
            ['TRUE'],
            ['True'],
            ['1'],
            ['yes'],
            ['YES'],
            ['on'],
            ['ON'],
        ];
    }

    #[DataProvider('falsyValuesProvider')]
    public function test_falsy_values(string $value): void
    {
        $_ENV['TEST_BOOL'] = $value;

        $this->assertFalse((new EnvBool('TEST_BOOL'))->read());
    }

    public static function falsyValuesProvider(): array
    {
        return [
            ['false'],
            ['FALSE'],
            ['False'],
            ['0'],
            ['no'],
            ['NO'],
            ['off'],
            ['OFF'],
            [''],
        ];
    }

    public function test_throws_on_invalid_bool_value(): void
    {
        $_ENV['TEST_BOOL'] = 'maybe';

        $this->expectException(InvalidEnvException::class);

        (new EnvBool('TEST_BOOL'))->read();
    }

    public function test_returns_default_when_missing(): void
    {
        $this->assertTrue((new EnvBool('TEST_BOOL', true))->read());
    }

    public function test_returns_null_when_nullable_and_missing(): void
    {
        $this->assertNull((new EnvBool('TEST_BOOL', nullable: true))->read());
    }

    public function test_throws_when_missing_and_required(): void
    {
        $this->expectException(InvalidEnvException::class);

        (new EnvBool('TEST_BOOL'))->read();
    }
}

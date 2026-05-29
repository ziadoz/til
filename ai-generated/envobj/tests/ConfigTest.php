<?php

declare(strict_types=1);

namespace Ziadoz\EnvObj\Tests;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Ziadoz\EnvObj\ArrayableConfig;
use Ziadoz\EnvObj\Attributes\EnvString;
use Ziadoz\EnvObj\Config;
use Ziadoz\EnvObj\EnvReader;
use Ziadoz\EnvObj\Readers\ServerEnvReader;
use Ziadoz\EnvObj\Tests\Fixtures\AppConfig;
use Ziadoz\EnvObj\Tests\Fixtures\DbConfig;

class ConfigTest extends TestCase
{
    protected function setUp(): void
    {
        EnvReader::set(new ServerEnvReader());

        $_ENV['DB_HOST']           = '127.0.0.1';
        $_ENV['DB_NAME']           = 'mydb';
        $_ENV['DB_USER']           = 'root';
        $_ENV['DB_PASS']           = 'secret';
        $_ENV['DB_OPTIONS_SSL_CA'] = '24';
        $_ENV['DB_MODES']          = 'STRICT,NO_ZERO_DATE';
    }

    protected function tearDown(): void
    {
        EnvReader::reset();

        unset(
            $_ENV['DB_HOST'],
            $_ENV['DB_NAME'],
            $_ENV['DB_USER'],
            $_ENV['DB_PASS'],
            $_ENV['DB_USERNAME'],
            $_ENV['DB_PASSWORD'],
            $_ENV['DB_OPTIONS_SSL_CA'],
            $_ENV['DB_MODES'],
        );
    }

    public function test_load_populates_env_var_properties(): void
    {
        $config = new DbConfig()->load();

        $this->assertSame('127.0.0.1', $config->host);
        $this->assertSame('mydb', $config->database);
        $this->assertSame('root', $config->username);
        $this->assertSame('secret', $config->password);
    }

    public function test_load_populates_nested_config_from_type(): void
    {
        $config = new AppConfig()->load();

        $this->assertInstanceOf(DbConfig::class, $config->db);
        $this->assertSame('127.0.0.1', $config->db->host);
    }

    public function test_load_populates_nested_config_from_instance(): void
    {
        $config = new AppConfig()->load();

        $this->assertSame(['STRICT', 'NO_ZERO_DATE'], $config->modes->modes);
    }

    public function test_load_returns_new_instance(): void
    {
        $original = new DbConfig();
        $loaded   = $original->load();

        $this->assertNotSame($original, $loaded);
    }

    public function test_config_is_readonly(): void
    {
        $config = new DbConfig()->load();

        $this->expectException(\Error::class);

        $config->host = 'other';
    }

    // -----------------------------------------------------------------------
    // ArrayableConfig tests
    // -----------------------------------------------------------------------

    public function test_array_access_by_property_name(): void
    {
        $config = new DbConfig()->load();

        $this->assertSame('127.0.0.1', $config['host']);
    }

    public function test_array_access_dot_notation(): void
    {
        $config = new AppConfig()->load();

        $this->assertSame('127.0.0.1', $config['db.host']);
        $this->assertSame('mydb', $config['db.database']);
    }

    public function test_array_access_throws_on_missing_key(): void
    {
        $config = new DbConfig()->load();

        $this->expectException(RuntimeException::class);

        $config['nonexistent'];
    }

    public function test_offset_set_throws(): void
    {
        $config = new DbConfig()->load();

        $this->expectException(RuntimeException::class);

        $config['host'] = 'other';
    }

    public function test_offset_unset_throws(): void
    {
        $config = new DbConfig()->load();

        $this->expectException(RuntimeException::class);

        unset($config['host']);
    }

    public function test_to_array(): void
    {
        $config = new DbConfig()->load();
        $array  = $config->toArray();

        $this->assertIsArray($array);
        $this->assertSame('127.0.0.1', $array['host']);
        $this->assertSame('mydb', $array['database']);
    }

    public function test_to_array_recursive(): void
    {
        $config = new AppConfig()->load();
        $array  = $config->toArray();

        $this->assertIsArray($array['db']);
        $this->assertSame('127.0.0.1', $array['db']['host']);
    }

    public function test_json_serialize(): void
    {
        $config = new DbConfig()->load();
        $json   = json_encode($config);

        $this->assertJson($json);

        $decoded = json_decode($json, true);

        $this->assertSame('127.0.0.1', $decoded['host']);
    }

    public function test_serialize_and_unserialize(): void
    {
        $config     = new DbConfig()->load();
        $serialised = serialize($config);
        $restored   = unserialize($serialised);

        $this->assertSame($config->host, $restored->host);
        $this->assertSame($config->database, $restored->database);
    }
}

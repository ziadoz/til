<?php
declare(strict_types=1);

// A PHP library like Pydantic settings where a config object maps automatically to env vars.
// The object should be read-only. Super simple in scope.
// Attributes: Env\String(), Int, Float, Bool, ArrayList, ArrayMap, Laravel Collection OptionsMap (list of allowed values) DSN, JSONMap, JSONArray, Nullable* Enums, Constants (e.g. PDO_* etc.). Default parameter. Prefixed, Fallback. Callable(fn () => ...)
// Ability to read from $_ENV or getenv(). Need two "Reader" classes. Reader could handle PREFIX_ stuff more naturally.
// EnvReader::set(new ServerEnvReader()), EnvReader::get()
// new GetEnvReader(), new PrefixedEnvReader(new GetEnvReader()), new FallbackEnvReader(..., ...)
// new DotEnvReader(), loads using dot env package, then reads from vars rather than set in $_ENV.
// Throw if type mismatches (strict_types).
// Need to be able to cast/validate what's read from env vars.
// https://gist.github.com/ziadoz/fc56be76a81c4e63862efa36a71d263c
// https://github.com/laravel/framework/blob/f2396a70e9658ac81f91db78240c0e05de2876bd/src/Illuminate/Collections/helpers.php#L46
// Simple arrayable/JSON ->toArray(), ->jsonSerialize() methods only. No Laravel dot notation, can be added by users???

/**
 * Env.
 */
$_ENV['DB_HOST'] = '127.0.0.1';
$_ENV['DB_NAME'] = 'db';
$_ENV['DB_USER'] = 'foo';
$_ENV['DB_PASS'] = 'bar';
$_ENV['DB_OPTIONS_SSL_CA'] = 24;
$_ENV['DB_MODES'] = 'STRICT,NO_ZERO_DATE';

/**
 * Attributes.
 */
interface EnvVar
{
}

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class EnvString implements EnvVar
{
    public function __construct(public string $key, public string $default = '')
    {
    }

    public function read(): string
    {
        return $_ENV[$this->key] ?? $this->default;
    }
}

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class EnvInteger implements EnvVar
{
    public function __construct(public string $key, public int $default = 0)
    {
    }

    public function read(): int
    {
        return $_ENV[$this->key] ?? $this->default;
    }
}

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class EnvArray implements EnvVar
{
    public function __construct(public string $key, public array $default = [])
    {
    }

    public function read(): array
    {
        return isset($_ENV[$this->key]) ? explode(',', $_ENV[$this->key]) : $this->default;
    }
}

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class EnvFallback implements EnvVar
{
    public array $envs;

    public function __construct(EnvVar ...$envs)
    {
        $this->envs = $envs;
    }

    public function read(): mixed
    {
        foreach ($this->envs as $env) {
            $value = $env->read();

            if ($value !== $env->default) {
                return $value;
            }
        }

        return $env->default;
    }
}

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class EnvPrefix implements EnvVar
{
    public function __construct(public string $prefix, public EnvVar $env)
    {
    }

    public function read(): mixed
    {
        return new ($this->env::class)($this->prefix . $this->env->key, $this->env->default)->read();
    }
}

/**
 * Config.
 */
readonly class Config
{
    // Cloning is the only way to change read-only properties, and allows them to be left uninitialised.
    // https://stitcher.io/blog/cloning-readonly-properties-in-php-83
    // https://github.com/spatie/php-cloneable/blob/main/src/Cloneable.php
    final public function __clone(): void
    {
        foreach (new ReflectionClass($this)->getProperties() as $property) {
            if ($property->isStatic()) {
                throw new RuntimeException('Cannot populate static property: ' . $property->getName());
            }

            if (! $property->isReadOnly()) {
                throw new RuntimeException('Cannot populate non-readonly property: ' . $property->getName());
            }

            $field = $property->getName();
            $type  = $property->getType()->getName();

            if (is_a($type, Config::class, true)) {
                $this->$field = new $type()->load();
                continue;
            }

            if ($property->isInitialized($this) && ($value = $property->getValue($this)) && $value instanceof Config) {
                $this->$field = new $value()->load();
                continue;
            }

            if (count($attributes = $property->getAttributes(EnvVar::class, ReflectionAttribute::IS_INSTANCEOF)) > 0) {
                foreach ($attributes as $attribute) {
                    $this->$field = $attribute->newInstance()->read();
                }
            }
        }
    }

    final public function load(): self
    {
        return clone $this;
    }
}

readonly class ArraybleConfig extends Config implements ArrayAccess
{
    public function offsetExists(mixed $offset): bool
    {
        return property_exists($this, $offset);
    }

    // Support Laravel style "key.value" etc. would be nice.
    public function offsetGet(mixed $offset): mixed
    {
        if (str_contains($offset, '.')) {
            $keys = explode('.', $offset);

            $result = $this;

            foreach ($keys as $key) {
                $result = isset($result[$key])
                    ? $result[$key]
                    : throw new RuntimeException('Missing config property: ' . $offset);
            }

            return $result;
        }

        return $this->offsetExists($offset)
            ? $this->$offset
            : throw new RuntimeException('Missing config property: ' . $offset);

        // Undefined array key "Plop"
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new RuntimeException('Cannot unset config property: ' . $offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new RuntimeException('Cannot set config property: ' . $offset);
    }

    public function toArray(): array
    {
        $array = [];

        foreach (get_object_vars($this) as $field => $value) {
            if (is_object($value) && in_array(ArrayableConfig::class, class_uses($value))) {
                $array[$field] = $value->toArray();
            } else {
                $array[$field] = $value;
            }
        }

        return $array;
    }
}

readonly class DbConfig extends ArraybleConfig
{
    #[EnvString('DB_HOST')]
    public string $host;

    #[EnvString('DB_NAME')]
    public string $database;

    #[EnvFallback(
        new EnvString('DB_USERNAME'),
        new EnvString('DB_USER'),
    )]
    public string $username;

    #[EnvFallback(
        new EnvString('DB_PASSWORD'),
        new EnvString('DB_PASS'),
    )]
    public string $password;
}

readonly class DbOptionsConfig extends ArraybleConfig
{
    #[EnvInteger('DB_OPTIONS_SSL_CA')]
    public int $sslCa;
}

readonly class DbModes extends ArraybleConfig
{
    #[EnvArray('DB_MODES')]
    public array $modes;
}

readonly class MyConfig extends ArraybleConfig
{
    public DbConfig $db;
    public DbOptionsConfig $dbOptions;

    public function __construct(public DbModes $modes = new DbModes)
    {
    }
}

/**
 * Usage.
 */
var_dump(
    $config = new MyConfig()->load(),
    $config->db->database,
    $config['db'],
    $config['db.database'],
    $config->toArray(),
    $serialised = serialize($config),
    unserialize($serialised),
);
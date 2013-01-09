<?php
class OpenStruct extends ArrayObject
{
    public function __construct($input = array())
    {
        parent::__construct($input, static::ARRAY_AS_PROPS);
    }

    public function offsetSet($key, $value)
    {   
        if (is_array($value)) {
            parent::offsetSet($key, new static($value));
        } else {
            parent::offsetSet($key, $value);
        }
    }

    public function offsetGet($key)
    {
        $raw = parent::offsetGet($key);
        if (is_callable($raw)) {
            return call_user_func($raw);
        }

        return $raw;
    }

    public function __call($method, $args)
    {
        $raw = parent::offsetGet($method);
        if (is_callable($raw)) {
            if (version_compare(PHP_VERSION, '5.4.0', '>=') && $raw instanceof \Closure) {
                $raw->bindTo($this);
            }
            
            return call_user_func_array($raw, $args);
        }
    }

    static public function fromJson($json)
    {
        if (! is_string($json)) {
            throw new InvalidArgumentException('Argument must be a string.');
        }

        $input = json_decode($json, true);
        if (null === $input) {
            throw new InvalidArgumentException('Argument must be a string containing valid JSON.');
        }

        return new static($input);  
    }
}
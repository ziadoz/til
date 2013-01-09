<?php
class Struct extends OpenStruct
{
    public function __construct($input)
    {
        parent::__construct($input);
    }
 
    public function offsetSet($key, $value)
    {
        if (! $this->offsetExists($key)) {
            throw new RuntimeException(sprintf('Undefined field "%s"', $key));
        }
 
        parent::offsetSet($key, $value);
    }
 
    public function offsetGet($key)
    {    
        if (! $this->offsetExists($key)) {
            throw new RuntimeException(sprintf('Undefined field "%s"', $key));
        }
 
        parent::offsetGet($key);
    }
    
    public function offsetUnset($key)
    {    
        throw new RuntimeException(sprintf('Cannot unset field "%s"', $key));
    }    
}
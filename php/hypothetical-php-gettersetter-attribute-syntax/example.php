<?php
class Example
{
    // By default the attribute binds a getter to `getName()`and setter to `setName()`.
    #[Accessor]
    protected string $name;
    
    // Custom getter and setter method names can be passed.
    #[Accessor(getter: 'customGetter, setter: 'customSetter')]
    protected string $name;
    
    // A separate getter attribute works the same way...
    #[Getter]
    protected string $name;
    
    #[Getter('customGetter')]
    protected string $name;
    
    // And a separate setter attribute works the same way too...
    #[Setter]
    protected string $name;
    
    #[Setter('customSetter')]
    protected string $name;    
    
    // And the actual methods...
    public function getName(): string
    {
        return ucwords($this->name);
    }
    
    public function setName(string $value): void
    {
        $this->name = $value;   
    }
}
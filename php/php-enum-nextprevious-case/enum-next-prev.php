<?php

enum Foo: string
{
    case A = 'A';
    case B = 'B';
    case C = 'C';
    
    public function next(): self
    {
        if ($this === self::cases()[count(self::cases()) - 1]) {
            return $this;
        }

        return self::cases()[array_search($this, self::cases()) + 1];
    }
    
    public function prev(): self
    {
        if ($this === self::cases()[0]) {
            return $this;
        }

        return self::cases()[array_search($this, self::cases()) - 1];
    }    
}

print_r(Foo::cases());
print_r(array_search(Foo::C, Foo::cases(), true));

print_r(Foo::A->next()->next()->next());
print_r(Foo::C->prev()->prev()->prev());
print_r(Foo::B->next()->prev());
php repl.php
> $i = 0
> $i++
> echo $i
1
> quit

php repl.php
> >>>
> class Foo extends StdClass 
> {
>   // Your code goes here.
>   // Spans mulitple lines.
> }
> <<<
> $f = new Foo
> $f->first = 'Joe'
> $f->last = 'Bloggs'
> dump $f
> quite
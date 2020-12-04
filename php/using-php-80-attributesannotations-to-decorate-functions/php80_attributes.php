<?php
// Attributes (AKA Annotations).
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_FUNCTION)]
class CharDecoratorAttribute
{
    public function __construct(protected string $char) // Constructor Property Promotion
    {
    }

    public function decorate(Closure $fn): Closure
    {
        return fn(string $str): string => $this->char . $fn($str) . $this->char;
    }
}

// Function decorated using Attributes with Named Parameters.
$xmas =
    #[CharDecoratorAttribute(char: '🎄')]
    #[CharDecoratorAttribute(char: '⛄️')]
    #[CharDecoratorAttribute(char: '🎅')]
    fn (string $str): string => $str;

// Use Reflection to get and instantiate Attributes, and then decorate the function.
function decorateFn(Closure $fn) {
    return array_reduce(
        array: (new ReflectionFunction($fn))->getAttributes(CharDecoratorAttribute::class),
        callback: fn($fn, $attribute) => $attribute->newInstance()->decorate($fn),
        initial: $fn,
    );
}

echo decorateFn($xmas)(str: 'Merry Christmas'); // 🎅⛄️🎄Merry Christmas🎄⛄️🎅
<?php
final class SortAssoc
{
    public function __construct(private readonly array $array, private readonly array $columns = [])
    {
    }
    
    public function __invoke(): array
    {
        $array = $this->array;
        
        usort($array, function (array $a, array $b) {
            $left  = [];
            $right = [];

            foreach ($this->columns as $column) {
                $left[]  = $a[$column] ?? throw new \OutOfBoundsException(sprintf('Invalid filter key: %s', $column));
                $right[] = $b[$column] ?? throw new \OutOfBoundsException(sprintf('Invalid filter key: %s', $column));
            }

            return [$left] <=> [$right];
        });

        return $array;        
    }
}

$array = [
    ['name' => 'Joe Bloggs', 'age' => 42],
    ['name' => 'Jane Bloggs', 'age' => 24],
];

$sorted = (new SortAssoc($array, ['name', 'age']))->__invoke();
print_r($sorted);
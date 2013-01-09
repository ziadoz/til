<?php
// OpenStruct with Constructor Parameters
// Cannot Change Structure
$person = new OpenStruct(array('first' => 'Joe', 'last' => 'Bloggs', 'age' => 20));

// Nested Array becomes OpenStruct
$person->address = array(
  'address'  => '123 Alphabet Street', 
  'city'     => 'Awesome City', 
  'postcode' => 'ABC 123', 
  'country'  => 'Awesome Country'
);

$person->first;         // $person['first'];
$person->last;          // $person['last'];
$person->age;           // $person['age'];
$person->address->city; // $person['address']['city'];

// Nested OpenStruct
$person->hobbies = new OpenStruct(array('Football', 'Ice Hockey', 'Formula 1'));

// Traversable
foreach ($person->hobbies as $hobby) {
    echo 'Hobby: ' . $hobby . "\n";
}

// Convert to JSON
echo 'JSON: ' . $person->toJson() . "\n";
print_r($person);

// OpenStruct with no Constructor
$person         = new OpenStruct;
$person->name   = 'Joe';
$person['name'] = 'John';
print_r($person);

// Struct Requires Constructor Parameters
// Cannot Change Structure
try {
    $person = new Struct(array('first' => 'Joe', 'last' => 'Bloggs', 'hobbies' => array('Football', 'Ice Hockey', 'Formula 1')));
    print_r($person);
    $person->age = 30;
} catch (RuntimeException $e) {
    echo 'Cannot change Struct' . "\n";
}

// OpenStruct from JSON String
// Can Change Structure
$json = OpenStruct::fromJson('{"first":"Joe","last":"Bloggs","age":20,"address":{"address":"123 Alphabet Street","city":"Awesome City","postcode":"ABC 123","country":"Canada"}}');
$json->foo = 'bar';
print_r($json);

// Struct from JSON String
// Cannot Change Structure
try {
    $json = Struct::fromJson('{"first":"Joe","last":"Bloggs","age":20,"address":{"address":"123 Alphabet Street","city":"Awesome City","postcode":"ABC 123","country":"Canada"}}');
    print_r($json);
    $json->foo = 'bar';
} catch (RuntimeException $e) {
    echo 'Cannot change Struct' . "\n";
}

// OpenStructs with Closures
$open        = new OpenStruct;
$open->first = 'Joe';
$open->last  = 'Bloggs';

$open->fullName = function() use ($open) {
    return $open->first . ' ' . $open->last;
};

/*
// PHP 5.4 Style
$open->fullName = function() {
    return $this->first . ' ' . $this->last;
};
*/

$open->last = 'Smith';
print_r($open->fullName());

// OpenStruct from Object
$object         = new StdClass;
$object->first  = 'Joe';
$object->last   = 'Bloggs';

$open = new OpenStruct($object);
$open->first;
print_r($open);

// Struct from Object
$object         = new StdClass;
$object->first  = 'Joe';
$object->last   = 'Bloggs';

try {
    $open = new Struct($object);
    print_r($open);
    $open->age = 30;
} catch (RuntimeException $e) {
    echo 'Cannot change Struct' . "\n";
}
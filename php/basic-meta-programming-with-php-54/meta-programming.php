<?php
trait MetaClass
{
	protected $__classMethods = array();
	
	static protected $__staticMethods = array();
	
	
	public function __call($name, $args)
	{
		if (isset($this->__classMethods[$name]) && $this->__classMethods[$name] instanceof \Closure) {
			return call_user_func_array($this->__classMethods[$name], $args);
		}
		
		if (get_parent_class()) {
			return parent::__call($name, $args);
		}
		
		throw new \BadMethodCallException;
	}
	
	public function addMethod($name, Closure $method)
	{
		$this->__classMethods[$name] = $method;
		$this->__classMethods[$name] = $this->__classMethods[$name]->bindTo($this, $this);
	}
	
	public function removeMethod($name)
	{
		unset($this->__classMethods[$name]);
	}
	
	public static function __callStatic($name, $args)
	{
		if (isset(static::$__staticMethods[$name]) && static::$__staticMethods[$name] instanceof \Closure) {
			return forward_static_call_array(static::$__staticMethods[$name], $args);
		}
		
		if (get_parent_class()) {
			return parent::__call($name, $args);
		}
		
		throw new \BadMethodCallException;
	}
	
	public static function addStaticMethod($name, Closure $method) 
	{
		static::$__staticMethods[$name] = $method;
	}
	
	public static function removeStaticMethod($name)
	{
		unset(static::$__staticMethods[$name]);
	}
}

class Person
{
	use MetaClass;
	
	public static $colours = array('red', 'green', 'blue');
	
	public function __construct($name)
	{
		$this->name = $name;
		
		// Create class methods on the fly.
		foreach (static::$colours as $colour) {
			$method = 'say' . ucwords($colour);
			$this->addMethod($method, function() use ($colour) {
				echo '<p>' . ucwords($this->name) . ' likes the colour ' . ucwords($colour) . '!</p>';
			});
		}
		
		// Create instance methods on the fly.
		static::addStaticMethod('showColours', function() {
			echo 'Colours: ' . ucwords(implode(', ', static::$colours));
		});
	}
	
	public function getPandaName()
	{
		echo '<p>' . ucwords($this->name) . ' the happy panda!</p>';
	}
	
	public static function iLikePandas()
	{
		echo '<p>Everybody loves pandas</p>';
	}
}

class SuperHero extends Person
{
	
}

// Create a person.
$person = new Person('Jamie');

// Class methods created on the fly in the constructor.
$person->sayRed();
$person->sayGreen();
$person->sayBlue();

// Add a new method on the fly.
$person->addMethod('sayColour', function($colour) {
	static::$colours[] = $colour;
	echo '<p>' . ucwords($this->name) . ' loves the colour ' . ucwords($colour) . '!</p>';
});
$person->sayColour('Purple');

// Static method created on the fly in the constructor.
Person::showColours();

// Static method created on the fly.
Person::addStaticMethod('sayHappyThings', function() {
	echo '<p>Happy things, like peanut butter and jelly.</p>';
});
Person::sayHappyThings();

// Call regularly declared class method.
$person->getPandaName();

// Call regularly declared static method.
Person::iLikePandas();

// Remove a class method on the fly.
try {
	$person->removeMethod('sayColour');
	$person->sayColour();	
} catch (BadMethodCallException $exception) {
	echo '<p>Class method sayColour does not exist.</p>';
}

// Remove a static method on the fly.
try {
	Person::removeStaticMethod('sayHappyThings');
	Person::sayHappyThings();
} catch (BadMethodCallException $exception) {
	echo '<p>Static method sayHappyThings does not exist.</p>';
}

// Create a superhero.
$hero = new SuperHero('Batman');

// Class methods created on the fly in the parents constructor.
$hero->sayRed();
$hero->sayGreen();
$hero->sayBlue();

// Class method added to child class on the fly.
$hero->addMethod('sayStuff', function() {
	echo '<p>' . ucwords($this->name) . ' say: I am the Dark Knight!</p>';
});
$hero->sayStuff();

// Class methods not available to their parent classes.
try {
	$person->sayStuff();
} catch (BadMethodCallException $exception) {
	echo '<p>Class method sayStuff does not exist.</p>';
}
<?php
// See: https://gist.github.com/1942528
trait Call_Dynamic_Methods 
{
	public function __call($name, $arguments) 
	{
		if (isset($this->{$name}) && $this->{$name} instanceof Closure) {
			$this->{$name} = $this->{$name}->bindTo($this, $this);
			return call_user_func_array($this->{$name}, $arguments);
		}

		return parent::__call($name, $arguments);
	}
}

class Meta
{
	use Call_Dynamic_Methods;

	public $name;

	public static $colours = array('red', 'green', 'blue');
	
	public function __construct()
	{
		foreach (static::$colours as $colour) {
			$functionName = 'name' . ucfirst($colour);
			$this->{$functionName} = function() use ($colour) {
				return '<span style="color: ' . $colour . '">' . $this->name . '</span>';
			};
		}
	}
}

$meta = new Meta;
$meta->name = 'John Smith';
echo $meta->nameGreen();
echo $meta->nameRed();
$meta->name = 'Joe Bloggs';
echo $meta->nameBlue();

/*
Note: 
Could this be taken a step further by adding functions to trait for creating and removing methods on the fly? 
Then something like Ruby's attr_accessor functionality (and more) could be recreated?
*/
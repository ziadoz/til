<?php
use \SimpleXMLElement;
use \BadMethodCallException;

class XmlElement
{
    /**
     * The default XML DOCTYPE.
     *
     * @var string
     */
    const DOCTYPE = '<?xml version="1.0" encoding="UTF-8"?>';

    /**
     * The internal SimpleXMLElement.
     *
     * @var SimpleXMLElement
     */
    private $element;

    /**
     * Create a new element.
     *
     * @param $name
     */
    public function __construct($name)
    {
        $xml  = static::DOCTYPE;
        $xml .= '<' . $name . '></' . $name . '>';

        $this->element = new SimpleXMLElement($xml);
    }

    /*
     * Proxy methods to the underlying SimpleXMLElement.
     *
     * @param  string $method
     * @param  mixed  $args
     * @return mixed
     * @throw  \BadMethodCallException
     */
    public function __call($method, $args)
    {
        if (method_exists($this->element, $method)) {
            return call_user_func_array([$this->element, $method], $args);
        }

        throw new BadMethodCallException(sprintf('The method "%s" does not exist', $method));
    }

    /**
     * Return the XML, but strip out the DOCTYPE first.
     *
     * @param null $filename
     * @return mixed
     */
    public function asXML($filename = null)
    {
        $xml = $this->element->asXML();

        if (! $xml) {
            return false;
        }

        $xml = trim(str_replace(static::DOCTYPE, '', $xml));

        if (! $filename) {
            return $xml;
        }

        return file_put_contents($filename, $xml) > 0;
    }
}

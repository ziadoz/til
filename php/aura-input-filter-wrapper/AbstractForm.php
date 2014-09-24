<?php
/**
 * A simple wrapper around Aura Input.
 */
use Aura\Input\Form;
use Aura\Input\Builder;
use Aura\Input\Filter;
use Aura\Html\HelperLocatorFactory;
use Aura\Filter\FilterFactory;

class AbstractForm extends Form
{
    /**
     * The Aura HTML helper.
     *
     * @var HelperLocator
     **/
    protected $helper;

    /**
     * The form's namespace.
     *
     * @var string
     **/
    protected $namespace;

    /**
     * Construct a new form.
     *
     * @var string
     **/
    public function __construct($options = null)
    {
        $builder = new Builder();

        $filter = new FilterFactory();
        $filter = $filter->newInstance();

        $helper = new HelperLocatorFactory();
        $helper = $helper->newInstance();

        $this->builder = $builder;
        $this->filter  = $filter;
        $this->helper  = $helper;
        $this->options = $options;

        $this->init();
    }

    /**
     * Get an Aura HTML helper object.
     *
     * @return HelperLocator
     **/
    public function getHelper()
    {
        return $this->helper;
    }

    /**
     * Set the Aura HTML helper object.
     *
     * @param  HelperLocator $helper
     * @return void
     **/
    public function setHelper(HelperLocator $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Set the form namespace.
     *
     * @param  string $namespace
     * @return void
     **/
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Get the form namespace.
     *
     * @return string
     **/
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Get a form input by name without throwing exceptions (for Twig templates).
     *
     * @param  string $name
     * @return array
     **/
    public function getInput($name)
    {
        if (! isset($this->inputs[$name])) {
            return false;
        }

        return parent::getInput($name);
    }

    /**
     * Get the form inputs.
     *
     * @return array
     **/
    public function getInputs()
    {
        $inputs = array();
        foreach ($this->getInputNames() as $name) {
            $inputs[] = $this->getInput($name);
        }

        return $inputs;
    }

    /**
     * Get a namespaced field name for a form input.
     *
     * @param  string $name
     * @return string
     **/
    public function getInputNamespace($name)
    {
        if (isset($this->namespace) && ! empty($this->namespace)) {
            return $this->namespace . '[' . $name . ']';
        }

        return $name;
    }
}
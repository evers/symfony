<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection;

/**
 * Definition represents a service definition.
 *
 * @author Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class Definition
{
    protected $class;
    protected $file;
    protected $factoryMethod;
    protected $factoryService;
    protected $scope;
    protected $arguments;
    protected $calls;
    protected $configurator;
    protected $tags;
    protected $public;
    protected $synthetic;
    protected $abstract;

    /**
     * Constructor.
     *
     * @param string $class     The service class
     * @param array  $arguments An array of arguments to pass to the service constructor
     */
    public function __construct($class = null, array $arguments = array())
    {
        $this->class = $class;
        $this->arguments = $arguments;
        $this->calls = array();
        $this->scope = ContainerInterface::SCOPE_CONTAINER;
        $this->tags = array();
        $this->public = true;
        $this->synthetic = false;
        $this->abstract = false;
    }

    /**
     * Sets the factory method able to create an instance of this class.
     *
     * @param  string $method The method name
     *
     * @return Definition The current instance
     */
    public function setFactoryMethod($method)
    {
        $this->factoryMethod = $method;

        return $this;
    }

    /**
     * Gets the factory method.
     *
     * @return string The factory method name
     */
    public function getFactoryMethod()
    {
        return $this->factoryMethod;
    }

    /**
     * Sets the name of the service that acts as a factory using the constructor method.
     *
     * @param string $factoryService The factory service id
     *
     * @return Definition The current instance
     */
    public function setFactoryService($factoryService)
    {
        $this->factoryService = $factoryService;

        return $this;
    }

    /**
     * Gets the factory service id.
     *
     * @return string The factory service id
     */
    public function getFactoryService()
    {
        return $this->factoryService;
    }

    /**
     * Sets the service class.
     *
     * @param  string $class The service class
     *
     * @return Definition The current instance
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Sets the service class.
     *
     * @return string The service class
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Sets the arguments to pass to the service constructor/factory method.
     *
     * @param  array $arguments An array of arguments
     *
     * @return Definition The current instance
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * Adds an argument to pass to the service constructor/factory method.
     *
     * @param  mixed $argument An argument
     *
     * @return Definition The current instance
     */
    public function addArgument($argument)
    {
        $this->arguments[] = $argument;

        return $this;
    }

    /**
     * Sets a specific argument
     *
     * @param integer $index
     * @param mixed $argument
     *
     * @return Definition The current instance
     */
    public function setArgument($index, $argument)
    {
        if ($index < 0 || $index > count($this->arguments) - 1) {
            throw new \OutOfBoundsException(sprintf('The index "%d" is not in the range [0, %d].', $index, count($this->arguments) - 1));
        }

        $this->arguments[$index] = $argument;

        return $this;
    }

    /**
     * Gets the arguments to pass to the service constructor/factory method.
     *
     * @return array The array of arguments
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Sets the methods to call after service initialization.
     *
     * @param  array $calls An array of method calls
     *
     * @return Definition The current instance
     */
    public function setMethodCalls(array $calls = array())
    {
        $this->calls = array();
        foreach ($calls as $call) {
            $this->addMethodCall($call[0], $call[1]);
        }

        return $this;
    }

    /**
     * Adds a method to call after service initialization.
     *
     * @param  string $method    The method name to call
     * @param  array  $arguments An array of arguments to pass to the method call
     *
     * @return Definition The current instance
     */
    public function addMethodCall($method, array $arguments = array())
    {
        $this->calls[] = array($method, $arguments);

        return $this;
    }

    /**
     * Removes a method to call after service initialization.
     *
     * @param  string $method    The method name to remove
     *
     * @return Definition The current instance
     */
    public function removeMethodCall($method)
    {
        foreach ($this->calls as $i => $call) {
            if ($call[0] === $method) {
                unset($this->calls[$i]);
                break;
            }
        }

        return $this;
    }

    /**
     * Check if the current definition has a given method to call after service initialization.
     *
     * @param  string $method    The method name to search for
     *
     * @return Boolean
     */
    public function hasMethodCall($method)
    {
        foreach ($this->calls as $i => $call) {
            if ($call[0] === $method) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets the methods to call after service initialization.
     *
     * @return  array An array of method calls
     */
    public function getMethodCalls()
    {
        return $this->calls;
    }

    /**
     * Sets tags for this definition
     *
     * @param array $tags
     *
     * @return Definition the current instance
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Returns all tags.
     *
     * @return array An array of tags
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Gets a tag by name.
     *
     * @param  string $name The tag name
     *
     * @return array An array of attributes
     */
    public function getTag($name)
    {
        return isset($this->tags[$name]) ? $this->tags[$name] : array();
    }

    /**
     * Adds a tag for this definition.
     *
     * @param  string $name       The tag name
     * @param  array  $attributes An array of attributes
     *
     * @return Definition The current instance
     */
    public function addTag($name, array $attributes = array())
    {
        if (!isset($this->tags[$name])) {
            $this->tags[$name] = array();
        }

        $this->tags[$name][] = $attributes;

        return $this;
    }

    /**
     * Whether this definition has a tag with the given name
     *
     * @param string $name
     *
     * @return Boolean
     */
    public function hasTag($name)
    {
        return isset($this->tags[$name]);
    }

    /**
     * Clears the tags for this definition.
     *
     * @return Definition The current instance
     */
    public function clearTags()
    {
        $this->tags = array();

        return $this;
    }

    /**
     * Sets a file to require before creating the service.
     *
     * @param  string $file A full pathname to include
     *
     * @return Definition The current instance
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Gets the file to require before creating the service.
     *
     * @return string The full pathname to include
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Sets the scope of the service
     *
     * @param  string $string Whether the service must be shared or not
     *
     * @return Definition The current instance
     */
    public function setScope($scope)
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * Returns the scope of the service
     *
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Sets the visibility of this service.
     *
     * @param Boolean $boolean
     * @return Definition The current instance
     */
    public function setPublic($boolean)
    {
        $this->public = (Boolean) $boolean;

        return $this;
    }

    /**
     * Whether this service is public facing
     *
     * @return Boolean
     */
    public function isPublic()
    {
        return $this->public;
    }

    /**
     * Sets whether this definition is synthetic, that is not constructed by the
     * container, but dynamically injected.
     *
     * @param Boolean $boolean
     *
     * @return Definition the current instance
     */
    public function setSynthetic($boolean)
    {
        $this->synthetic = (Boolean) $boolean;

        return $this;
    }

    /**
     * Whether this definition is synthetic, that is not constructed by the
     * container, but dynamically injected.
     *
     * @return Boolean
     */
    public function isSynthetic()
    {
        return $this->synthetic;
    }

    /**
     * Whether this definition is abstract, that means it merely serves as a
     * template for other definitions.
     *
     * @param Boolean $boolean
     *
     * @return Definition the current instance
     */
    public function setAbstract($boolean)
    {
        $this->abstract = (Boolean) $boolean;

        return $this;
    }

    /**
     * Whether this definition is abstract, that means it merely serves as a
     * template for other definitions.
     *
     * @return Boolean
     */
    public function isAbstract()
    {
        return $this->abstract;
    }

    /**
     * Sets a configurator to call after the service is fully initialized.
     *
     * @param  mixed $callable A PHP callable
     *
     * @return Definition The current instance
     */
    public function setConfigurator($callable)
    {
        $this->configurator = $callable;

        return $this;
    }

    /**
     * Gets the configurator to call after the service is fully initialized.
     *
     * @return mixed The PHP callable to call
     */
    public function getConfigurator()
    {
        return $this->configurator;
    }
}

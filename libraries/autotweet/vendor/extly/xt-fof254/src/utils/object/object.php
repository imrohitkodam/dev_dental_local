<?php

/*
 * @package     XT Transitional Package from FrameworkOnFramework
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2024 Extly, CB. All rights reserved.
 *              Based on Akeeba's FrameworkOnFramework
 * @license     https://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * @see         https://www.extly.com
 */

defined('XTF0F_INCLUDED') || exit;

/**
 * Temporary class for backwards compatibility. You should not be using this
 * in your code. It is currently present to handle the validation error stack
 * for XTF0FTable::check() and will be removed in an upcoming version.
 *
 * This class is based on JObject as found in Joomla! 3.2.1
 *
 * @deprecated  2.1
 *
 * @codeCoverageIgnore
 */
class XTF0FUtilsObject
{
    /**
     * An array of error messages or Exception objects.
     *
     * @var array
     */
    protected $_errors = [];

    /**
     * Class constructor, overridden in descendant classes.
     *
     * @param mixed $properties either and associative array or another
     *                          object to set the initial properties of the object
     */
    public function __construct($properties = null)
    {
        if (null !== $properties) {
            $this->setProperties($properties);
        }
    }

    /**
     * Magic method to convert the object to a string gracefully.
     *
     * @return string the classname
     */
    public function __toString()
    {
        return static::class;
    }

    /**
     * Sets a default value if not alreay assigned
     *
     * @param string $property the name of the property
     * @param mixed  $default  the default value
     */
    public function def($property, $default = null)
    {
        $value = $this->get($property, $default);

        return $this->set($property, $value);
    }

    /**
     * Returns a property of the object or the default value if the property is not set.
     *
     * @param string $property the name of the property
     * @param mixed  $default  the default value
     *
     * @return mixed the value of the property
     */
    public function get($property, $default = null)
    {
        return $this->$property ?? $default;
    }

    /**
     * Returns an associative array of object properties.
     *
     * @param bool $public if true, returns only the public properties
     *
     * @return array
     */
    public function getProperties($public = true)
    {
        $vars = get_object_vars($this);
        if ($public) {
            foreach (array_keys($vars) as $key) {
                if ('_' === substr($key, 0, 1)) {
                    unset($vars[$key]);
                }
            }
        }

        return $vars;
    }

    /**
     * Get the most recent error message.
     *
     * @param int  $i        option error index
     * @param bool $toString indicates if JError objects should return their error message
     *
     * @return string Error message
     */
    public function getError($i = null, $toString = true)
    {
        // Find the error
        if (null === $i) {
            // Default, return the last message
            $error = end($this->_errors);
        } elseif (!array_key_exists($i, $this->_errors)) {
            // If $i has been specified but does not exist, return false
            return false;
        } else {
            $error = $this->_errors[$i];
        }

        // Check if only the string is requested
        if ($error instanceof Exception && $toString) {
            return (string) $error;
        }

        return $error;
    }

    /**
     * Return all errors, if any.
     *
     * @return array array of error messages or JErrors
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Modifies a property of the object, creating it if it does not already exist.
     *
     * @param string $property the name of the property
     * @param mixed  $value    the value of the property to set
     *
     * @return mixed previous value of the property
     */
    public function set($property, $value = null)
    {
        $previous = $this->$property ?? null;
        $this->$property = $value;

        return $previous;
    }

    /**
     * Set the object properties based on a named array/hash.
     *
     * @param mixed $properties either an associative array or another object
     *
     * @return bool
     */
    public function setProperties($properties)
    {
        if (is_array($properties) || is_object($properties)) {
            foreach ((array) $properties as $k => $v) {
                // Use the set function which might be overridden.
                $this->set($k, $v);
            }

            return true;
        }

        return false;
    }

    /**
     * Add an error message.
     *
     * @param string $error error message
     *
     * @return void
     */
    public function setError($error)
    {
        $this->_errors[] = $error;
    }
}

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

// Protect from unauthorized access
defined('XTF0F_INCLUDED') || exit;

/**
 * Query Element Class.
 *
 * @property string $name     The name of the element.
 * @property array  $elements An array of elements.
 * @property string $glue     Glue piece.
 *
 * @since  11.1
 */
class XTF0FDatabaseQueryElement
{
    /**
     * @var string the name of the element
     *
     * @since  11.1
     */
    protected $name = null;

    /**
     * @var array an array of elements
     *
     * @since  11.1
     */
    protected $elements = [];

    /**
     * @var string glue piece
     *
     * @since  11.1
     */
    protected $glue = null;

    /**
     * Constructor.
     *
     * @param string $name     the name of the element
     * @param mixed  $elements string or array
     * @param string $glue     the glue for elements
     *
     * @since   11.1
     */
    public function __construct($name, $elements, $glue = ',')
    {
        $this->name = $name;
        $this->glue = $glue;

        $this->append($elements);
    }

    /**
     * Magic function to convert the query element to a string.
     *
     * @return string
     *
     * @since   11.1
     */
    public function __toString()
    {
        if ('()' === substr($this->name, -2)) {
            return \PHP_EOL.substr($this->name, 0, -2).'('.implode($this->glue, $this->elements).')';
        } else {
            return \PHP_EOL.$this->name.' '.implode($this->glue, $this->elements);
        }
    }

    /**
     * Method to provide deep copy support to nested objects and arrays
     * when cloning.
     *
     * @return void
     *
     * @since   11.3
     */
    public function __clone()
    {
        foreach ($this as $k => $v) {
            if (is_object($v) || is_array($v)) {
                $this->{$k} = unserialize(serialize($v));
            }
        }
    }

    /**
     * Appends element parts to the internal list.
     *
     * @param mixed $elements string or array
     *
     * @return void
     *
     * @since   11.1
     */
    public function append($elements)
    {
        if (is_array($elements)) {
            $this->elements = array_merge($this->elements, $elements);
        } else {
            $this->elements = array_merge($this->elements, [$elements]);
        }
    }

    /**
     * Gets the elements of this element.
     *
     * @return array
     *
     * @since   11.1
     */
    public function getElements()
    {
        return $this->elements;
    }
}

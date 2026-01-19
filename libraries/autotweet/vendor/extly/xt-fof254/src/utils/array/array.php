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
 * A utility class to handle array manipulation.
 *
 * Based on the \Joomla\Utilities\ArrayHelper class as found in Joomla! 3.2.0
 */
abstract class XTF0FUtilsArray
{
    /**
     * Option to perform case-sensitive sorts.
     *
     * @var mixed boolean or array of booleans
     */
    protected static $sortCase;

    /**
     * Option to set the sort direction.
     *
     * @var mixed integer or array of integers
     */
    protected static $sortDirection;

    /**
     * Option to set the object key to sort on.
     *
     * @var string
     */
    protected static $sortKey;

    /**
     * Option to perform a language aware sort.
     *
     * @var mixed boolean or array of booleans
     */
    protected static $sortLocale;

    /**
     * Function to convert array to integer values
     *
     * @param array &$array  The source array to convert
     * @param mixed $default A default value (int|array) to assign if $array is not an array
     *
     * @return void
     */
    public static function toInteger(&$array, $default = null)
    {
        if (is_array($array)) {
            foreach ($array as $i => $v) {
                $array[$i] = (int) $v;
            }
        } elseif (null === $default) {
            $array = [];
        } elseif (is_array($default)) {
            self::toInteger($default, null);
            $array = $default;
        } else {
            $array = [(int) $default];
        }
    }

    /**
     * Utility function to map an array to a stdClass object.
     *
     * @param array  &$array The array to map
     * @param string $class  Name of the class to create
     *
     * @return object The object mapped from the given array
     */
    public static function toObject(&$array, $class = 'stdClass')
    {
        $obj = null;

        if (is_array($array)) {
            $obj = new $class();

            foreach ($array as $k => $v) {
                $obj->$k = is_array($v) ? self::toObject($v, $class) : $v;
            }
        }

        return $obj;
    }

    /**
     * Utility function to map an array to a string.
     *
     * @param array  $array        the array to map
     * @param string $inner_glue   the glue (optional, defaults to '=') between the key and the value
     * @param string $outer_glue   the glue (optional, defaults to ' ') between array elements
     * @param bool   $keepOuterKey true if final key should be kept
     *
     * @return string The string mapped from the given array
     */
    public static function toString($array = null, $inner_glue = '=', $outer_glue = ' ', $keepOuterKey = false)
    {
        $output = [];

        if (is_array($array)) {
            foreach ($array as $key => $item) {
                if (is_array($item)) {
                    if ($keepOuterKey) {
                        $output[] = $key;
                    }

                    // This is value is an array, go and do it again!
                    $output[] = self::toString($item, $inner_glue, $outer_glue, $keepOuterKey);
                } else {
                    $output[] = $key.$inner_glue.'"'.$item.'"';
                }
            }
        }

        return implode($outer_glue, $output);
    }

    /**
     * Utility function to map an object to an array
     *
     * @param object $p_obj   The source object
     * @param bool   $recurse True to recurse through multi-level objects
     * @param string $regex   An optional regular expression to match on field names
     *
     * @return array The array mapped from the given object
     */
    public static function fromObject($p_obj, $recurse = true, $regex = null)
    {
        if (is_object($p_obj)) {
            return self::_fromObject($p_obj, $recurse, $regex);
        } else {
            return null;
        }
    }

    /**
     * Extracts a column from an array of arrays or objects
     *
     * @param array  &$array The source array
     * @param string $index  The index of the column or name of object property
     *
     * @return array Column of values from the source array
     */
    public static function getColumn(&$array, $index)
    {
        $result = [];

        if (is_array($array)) {
            foreach ($array as &$item) {
                if (is_array($item) && isset($item[$index])) {
                    $result[] = $item[$index];
                } elseif (is_object($item) && isset($item->$index)) {
                    $result[] = $item->$index;
                }

                // Else ignore the entry
            }
        }

        return $result;
    }

    /**
     * Utility function to return a value from a named array or a specified default
     *
     * @param array  &$array  A named array
     * @param string $name    The key to search for
     * @param mixed  $default The default value to give if no key found
     * @param string $type    Return type for the variable (INT, FLOAT, STRING, WORD, BOOLEAN, ARRAY)
     *
     * @return mixed The value from the source array
     */
    public static function getValue(&$array, $name, $default = null, $type = '')
    {
        $result = null;

        if (isset($array[$name])) {
            $result = $array[$name];
        }

        // Handle the default case
        if (null === $result) {
            $result = $default;
        }

        // Handle the type constraint
        switch (strtoupper($type)) {
            case 'INT':
            case 'INTEGER':
                // Only use the first integer value
                @preg_match('/-?\d+/', $result, $matches);
                $result = @(int) $matches[0];
                break;

            case 'FLOAT':
            case 'DOUBLE':
                // Only use the first floating point value
                @preg_match('/-?\d+(\.\d+)?/', $result, $matches);
                $result = @(float) $matches[0];
                break;

            case 'BOOL':
            case 'BOOLEAN':
                $result = (bool) $result;
                break;

            case 'ARRAY':
                if (!is_array($result)) {
                    $result = [$result];
                }

                break;

            case 'STRING':
                $result = (string) $result;
                break;

            case 'WORD':
                $result = (string) preg_replace('#\W#', '', $result);
                break;

            case 'NONE':
            default:
                // No casting necessary
                break;
        }

        return $result;
    }

    /**
     * Takes an associative array of arrays and inverts the array keys to values using the array values as keys.
     *
     * Example:
     * $input = array(
     *     'New' => array('1000', '1500', '1750'),
     *     'Used' => array('3000', '4000', '5000', '6000')
     * );
     * $output = XTF0FUtilsArray::invert($input);
     *
     * Output would be equal to:
     * $output = array(
     *     '1000' => 'New',
     *     '1500' => 'New',
     *     '1750' => 'New',
     *     '3000' => 'Used',
     *     '4000' => 'Used',
     *     '5000' => 'Used',
     *     '6000' => 'Used'
     * );
     *
     * @param array $array the source array
     *
     * @return array the inverted array
     */
    public static function invert($array)
    {
        $return = [];

        foreach ($array as $base => $values) {
            if (!is_array($values)) {
                continue;
            }

            foreach ($values as $value) {
                // If the key isn't scalar then ignore it.
                if (is_scalar($value)) {
                    $return[$value] = $base;
                }
            }
        }

        return $return;
    }

    /**
     * Method to determine if an array is an associative array.
     *
     * @param array $array an array to test
     *
     * @return bool true if the array is an associative array
     */
    public static function isAssociative($array)
    {
        if (is_array($array)) {
            foreach (array_keys($array) as $k => $v) {
                if ($k !== $v) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Pivots an array to create a reverse lookup of an array of scalars, arrays or objects.
     *
     * @param array  $source the source array
     * @param string $key    where the elements of the source array are objects or arrays, the key to pivot on
     *
     * @return array an array of arrays pivoted either on the value of the keys, or an individual key of an object or array
     */
    public static function pivot($source, $key = null)
    {
        $result = [];
        $counter = [];

        foreach ($source as $index => $value) {
            // Determine the name of the pivot key, and its value.
            if (is_array($value)) {
                // If the key does not exist, ignore it.
                if (!isset($value[$key])) {
                    continue;
                }

                $resultKey = $value[$key];
                $resultValue = &$source[$index];
            } elseif (is_object($value)) {
                // If the key does not exist, ignore it.
                if (!isset($value->$key)) {
                    continue;
                }

                $resultKey = $value->$key;
                $resultValue = &$source[$index];
            } else {
                // Just a scalar value.
                $resultKey = $value;
                $resultValue = $index;
            }

            // The counter tracks how many times a key has been used.
            if (empty($counter[$resultKey])) {
                // The first time around we just assign the value to the key.
                $result[$resultKey] = $resultValue;
                $counter[$resultKey] = 1;
            } elseif (1 == $counter[$resultKey]) {
                // If there is a second time, we convert the value into an array.
                $result[$resultKey] = [
                    $result[$resultKey],
                    $resultValue,
                ];
                $counter[$resultKey]++;
            } else {
                // After the second time, no need to track any more. Just append to the existing array.
                $result[$resultKey][] = $resultValue;
            }
        }

        unset($counter);

        return $result;
    }

    /**
     * Utility function to sort an array of objects on a given field
     *
     * @param array &$a            An array of objects
     * @param mixed $k             The key (string) or a array of key to sort on
     * @param mixed $direction     Direction (integer) or an array of direction to sort in [1 = Ascending] [-1 = Descending]
     * @param mixed $caseSensitive Boolean or array of booleans to let sort occur case sensitive or insensitive
     * @param mixed $locale        Boolean or array of booleans to let sort occur using the locale language or not
     *
     * @return array The sorted array of objects
     */
    public static function sortObjects(&$a, $k, $direction = 1, $caseSensitive = true, $locale = false)
    {
        if (!is_array($locale) || !is_array($locale[0])) {
            $locale = [$locale];
        }

        self::$sortCase = (array) $caseSensitive;
        self::$sortDirection = (array) $direction;
        self::$sortKey = (array) $k;
        self::$sortLocale = $locale;

        usort($a, [self::class, '_sortObjects']);

        self::$sortCase = null;
        self::$sortDirection = null;
        self::$sortKey = null;
        self::$sortLocale = null;

        return $a;
    }

    /**
     * Multidimensional array safe unique test
     *
     * @param array $myArray the array to make unique
     *
     * @return array
     *
     * @see     http://php.net/manual/en/function.array-unique.php
     */
    public static function arrayUnique($myArray)
    {
        if (!is_array($myArray)) {
            return $myArray;
        }

        foreach ($myArray as &$myvalue) {
            $myvalue = serialize($myvalue);
        }

        $myArray = array_unique($myArray);

        foreach ($myArray as &$myvalue) {
            $myvalue = unserialize($myvalue);
        }

        return $myArray;
    }

    /**
     * Utility function to map an object or array to an array
     *
     * @param mixed  $item    The source object or array
     * @param bool   $recurse True to recurse through multi-level objects
     * @param string $regex   An optional regular expression to match on field names
     *
     * @return array The array mapped from the given object
     */
    protected static function _fromObject($item, $recurse, $regex)
    {
        if (is_object($item)) {
            $result = [];

            foreach (get_object_vars($item) as $k => $v) {
                if (!$regex || preg_match($regex, $k)) {
                    $result[$k] = $recurse ? self::_fromObject($v, $recurse, $regex) : $v;
                }
            }
        } elseif (is_array($item)) {
            $result = [];

            foreach ($item as $k => $v) {
                $result[$k] = self::_fromObject($v, $recurse, $regex);
            }
        } else {
            $result = $item;
        }

        return $result;
    }

    /**
     * Callback function for sorting an array of objects on a key
     *
     * @param array &$a An array of objects
     * @param array &$b An array of objects
     *
     * @return int Comparison status
     *
     * @see     XTF0FUtilsArray::sortObjects()
     */
    protected static function _sortObjects(&$a, &$b)
    {
        $key = self::$sortKey;

        for ($i = 0, $count = count($key); $i < $count; $i++) {
            if (isset(self::$sortDirection[$i])) {
                $direction = self::$sortDirection[$i];
            }

            if (isset(self::$sortCase[$i])) {
                $caseSensitive = self::$sortCase[$i];
            }

            if (isset(self::$sortLocale[$i])) {
                $locale = self::$sortLocale[$i];
            }

            $va = $a->{$key[$i]};
            $vb = $b->{$key[$i]};

            if ((is_bool($va) || is_numeric($va)) && (is_bool($vb) || is_numeric($vb))) {
                $cmp = $va - $vb;
            } elseif ($caseSensitive) {
                $cmp = strcmp($va, $vb, $locale);
            } else {
                $cmp = strcasecmp($va, $vb, $locale);
            }

            if ($cmp > 0) {
                return $direction;
            }

            if ($cmp < 0) {
                return -$direction;
            }
        }

        return 0;
    }
}

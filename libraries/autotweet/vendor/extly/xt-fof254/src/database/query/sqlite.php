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
 * SQLite Query Building Class.
 *
 * @since  12.1
 */
class XTF0FDatabaseQuerySqlite extends XTF0FDatabaseQueryPdo implements XTF0FDatabaseQueryPreparable, XTF0FDatabaseQueryLimitable
{
    /**
     * @var int the offset for the result set
     *
     * @since  12.1
     */
    protected $offset;

    /**
     * @var int the limit for the result set
     *
     * @since  12.1
     */
    protected $limit;

    /**
     * @var array Bounded object array
     *
     * @since  12.1
     */
    protected $bounded = [];

    /**
     * Method to add a variable to an internal array that will be bound to a prepared SQL statement before query execution. Also
     * removes a variable that has been bounded from the internal bounded array when the passed in value is null.
     *
     * @param string|int $key           The key that will be used in your SQL query to reference the value. Usually of
     *                                  the form ':key', but can also be an integer.
     * @param mixed      &$value        The value that will be bound. The value is passed by reference to support output
     *                                  parameters such as those possible with stored procedures.
     * @param int        $dataType      constant corresponding to a SQL datatype
     * @param int        $length        The length of the variable. Usually required for OUTPUT parameters.
     * @param array      $driverOptions optional driver options to be used
     *
     * @return XTF0FDatabaseQuerySqlite
     *
     * @since   12.1
     */
    public function bind($key = null, &$value = null, $dataType = PDO::PARAM_STR, $length = 0, $driverOptions = [])
    {
        // Case 1: Empty Key (reset $bounded array)
        if (empty($key)) {
            $this->bounded = [];

            return $this;
        }

        // Case 2: Key Provided, null value (unset key from $bounded array)
        if (null === $value) {
            if (isset($this->bounded[$key])) {
                unset($this->bounded[$key]);
            }

            return $this;
        }

        $obj = new stdClass();

        $obj->value = &$value;
        $obj->dataType = $dataType;
        $obj->length = $length;
        $obj->driverOptions = $driverOptions;

        // Case 3: Simply add the Key/Value into the bounded array
        $this->bounded[$key] = $obj;

        return $this;
    }

    /**
     * Retrieves the bound parameters array when key is null and returns it by reference. If a key is provided then that item is
     * returned.
     *
     * @param mixed $key the bounded variable key to retrieve
     *
     * @since   12.1
     */
    public function &getBounded($key = null)
    {
        if (empty($key)) {
            return $this->bounded;
        } elseif (isset($this->bounded[$key])) {
            return $this->bounded[$key];
        }

        return null;
    }

    /**
     * Gets the number of characters in a string.
     *
     * Note, use 'length' to find the number of bytes in a string.
     *
     * Usage:
     * $query->select($query->charLength('a'));
     *
     * @param string $field     a value
     * @param string $operator  Comparison operator between charLength integer value and $condition
     * @param string $condition integer value to compare charLength with
     *
     * @return string the required char length call
     *
     * @since   13.1
     */
    public function charLength($field, $operator = null, $condition = null)
    {
        return 'length('.$field.')'.(isset($operator) && isset($condition) ? ' '.$operator.' '.$condition : '');
    }

    /**
     * Clear data from the query or a specific clause of the query.
     *
     * @param string $clause optionally, the name of the clause to clear, or nothing to clear the whole query
     *
     * @return XTF0FDatabaseQuerySqlite returns this object to allow chaining
     *
     * @since   12.1
     */
    public function clear($clause = null)
    {
        if ($clause === null) {
            $this->bounded = [];
        }

        parent::clear($clause);

        return $this;
    }

    /**
     * Concatenates an array of column names or values.
     *
     * Usage:
     * $query->select($query->concatenate(array('a', 'b')));
     *
     * @param array  $values    an array of values to concatenate
     * @param string $separator as separator to place between each value
     *
     * @return string the concatenated values
     *
     * @since   11.1
     */
    public function concatenate($values, $separator = null)
    {
        if ($separator) {
            return implode(' || '.$this->quote($separator).' || ', $values);
        } else {
            return implode(' || ', $values);
        }
    }

    /**
     * Method to modify a query already in string format with the needed
     * additions to make the query limited to a particular number of
     * results, or start at a particular offset. This method is used
     * automatically by the __toString() method if it detects that the
     * query implements the XTF0FDatabaseQueryLimitable interface.
     *
     * @param string $query  The query in string format
     * @param int    $limit  The limit for the result set
     * @param int    $offset The offset for the result set
     *
     * @return string
     *
     * @since   12.1
     */
    public function processLimit($query, $limit, $offset = 0)
    {
        if ($limit > 0 || $offset > 0) {
            $query .= ' LIMIT '.$offset.', '.$limit;
        }

        return $query;
    }

    /**
     * Sets the offset and limit for the result set, if the database driver supports it.
     *
     * Usage:
     * $query->setLimit(100, 0); (retrieve 100 rows, starting at first record)
     * $query->setLimit(50, 50); (retrieve 50 rows, starting at 50th record)
     *
     * @param int $limit  The limit for the result set
     * @param int $offset The offset for the result set
     *
     * @return XTF0FDatabaseQuerySqlite returns this object to allow chaining
     *
     * @since   12.1
     */
    public function setLimit($limit = 0, $offset = 0)
    {
        $this->limit = (int) $limit;
        $this->offset = (int) $offset;

        return $this;
    }

    /**
     * Add to the current date and time.
     * Usage:
     * $query->select($query->dateAdd());
     * Prefixing the interval with a - (negative sign) will cause subtraction to be used.
     *
     * @param datetime $date     The date or datetime to add to
     * @param string   $interval The string representation of the appropriate number of units
     * @param string   $datePart The part of the date to perform the addition on
     *
     * @return string The string with the appropriate sql for addition of dates
     *
     * @since   13.1
     * @see    http://www.sqlite.org/lang_datefunc.html
     */
    public function dateAdd($date, $interval, $datePart)
    {
        // SQLite does not support microseconds as a separate unit. Convert the interval to seconds
        if (0 == strcasecmp($datePart, 'microseconds')) {
            $interval = .001 * $interval;
            $datePart = 'seconds';
        }

        if ('-' !== substr($interval, 0, 1)) {
            return "datetime('".$date."', '+".$interval.' '.$datePart."')";
        } else {
            return "datetime('".$date."', '".$interval.' '.$datePart."')";
        }
    }

    /**
     * Gets the current date and time.
     *
     * Usage:
     * $query->where('published_up < '.$query->currentTimestamp());
     *
     * @return string
     *
     * @since   3.4
     */
    public function currentTimestamp()
    {
        return 'CURRENT_TIMESTAMP';
    }
}

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
 * Query Building Class.
 *
 * @since  11.1
 */
class XTF0FDatabaseQueryMysqli extends XTF0FDatabaseQuery implements XTF0FDatabaseQueryLimitable
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
     * Method to modify a query already in string format with the needed
     * additions to make the query limited to a particular number of
     * results, or start at a particular offset.
     *
     * @param string $query  The query in string format
     * @param int    $limit  The limit for the result set
     * @param int    $offset The offset for the result set
     *
     * @return string
     *
     * @since 12.1
     */
    public function processLimit($query, $limit, $offset = 0)
    {
        if ($limit > 0 || $offset > 0) {
            $query .= ' LIMIT '.$offset.', '.$limit;
        }

        return $query;
    }

    /**
     * Concatenates an array of column names or values.
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
            $concat_string = 'CONCAT_WS('.$this->quote($separator);

            foreach ($values as $value) {
                $concat_string .= ', '.$value;
            }

            return $concat_string.')';
        } else {
            return 'CONCAT('.implode(',', $values).')';
        }
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
     * @return XTF0FDatabaseQuery returns this object to allow chaining
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
     * Return correct regexp operator for mysqli.
     *
     * Ensure that the regexp operator is mysqli compatible.
     *
     * Usage:
     * $query->where('field ' . $query->regexp($search));
     *
     * @param string $value the regex pattern
     *
     * @return string returns the regex operator
     *
     * @since   11.3
     */
    public function regexp($value)
    {
        return ' REGEXP '.$value;
    }

    /**
     * Return correct rand() function for Mysql.
     *
     * Ensure that the rand() function is Mysql compatible.
     *
     * Usage:
     * $query->Rand();
     *
     * @return string the correct rand function
     *
     * @since   3.5
     */
    public function Rand()
    {
        return ' RAND() ';
    }
}

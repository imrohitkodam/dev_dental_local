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
 * PostgreSQL database iterator.
 */
class XTF0FDatabaseIteratorPostgresql extends XTF0FDatabaseIterator
{
    /**
     * Get the number of rows in the result set for the executed SQL given by the cursor.
     *
     * @return int the number of rows in the result set
     *
     * @see     Countable::count()
     */
    public function count()
    {
        return @pg_num_rows($this->cursor);
    }

    /**
     * Method to fetch a row from the result set cursor as an object.
     *
     * @return mixed either the next row from the result set or false if there are no more rows
     */
    protected function fetchObject()
    {
        return @pg_fetch_object($this->cursor, null, $this->class);
    }

    /**
     * Method to free up the memory used for the result set.
     *
     * @return void
     */
    protected function freeResult()
    {
        @pg_free_result($this->cursor);
    }
}

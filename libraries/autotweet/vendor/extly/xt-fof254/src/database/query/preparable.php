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

if (!interface_exists('JDatabaseQueryPreparable')) {
    /**
     * Joomla Database Query Preparable Interface.
     * Adds bind/unbind methods as well as a getBounded() method
     * to retrieve the stored bounded variables on demand prior to
     * query execution.
     *
     * @since  12.1
     */
    interface JDatabaseQueryPreparable
    {
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
         * @return XTF0FDatabaseQuery
         *
         * @since   12.1
         */
        public function bind($key = null, &$value = null, $dataType = PDO::PARAM_STR, $length = 0, $driverOptions = []);

        /**
         * Retrieves the bound parameters array when key is null and returns it by reference. If a key is provided then that item is
         * returned.
         *
         * @param mixed $key the bounded variable key to retrieve
         *
         * @since   12.1
         */
        public function &getBounded($key = null);
    }
}

interface XTF0FDatabaseQueryPreparable extends JDatabaseQueryPreparable
{
}

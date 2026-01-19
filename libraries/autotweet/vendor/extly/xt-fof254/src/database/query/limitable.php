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

if (!interface_exists('JDatabaseQueryLimitable')) {
    /**
     * Joomla Database Query Limitable Interface.
     * Adds bind/unbind methods as well as a getBounded() method
     * to retrieve the stored bounded variables on demand prior to
     * query execution.
     *
     * @since  12.1
     */
    interface JDatabaseQueryLimitable
    {
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
        public function processLimit($query, $limit, $offset = 0);

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
        public function setLimit($limit = 0, $offset = 0);
    }
}

/**
 * Joomla Database Query Limitable Interface.
 * Adds bind/unbind methods as well as a getBounded() method
 * to retrieve the stored bounded variables on demand prior to
 * query execution.
 *
 * @since  12.1
 */
interface XTF0FDatabaseQueryLimitable extends JDatabaseQueryLimitable
{
}

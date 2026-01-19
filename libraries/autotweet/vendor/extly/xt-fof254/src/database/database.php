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
 * Database connector class.
 *
 * @since       11.1
 * @deprecated  13.3 (Platform) & 4.0 (CMS)
 */
abstract class XTF0FDatabase
{
    public $errorMsg;

    public $errorNum;

    public $sql;

    /**
     * Execute the SQL statement.
     *
     * @return mixed a database cursor resource on success, boolean false on failure
     *
     * @since   11.1
     *
     * @throws RuntimeException
     *
     * @deprecated  13.1 (Platform) & 4.0 (CMS)
     */
    public function query()
    {
        if (class_exists('JLog')) {
            JLog::add('XTF0FDatabase::query() is deprecated, use XTF0FDatabaseDriver::execute() instead.', JLog::WARNING, 'deprecated');
        }

        return $this->execute();
    }

    /**
     * Get a list of available database connectors.  The list will only be populated with connectors that both
     * the class exists and the static test method returns true.  This gives us the ability to have a multitude
     * of connector classes that are self-aware as to whether or not they are able to be used on a given system.
     *
     * @return array an array of available database connectors
     *
     * @since   11.1
     * @deprecated  13.1 (Platform) & 4.0 (CMS)
     */
    public static function getConnectors()
    {
        if (class_exists('JLog')) {
            JLog::add('XTF0FDatabase::getConnectors() is deprecated, use XTF0FDatabaseDriver::getConnectors() instead.', JLog::WARNING, 'deprecated');
        }

        return XTF0FDatabaseDriver::getConnectors();
    }

    /**
     * Gets the error message from the database connection.
     *
     * @param bool $escaped true to escape the message string for use in JavaScript
     *
     * @return string the error message for the most recent query
     *
     * @deprecated  13.3 (Platform) & 4.0 (CMS)
     * @since   11.1
     */
    public function getErrorMsg($escaped = false)
    {
        if (class_exists('JLog')) {
            JLog::add('XTF0FDatabase::getErrorMsg() is deprecated, use exception handling instead.', JLog::WARNING, 'deprecated');
        }

        if ($escaped) {
            return addslashes($this->errorMsg);
        } else {
            return $this->errorMsg;
        }
    }

    /**
     * Gets the error number from the database connection.
     *
     * @return int the error number for the most recent query
     *
     * @since       11.1
     * @deprecated  13.3 (Platform) & 4.0 (CMS)
     */
    public function getErrorNum()
    {
        if (class_exists('JLog')) {
            JLog::add('XTF0FDatabase::getErrorNum() is deprecated, use exception handling instead.', JLog::WARNING, 'deprecated');
        }

        return $this->errorNum;
    }

    /**
     * Method to return a XTF0FDatabaseDriver instance based on the given options.  There are three global options and then
     * the rest are specific to the database driver.  The 'driver' option defines which XTF0FDatabaseDriver class is
     * used for the connection -- the default is 'mysqli'.  The 'database' option determines which database is to
     * be used for the connection.  The 'select' option determines whether the connector should automatically select
     * the chosen database.
     *
     * Instances are unique to the given options and new objects are only created when a unique options array is
     * passed into the method.  This ensures that we don't end up with unnecessary database connection resources.
     *
     * @param array $options parameters to be passed to the database driver
     *
     * @return XTF0FDatabaseDriver a database object
     *
     * @since       11.1
     * @deprecated  13.1 (Platform) & 4.0 (CMS)
     */
    public static function getInstance($options = [])
    {
        if (class_exists('JLog')) {
            JLog::add('XTF0FDatabase::getInstance() is deprecated, use XTF0FDatabaseDriver::getInstance() instead.', JLog::WARNING, 'deprecated');
        }

        return XTF0FDatabaseDriver::getInstance($options);
    }

    /**
     * Splits a string of multiple queries into an array of individual queries.
     *
     * @param string $query input SQL string with which to split into individual queries
     *
     * @return array the queries from the input string separated into an array
     *
     * @since   11.1
     * @deprecated  13.1 (Platform) & 4.0 (CMS)
     */
    public static function splitSql($query)
    {
        if (class_exists('JLog')) {
            JLog::add('XTF0FDatabase::splitSql() is deprecated, use XTF0FDatabaseDriver::splitSql() instead.', JLog::WARNING, 'deprecated');
        }

        return XTF0FDatabaseDriver::splitSql($query);
    }

    /**
     * Return the most recent error message for the database connector.
     *
     * @param bool $showSQL true to display the SQL statement sent to the database as well as the error
     *
     * @return string the error message for the most recent query
     *
     * @since   11.1
     * @deprecated  13.3 (Platform) & 4.0 (CMS)
     */
    public function stderr($showSQL = false)
    {
        if (class_exists('JLog')) {
            JLog::add('XTF0FDatabase::stderr() is deprecated.', JLog::WARNING, 'deprecated');
        }

        if (0 != $this->errorNum) {
            return JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $this->errorNum, $this->errorMsg)
            .($showSQL ? sprintf('<br />SQL = <pre>%s</pre>', $this->sql) : '');
        } else {
            return JText::_('JLIB_DATABASE_FUNCTION_NOERROR');
        }
    }

    /**
     * Test to see if the connector is available.
     *
     * @return bool true on success, false otherwise
     *
     * @since   11.1
     * @deprecated  12.3 (Platform) & 4.0 (CMS) - Use XTF0FDatabaseDriver::isSupported() instead.
     */
    public static function test()
    {
        if (class_exists('JLog')) {
            JLog::add('XTF0FDatabase::test() is deprecated. Use XTF0FDatabaseDriver::isSupported() instead.', JLog::WARNING, 'deprecated');
        }

        return static::isSupported();
    }
}

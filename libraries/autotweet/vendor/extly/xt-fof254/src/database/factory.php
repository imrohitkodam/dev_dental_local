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
 * Joomla Platform Database Factory class
 *
 * @since  12.1
 */
class XTF0FDatabaseFactory
{
    /**
     * Contains the current XTF0FDatabaseFactory instance
     *
     * @var XTF0FDatabaseFactory
     *
     * @since  12.1
     */
    private static $xtf0FDatabaseFactory = null;

    /**
     * Method to return a XTF0FDatabaseDriver instance based on the given options. There are three global options and then
     * the rest are specific to the database driver. The 'database' option determines which database is to
     * be used for the connection. The 'select' option determines whether the connector should automatically select
     * the chosen database.
     *
     * Instances are unique to the given options and new objects are only created when a unique options array is
     * passed into the method.  This ensures that we don't end up with unnecessary database connection resources.
     *
     * @param string $name    Name of the database driver you'd like to instantiate
     * @param array  $options parameters to be passed to the database driver
     *
     * @return XTF0FDatabaseDriver a database driver object
     *
     * @since   12.1
     *
     * @throws RuntimeException
     */
    public function getDriver($name = 'joomla', $options = [])
    {
        // Sanitize the database connector options.
        $options['driver'] = preg_replace('/[^A-Z0-9_\.-]/i', '', $name);
        $options['database'] ??= null;
        $options['select'] ??= true;

        // Derive the class name from the driver.
        $class = 'XTF0FDatabaseDriver'.ucfirst(strtolower($options['driver']));

        // If the class still doesn't exist we have nothing left to do but throw an exception.  We did our best.
        if (!class_exists($class)) {
            throw new RuntimeException(sprintf('Unable to load Database Driver: %s', $options['driver']));
        }

        // Create our new XTF0FDatabaseDriver connector based on the options given.
        try {
            $instance = new $class($options);
        } catch (RuntimeException $runtimeException) {
            throw new RuntimeException(sprintf('Unable to connect to the Database: %s', $runtimeException->getMessage()), $runtimeException->getCode(), $runtimeException);
        }

        return $instance;
    }

    /**
     * Gets an instance of the factory object.
     *
     * @return XTF0FDatabaseFactory
     *
     * @since   12.1
     */
    public static function getInstance()
    {
        return self::$xtf0FDatabaseFactory ?: new self();
    }

    /**
     * Get the current query object or a new XTF0FDatabaseQuery object.
     *
     * @param string              $name name of the driver you want an query object for
     * @param XTF0FDatabaseDriver $xtf0FDatabaseDriver Optional XTF0FDatabaseDriver instance
     *
     * @return XTF0FDatabaseQuery the current query object or a new object extending the XTF0FDatabaseQuery class
     *
     * @since   12.1
     *
     * @throws RuntimeException
     */
    public function getQuery($name, XTF0FDatabaseDriver $xtf0FDatabaseDriver = null)
    {
        // Derive the class name from the driver.
        $class = 'XTF0FDatabaseQuery'.ucfirst(strtolower($name));

        // Make sure we have a query class for this driver.
        if (!class_exists($class)) {
            // If it doesn't exist we are at an impasse so throw an exception.
            throw new RuntimeException('Database Query class not found');
        }

        return new $class($xtf0FDatabaseDriver);
    }

    /**
     * Gets an instance of a factory object to return on subsequent calls of getInstance.
     *
     * @param XTF0FDatabaseFactory $instance a XTF0FDatabaseFactory object
     *
     * @return void
     *
     * @since   12.1
     */
    public static function setInstance(self $instance = null)
    {
        self::$xtf0FDatabaseFactory = $instance;
    }
}

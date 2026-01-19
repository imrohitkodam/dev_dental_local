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
 * MySQL database driver supporting PDO based connections
 *
 * @see         http://php.net/manual/en/ref.pdo-mysql.php
 * @since       3.4
 */
class XTF0FDatabaseDriverPdomysql extends XTF0FDatabaseDriverPdo
{
    /**
     * @var mixed
     */
    public $charset;

    /**
     * The name of the database driver.
     *
     * @var string
     *
     * @since  3.4
     */
    public $name = 'pdomysql';

    /**
     * The type of the database server family supported by this driver.
     *
     * @var string
     *
     * @since  CMS 3.5.0
     */
    public $serverType = 'mysql';

    /**
     * The character(s) used to quote SQL statement names such as table names or field names,
     * etc. The child classes should define this as necessary.  If a single character string the
     * same character is used for both sides of the quoted name, else the first character will be
     * used for the opening quote and the second for the closing quote.
     *
     * @var string
     *
     * @since  3.4
     */
    protected $nameQuote = '`';

    /**
     * The null or zero representation of a timestamp for the database driver.  This should be
     * defined in child classes to hold the appropriate value for the engine.
     *
     * @var string
     *
     * @since  3.4
     */
    protected $nullDate = '0000-00-00 00:00:00';

    /**
     * The minimum supported database version.
     *
     * @var string
     *
     * @since  3.4
     */
    protected static $dbMinimum = '5.0.4';

    /**
     * Constructor.
     *
     * @param array $options array of database options with keys: host, user, password, database, select
     *
     * @since   3.4
     */
    public function __construct($options)
    {
        /**
         * Pre-populate the UTF-8 Multibyte compatibility flag. Unfortuantely PDO won't report the server version
         * unless we're connected to it and we cannot connect to it unless we know if it supports utf8mb4 which requires
         * us knowing the server version. Between this chicken and egg issue we _assume_ it's supported and we'll just
         * catch any problems at connection time.
         */
        $this->utf8mb4 = true;

        // Get some basic values from the options.
        $options['driver'] = 'mysql';
        $options['charset'] ??= 'utf8';

        if ($this->utf8mb4 && ('utf8' == $options['charset'])) {
            $options['charset'] = 'utf8mb4';
        }

        $this->charset = $options['charset'];

        // Finalize initialisation.
        parent::__construct($options);
    }

    /**
     * Connects to the database if needed.
     *
     * @return void returns void if the database connected successfully
     *
     * @since   3.4
     *
     * @throws RuntimeException
     */
    public function connect()
    {
        try {
            // Try to connect to MySQL
            parent::connect();
        } catch (\RuntimeException $runtimeException) {
            // If the connection failed but not because of the wrong character set bubble up the exception
            if (!$this->utf8mb4 || ('utf8mb4' != $this->options['charset'])) {
                throw $runtimeException;
            }

            /**
             * If the connection failed and I was trying to use the utf8mb4 charset then it is likely that the server
             * doesn't support utf8mb4 despite claiming otherwise.
             *
             * This happens on old MySQL server versions (less than 5.5.3) using the mysqlnd PHP driver. Since mysqlnd
             * masks the server version and reports only its own we can not be sure if the server actually does support
             * UTF-8 Multibyte (i.e. it's MySQL 5.5.3 or later). Since the utf8mb4 charset is undefined in this case we
             * catch the error and determine that utf8mb4 is not supported!
             */
            $this->utf8mb4 = false;
            $this->options['charset'] = 'utf8';

            parent::connect();
        }

        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
    }

    /**
     * Test to see if the MySQL connector is available.
     *
     * @return bool true on success, false otherwise
     *
     * @since   3.4
     */
    public static function isSupported()
    {
        return class_exists('PDO') && in_array('mysql', PDO::getAvailableDrivers());
    }

    /**
     * Drops a table from the database.
     *
     * @param string $tableName the name of the database table to drop
     * @param bool   $ifExists  optionally specify that the table must exist before it is dropped
     *
     * @return XTF0FDatabaseDriverPdomysql returns this object to support chaining
     *
     * @since   3.4
     *
     * @throws RuntimeException
     */
    public function dropTable($tableName, $ifExists = true)
    {
        $this->connect();

        $xtf0FDatabaseQuery = $this->getQuery(true);

        $xtf0FDatabaseQuery->setQuery('DROP TABLE '.($ifExists ? 'IF EXISTS ' : '').$this->quoteName($tableName));

        $this->setQuery($xtf0FDatabaseQuery);

        $this->execute();

        return $this;
    }

    /**
     * Select a database for use.
     *
     * @param string $database the name of the database to select for use
     *
     * @return bool true if the database was successfully selected
     *
     * @since   3.4
     *
     * @throws RuntimeException
     */
    public function select($database)
    {
        $this->connect();

        $this->setQuery('USE '.$this->quoteName($database));

        $this->execute();

        return $this;
    }

    /**
     * Method to get the database collation in use by sampling a text field of a table in the database.
     *
     * @return mixed the collation in use by the database (string) or boolean false if not supported
     *
     * @since   3.4
     *
     * @throws RuntimeException
     */
    public function getCollation()
    {
        $this->connect();

        // Attempt to get the database collation by accessing the server system variable.
        $this->setQuery('SHOW VARIABLES LIKE "collation_database"');
        $result = $this->loadObject();

        if (property_exists($result, 'Value')) {
            return $result->Value;
        } else {
            return false;
        }
    }

    /**
     * Method to get the database connection collation, as reported by the driver. If the connector doesn't support
     * reporting this value please return an empty string.
     *
     * @return string
     */
    public function getConnectionCollation()
    {
        $this->connect();

        // Attempt to get the database collation by accessing the server system variable.
        $this->setQuery('SHOW VARIABLES LIKE "collation_connection"');
        $result = $this->loadObject();

        if (property_exists($result, 'Value')) {
            return $result->Value;
        } else {
            return false;
        }
    }

    /**
     * Shows the table CREATE statement that creates the given tables.
     *
     * @param mixed $tables a table name or a list of table names
     *
     * @return array a list of the create SQL for the tables
     *
     * @since   3.4
     *
     * @throws RuntimeException
     */
    public function getTableCreate($tables)
    {
        $this->connect();

        // Initialise variables.
        $result = [];

        // Sanitize input to an array and iterate over the list.
        $tables = (array) $tables;

        foreach ($tables as $table) {
            $this->setQuery('SHOW CREATE TABLE '.$this->quoteName($table));

            $row = $this->loadRow();

            // Populate the result array based on the create statements.
            $result[$table] = $row[1];
        }

        return $result;
    }

    /**
     * Retrieves field information about a given table.
     *
     * @param string $table    the name of the database table
     * @param bool   $typeOnly true to only return field types
     *
     * @return array an array of fields for the database table
     *
     * @since   3.4
     *
     * @throws RuntimeException
     */
    public function getTableColumns($table, $typeOnly = true)
    {
        $this->connect();

        $result = [];

        // Set the query to get the table fields statement.
        $this->setQuery('SHOW FULL COLUMNS FROM '.$this->quoteName($table));

        $fields = $this->loadObjectList();

        // If we only want the type as the value add just that to the list.
        if ($typeOnly) {
            foreach ($fields as $field) {
                $result[$field->Field] = preg_replace('/[(0-9)]/', '', $field->Type);
            }
        }
        // If we want the whole field data object add that to the list.
        else {
            foreach ($fields as $field) {
                $result[$field->Field] = $field;
            }
        }

        return $result;
    }

    /**
     * Get the details list of keys for a table.
     *
     * @param string $table the name of the table
     *
     * @return array an array of the column specification for the table
     *
     * @since   3.4
     *
     * @throws RuntimeException
     */
    public function getTableKeys($table)
    {
        $this->connect();

        // Get the details columns information.
        $this->setQuery('SHOW KEYS FROM '.$this->quoteName($table));

        $keys = $this->loadObjectList();

        return $keys;
    }

    /**
     * Method to get an array of all tables in the database.
     *
     * @return array an array of all the tables in the database
     *
     * @since   3.4
     *
     * @throws RuntimeException
     */
    public function getTableList()
    {
        $this->connect();

        // Set the query to get the tables statement.
        $this->setQuery('SHOW TABLES');
        $tables = $this->loadColumn();

        return $tables;
    }

    /**
     * Get the version of the database connector.
     *
     * @return string the database connector version
     *
     * @since   3.4
     */
    public function getVersion()
    {
        $this->connect();

        return $this->getOption(PDO::ATTR_SERVER_VERSION);
    }

    /**
     * Locks a table in the database.
     *
     * @param string $table the name of the table to unlock
     *
     * @return XTF0FDatabaseDriverPdomysql returns this object to support chaining
     *
     * @since   3.4
     *
     * @throws RuntimeException
     */
    public function lockTable($table)
    {
        $this->setQuery('LOCK TABLES '.$this->quoteName($table).' WRITE')->execute();

        return $this;
    }

    /**
     * Renames a table in the database.
     *
     * @param string $oldTable The name of the table to be renamed
     * @param string $newTable the new name for the table
     * @param string $backup   not used by MySQL
     * @param string $prefix   not used by MySQL
     *
     * @return XTF0FDatabaseDriverPdomysql returns this object to support chaining
     *
     * @since   3.4
     *
     * @throws RuntimeException
     */
    public function renameTable($oldTable, $newTable, $backup = null, $prefix = null)
    {
        $this->setQuery('RENAME TABLE '.$this->quoteName($oldTable).' TO '.$this->quoteName($newTable));

        $this->execute();

        return $this;
    }

    /**
     * Method to escape a string for usage in an SQL statement.
     *
     * Oracle escaping reference:
     * http://www.orafaq.com/wiki/SQL_FAQ#How_does_one_escape_special_characters_when_writing_SQL_queries.3F
     *
     * SQLite escaping notes:
     * http://www.sqlite.org/faq.html#q14
     *
     * Method body is as implemented by the Zend Framework
     *
     * Note: Using query objects with bound variables is
     * preferable to the below.
     *
     * @param string $text  the string to be escaped
     * @param bool   $extra unused optional parameter to provide extra escaping
     *
     * @return string the escaped string
     *
     * @since   3.4
     */
    public function escape($text, $extra = false)
    {
        $this->connect();

        if (is_int($text) || is_float($text)) {
            return $text;
        }

        $result = substr($this->connection->quote($text), 1, -1);

        if ($extra) {
            $result = addcslashes($result, '%_');
        }

        return $result;
    }

    /**
     * Unlocks tables in the database.
     *
     * @return XTF0FDatabaseDriverPdomysql returns this object to support chaining
     *
     * @since   3.4
     *
     * @throws RuntimeException
     */
    public function unlockTables()
    {
        $this->setQuery('UNLOCK TABLES')->execute();

        return $this;
    }

    /**
     * Method to commit a transaction.
     *
     * @param bool $toSavepoint if true, commit to the last savepoint
     *
     * @return void
     *
     * @since   3.4
     *
     * @throws RuntimeException
     */
    public function transactionCommit($toSavepoint = false)
    {
        $this->connect();

        if (!$toSavepoint || $this->transactionDepth <= 1) {
            parent::transactionCommit($toSavepoint);
        } else {
            $this->transactionDepth--;
        }
    }

    /**
     * Method to roll back a transaction.
     *
     * @param bool $toSavepoint if true, rollback to the last savepoint
     *
     * @return void
     *
     * @since   3.4
     *
     * @throws RuntimeException
     */
    public function transactionRollback($toSavepoint = false)
    {
        $this->connect();

        if (!$toSavepoint || $this->transactionDepth <= 1) {
            parent::transactionRollback($toSavepoint);
        } else {
            $savepoint = 'SP_'.($this->transactionDepth - 1);
            $this->setQuery('ROLLBACK TO SAVEPOINT '.$this->quoteName($savepoint));

            if ($this->execute()) {
                $this->transactionDepth--;
            }
        }
    }

    /**
     * Method to initialize a transaction.
     *
     * @param bool $asSavepoint if true and a transaction is already active, a savepoint will be created
     *
     * @return void
     *
     * @since   3.4
     *
     * @throws RuntimeException
     */
    public function transactionStart($asSavepoint = false)
    {
        $this->connect();

        if (!$asSavepoint || !$this->transactionDepth) {
            parent::transactionStart($asSavepoint);
        } else {
            $savepoint = 'SP_'.$this->transactionDepth;
            $this->setQuery('SAVEPOINT '.$this->quoteName($savepoint));

            if ($this->execute()) {
                $this->transactionDepth++;
            }
        }
    }
}

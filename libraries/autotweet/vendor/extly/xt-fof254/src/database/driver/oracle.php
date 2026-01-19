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
 * Oracle database driver
 *
 * @see    http://php.net/pdo
 * @since  12.1
 */
class XTF0FDatabaseDriverOracle extends XTF0FDatabaseDriverPdo
{
    /**
     * The name of the database driver.
     *
     * @var string
     *
     * @since  12.1
     */
    public $name = 'oracle';

    /**
     * The type of the database server family supported by this driver.
     *
     * @var string
     *
     * @since  CMS 3.5.0
     */
    public $serverType = 'oracle';

    /**
     * The character(s) used to quote SQL statement names such as table names or field names,
     * etc.  The child classes should define this as necessary.  If a single character string the
     * same character is used for both sides of the quoted name, else the first character will be
     * used for the opening quote and the second for the closing quote.
     *
     * @var string
     *
     * @since  12.1
     */
    protected $nameQuote = '"';

    /**
     * Returns the current dateformat
     *
     * @var string
     *
     * @since 12.1
     */
    protected $dateformat;

    /**
     * Returns the current character set
     *
     * @var string
     *
     * @since 12.1
     */
    protected $charset;

    /**
     * Constructor.
     *
     * @param array $options List of options used to configure the connection
     *
     * @since   12.1
     */
    public function __construct($options)
    {
        $options['driver'] = 'oci';
        $options['charset'] ??= 'AL32UTF8';
        $options['dateformat'] ??= 'RRRR-MM-DD HH24:MI:SS';

        $this->charset = $options['charset'];
        $this->dateformat = $options['dateformat'];

        // Finalize initialisation
        parent::__construct($options);
    }

    /**
     * Destructor.
     *
     * @since   12.1
     */
    public function __destruct()
    {
        $this->freeResult();
        unset($this->connection);
    }

    /**
     * Connects to the database if needed.
     *
     * @return void returns void if the database connected successfully
     *
     * @since   12.1
     *
     * @throws RuntimeException
     */
    public function connect()
    {
        if ($this->connection) {
            return;
        }

        parent::connect();

        if (isset($this->options['schema'])) {
            $this->setQuery('ALTER SESSION SET CURRENT_SCHEMA = '.$this->quoteName($this->options['schema']))->execute();
        }

        $this->setDateFormat($this->dateformat);
    }

    /**
     * Disconnects the database.
     *
     * @return void
     *
     * @since   12.1
     */
    public function disconnect()
    {
        // Close the connection.
        $this->freeResult();
        unset($this->connection);
    }

    /**
     * Drops a table from the database.
     *
     * Note: The IF EXISTS flag is unused in the Oracle driver.
     *
     * @param string $tableName the name of the database table to drop
     * @param bool   $ifExists  optionally specify that the table must exist before it is dropped
     *
     * @return XTF0FDatabaseDriverOracle returns this object to support chaining
     *
     * @since   12.1
     */
    public function dropTable($tableName, $ifExists = true)
    {
        $this->connect();

        $xtf0FDatabaseQuery = $this->getQuery(true)
            ->setQuery('DROP TABLE :tableName');
        $xtf0FDatabaseQuery->bind(':tableName', $tableName);

        $this->setQuery($xtf0FDatabaseQuery);

        $this->execute();

        return $this;
    }

    /**
     * Method to get the database collation in use by sampling a text field of a table in the database.
     *
     * @return mixed the collation in use by the database or boolean false if not supported
     *
     * @since   12.1
     */
    public function getCollation()
    {
        return $this->charset;
    }

    /**
     * Method to get the database connection collation, as reported by the driver. If the connector doesn't support
     * reporting this value please return an empty string.
     *
     * @return string
     */
    public function getConnectionCollation()
    {
        return $this->charset;
    }

    /**
     * Get a query to run and verify the database is operational.
     *
     * @return string the query to check the health of the DB
     *
     * @since   12.2
     */
    public function getConnectedQuery()
    {
        return 'SELECT 1 FROM dual';
    }

    /**
     * Returns the current date format
     * This method should be useful in the case that
     * somebody actually wants to use a different
     * date format and needs to check what the current
     * one is to see if it needs to be changed.
     *
     * @return string The current date format
     *
     * @since 12.1
     */
    public function getDateFormat()
    {
        return $this->dateformat;
    }

    /**
     * Shows the table CREATE statement that creates the given tables.
     *
     * Note: You must have the correct privileges before this method
     * will return usable results!
     *
     * @param mixed $tables a table name or a list of table names
     *
     * @return array a list of the create SQL for the tables
     *
     * @since   12.1
     *
     * @throws RuntimeException
     */
    public function getTableCreate($tables)
    {
        $this->connect();

        $result = [];
        $query = $this->getQuery(true)
            ->select('dbms_metadata.get_ddl(:type, :tableName)')
            ->from('dual')
            ->bind(':type', 'TABLE');

        // Sanitize input to an array and iterate over the list.
        $tables = (array) $tables;

        foreach ($tables as $table) {
            $query->bind(':tableName', $table);
            $this->setQuery($query);
            $statement = (string) $this->loadResult();
            $result[$table] = $statement;
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
     * @since   12.1
     *
     * @throws RuntimeException
     */
    public function getTableColumns($table, $typeOnly = true)
    {
        $this->connect();

        $columns = [];
        $xtf0FDatabaseQuery = $this->getQuery(true);

        $fieldCasing = $this->getOption(PDO::ATTR_CASE);

        $this->setOption(PDO::ATTR_CASE, PDO::CASE_UPPER);

        $table = strtoupper($table);

        $xtf0FDatabaseQuery->select('*');
        $xtf0FDatabaseQuery->from('ALL_TAB_COLUMNS');
        $xtf0FDatabaseQuery->where('table_name = :tableName');

        $prefixedTable = str_replace('#__', strtoupper($this->tablePrefix), $table);
        $xtf0FDatabaseQuery->bind(':tableName', $prefixedTable);
        $this->setQuery($xtf0FDatabaseQuery);
        $fields = $this->loadObjectList();

        if ($typeOnly) {
            foreach ($fields as $field) {
                $columns[$field->COLUMN_NAME] = $field->DATA_TYPE;
            }
        } else {
            foreach ($fields as $field) {
                $columns[$field->COLUMN_NAME] = $field;
                $columns[$field->COLUMN_NAME]->Default = null;
            }
        }

        $this->setOption(PDO::ATTR_CASE, $fieldCasing);

        return $columns;
    }

    /**
     * Get the details list of keys for a table.
     *
     * @param string $table the name of the table
     *
     * @return array an array of the column specification for the table
     *
     * @since   12.1
     *
     * @throws RuntimeException
     */
    public function getTableKeys($table)
    {
        $this->connect();

        $xtf0FDatabaseQuery = $this->getQuery(true);

        $fieldCasing = $this->getOption(PDO::ATTR_CASE);

        $this->setOption(PDO::ATTR_CASE, PDO::CASE_UPPER);

        $table = strtoupper($table);
        $xtf0FDatabaseQuery->select('*')
            ->from('ALL_CONSTRAINTS')
            ->where('table_name = :tableName')
            ->bind(':tableName', $table);

        $this->setQuery($xtf0FDatabaseQuery);
        $keys = $this->loadObjectList();

        $this->setOption(PDO::ATTR_CASE, $fieldCasing);

        return $keys;
    }

    /**
     * Method to get an array of all tables in the database (schema).
     *
     * @param string $databaseName        The database (schema) name
     * @param bool   $includeDatabaseName Whether to include the schema name in the results
     *
     * @return array an array of all the tables in the database
     *
     * @since   12.1
     *
     * @throws RuntimeException
     */
    public function getTableList($databaseName = null, $includeDatabaseName = false)
    {
        $this->connect();

        $xtf0FDatabaseQuery = $this->getQuery(true);

        if ($includeDatabaseName) {
            $xtf0FDatabaseQuery->select('owner, table_name');
        } else {
            $xtf0FDatabaseQuery->select('table_name');
        }

        $xtf0FDatabaseQuery->from('all_tables');

        if ($databaseName) {
            $xtf0FDatabaseQuery->where('owner = :database')
                ->bind(':database', $databaseName);
        }

        $xtf0FDatabaseQuery->order('table_name');

        $this->setQuery($xtf0FDatabaseQuery);

        $tables = $includeDatabaseName ? $this->loadAssocList() : $this->loadColumn();

        return $tables;
    }

    /**
     * Get the version of the database connector.
     *
     * @return string the database connector version
     *
     * @since   12.1
     */
    public function getVersion()
    {
        $this->connect();

        $this->setQuery("select value from nls_database_parameters where parameter = 'NLS_RDBMS_VERSION'");

        return $this->loadResult();
    }

    /**
     * Select a database for use.
     *
     * @param string $database the name of the database to select for use
     *
     * @return bool true if the database was successfully selected
     *
     * @since   12.1
     *
     * @throws RuntimeException
     */
    public function select($database)
    {
        $this->connect();

        return true;
    }

    /**
     * Sets the Oracle Date Format for the session
     * Default date format for Oracle is = DD-MON-RR
     * The default date format for this driver is:
     * 'RRRR-MM-DD HH24:MI:SS' since it is the format
     * that matches the MySQL one used within most Joomla
     * tables.
     *
     * @param string $dateFormat Oracle Date Format String
     *
     * @return bool
     *
     * @since  12.1
     */
    public function setDateFormat($dateFormat = 'DD-MON-RR')
    {
        $this->connect();

        $this->setQuery(sprintf("ALTER SESSION SET NLS_DATE_FORMAT = '%s'", $dateFormat));

        if (!$this->execute()) {
            return false;
        }

        $this->setQuery(sprintf("ALTER SESSION SET NLS_TIMESTAMP_FORMAT = '%s'", $dateFormat));

        if (!$this->execute()) {
            return false;
        }

        $this->dateformat = $dateFormat;

        return true;
    }

    /**
     * Set the connection to use UTF-8 character encoding.
     *
     * Returns false automatically for the Oracle driver since
     * you can only set the character set when the connection
     * is created.
     *
     * @return bool true on success
     *
     * @since   12.1
     */
    public function setUtf()
    {
        return false;
    }

    /**
     * Locks a table in the database.
     *
     * @param string $table the name of the table to unlock
     *
     * @return XTF0FDatabaseDriverOracle returns this object to support chaining
     *
     * @since   12.1
     *
     * @throws RuntimeException
     */
    public function lockTable($table)
    {
        $this->setQuery('LOCK TABLE '.$this->quoteName($table).' IN EXCLUSIVE MODE')->execute();

        return $this;
    }

    /**
     * Renames a table in the database.
     *
     * @param string $oldTable The name of the table to be renamed
     * @param string $newTable the new name for the table
     * @param string $backup   not used by Oracle
     * @param string $prefix   not used by Oracle
     *
     * @return XTF0FDatabaseDriverOracle returns this object to support chaining
     *
     * @since   12.1
     *
     * @throws RuntimeException
     */
    public function renameTable($oldTable, $newTable, $backup = null, $prefix = null)
    {
        $this->setQuery('RENAME '.$oldTable.' TO '.$newTable)->execute();

        return $this;
    }

    /**
     * Unlocks tables in the database.
     *
     * @return XTF0FDatabaseDriverOracle returns this object to support chaining
     *
     * @since   12.1
     *
     * @throws RuntimeException
     */
    public function unlockTables()
    {
        $this->setQuery('COMMIT')->execute();

        return $this;
    }

    /**
     * Test to see if the PDO ODBC connector is available.
     *
     * @return bool true on success, false otherwise
     *
     * @since   12.1
     */
    public static function isSupported()
    {
        return class_exists('PDO') && in_array('oci', PDO::getAvailableDrivers());
    }

    /**
     * This function replaces a string identifier <var>$prefix</var> with the string held is the
     * <var>tablePrefix</var> class variable.
     *
     * @param string $query  the SQL statement to prepare
     * @param string $prefix the common table prefix
     *
     * @return string the processed SQL statement
     *
     * @since   11.1
     */
    public function replacePrefix($query, $prefix = '#__')
    {
        $startPos = 0;
        $quoteChar = "'";
        $literal = '';

        $query = trim($query);
        $n = strlen($query);

        while ($startPos < $n) {
            $ip = strpos($query, $prefix, $startPos);

            if (false === $ip) {
                break;
            }

            $j = strpos($query, "'", $startPos);

            if (false === $j) {
                $j = $n;
            }

            $literal .= str_replace($prefix, $this->tablePrefix, substr($query, $startPos, $j - $startPos));
            $startPos = $j;

            $j = $startPos + 1;

            if ($j >= $n) {
                break;
            }

            // Quote comes first, find end of quote
            while (true) {
                $k = strpos($query, $quoteChar, $j);
                $escaped = false;

                if (false === $k) {
                    break;
                }

                $l = $k - 1;

                while ($l >= 0 && '\\' === $query[$l]) {
                    $l--;
                    $escaped = !$escaped;
                }

                if ($escaped) {
                    $j = $k + 1;
                    continue;
                }

                break;
            }

            if (false === $k) {
                // Error in the query - no end quote; ignore it
                break;
            }

            $literal .= substr($query, $startPos, $k - $startPos + 1);
            $startPos = $k + 1;
        }

        if ($startPos < $n) {
            $literal .= substr($query, $startPos, $n - $startPos);
        }

        return $literal;
    }

    /**
     * Method to commit a transaction.
     *
     * @param bool $toSavepoint if true, commit to the last savepoint
     *
     * @return void
     *
     * @since   12.3
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
     * @since   12.3
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
     * @since   12.3
     *
     * @throws RuntimeException
     */
    public function transactionStart($asSavepoint = false)
    {
        $this->connect();

        if (!$asSavepoint || !$this->transactionDepth) {
            return parent::transactionStart($asSavepoint);
        }

        $savepoint = 'SP_'.$this->transactionDepth;
        $this->setQuery('SAVEPOINT '.$this->quoteName($savepoint));

        if ($this->execute()) {
            $this->transactionDepth++;
        }

        return null;
    }
}

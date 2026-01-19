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

/**
 *  @copyright   Copyright (c)2012-2020 Extly, CB. All rights reserved. / Based on FrameworkOnFramework of Akeeba
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * This file is adapted from the Joomla! Platform. It is used to iterate a database cursor returning XTF0FTable objects
 * instead of plain stdClass objects
 */
class XTF0FDatabaseInstaller
{
    /** @var array A list of the base names of the XML schema files */
    public $xmlFiles = ['mysql', 'mysqli', 'pdomysql', 'postgresql', 'sqlsrv', 'mssql'];

    /**
     * @var \Joomla\CMS\Input\Input Input variables
     */
    protected $input = [];

    /** @var array Internal cache for table list */
    protected static $allTables = [];

    /** @var XTF0FDatabase The database connector object */
    private $xtf0FDatabase = null;

    /** @var string The directory where the XML schema files are stored */
    private $xmlDirectory = null;

    /**
     * Public constructor
     *
     * @param array $config The configuration array
     */
    public function __construct($config = [])
    {
        // Make sure $config is an array
        if (is_object($config)) {
            $config = (array) $config;
        } elseif (!is_array($config)) {
            $config = [];
        }

        // Get the input
        if (array_key_exists('input', $config)) {
            if ($config['input'] instanceof \Joomla\CMS\Input\Input) {
                $this->input = $config['input'];
            } else {
                $this->input = new \Joomla\CMS\Input\Input($config['input']);
            }
        } else {
            $this->input = $this->getJoomlaInput();
        }

        // Set the database object
        $this->xtf0FDatabase = array_key_exists('dbo', $config) ? $config['dbo'] : XTF0FPlatform::getInstance()->getDbo();

        // Set the $name/$_name variable
        $component = $this->input->getCmd('option', 'com_foobar');

        if (array_key_exists('option', $config)) {
            $component = $config['option'];
        }

        // Figure out where the XML schema files are stored
        if (array_key_exists('dbinstaller_directory', $config)) {
            $this->xmlDirectory = $config['dbinstaller_directory'];
        } else {
            // Nothing is defined, assume the files are stored in the sql/xml directory inside the component's administrator section
            $directories = XTF0FPlatform::getInstance()->getComponentBaseDirs($component);
            $this->setXmlDirectory($directories['admin'].'/sql/xml');
        }

        // Do we have a set of XML files to look for?
        if (array_key_exists('dbinstaller_files', $config)) {
            $files = $config['dbinstaller_files'];

            if (!is_array($files)) {
                $files = explode(',', $files);
            }

            $this->xmlFiles = $files;
        }
    }

    /**
     * Sets the directory where XML schema files are stored
     *
     * @param string $xmlDirectory
     */
    public function setXmlDirectory($xmlDirectory)
    {
        $this->xmlDirectory = $xmlDirectory;
    }

    /**
     * Returns the directory where XML schema files are stored
     *
     * @return string
     */
    public function getXmlDirectory()
    {
        return $this->xmlDirectory;
    }

    /**
     * Creates or updates the database schema
     *
     * @return void
     *
     * @throws Exception When a database query fails and it doesn't have the canfail flag
     */
    public function updateSchema()
    {
        // Get the schema XML file
        $xml = $this->findSchemaXml();

        if (empty($xml)) {
            return;
        }

        // Make sure there are SQL commands in this file
        if (!$xml->sql) {
            return;
        }

        // Walk the sql > action tags to find all tables
        /** @var SimpleXMLElement $actions */
        $actions = $xml->sql->children();

        /**
         * The meta/autocollation node defines if I should automatically apply the correct collation (utf8 or utf8mb4)
         * to the database tables managed by the schema updater. When enabled (default) the queries are automatically
         * converted to the correct collation (utf8mb4_unicode_ci or utf8_general_ci) depending on whether your Joomla!
         * and MySQL server support Multibyte UTF-8 (UTF8MB4). Moreover, if UTF8MB4 is supported, all CREATE TABLE
         * queries are analyzed and the tables referenced in them are auto-converted to the proper utf8mb4 collation.
         */
        $autoCollationConversion = true;

        if ($xml->meta->autocollation) {
            $value = (string) $xml->meta->autocollation;
            $value = trim($value);
            $value = strtolower($value);

            $autoCollationConversion = in_array($value, ['true', '1', 'on', 'yes']);
        }

        try {
            $hasUtf8mb4Support = $this->xtf0FDatabase->hasUTF8mb4Support();
        } catch (\Exception $exception) {
            $hasUtf8mb4Support = false;
        }

        $tablesToConvert = [];

        /** @var SimpleXMLElement $action */
        foreach ($actions as $action) {
            // Get the attributes
            $attributes = $action->attributes();

            // Get the table / view name
            $table = $attributes->table ? (string) $attributes->table : '';

            if ($table === '' || $table === '0') {
                continue;
            }

            // Am I allowed to let this action fail?
            $canFailAction = $attributes->canfail ?: 0;

            // Evaluate conditions
            $shouldExecute = true;

            /** @var SimpleXMLElement $node */
            foreach ($action->children() as $node) {
                if ('condition' === $node->getName()) {
                    // Get the operator
                    $operator = $node->attributes()->operator ? (string) $node->attributes()->operator : 'and';
                    $operator = $operator === '' || $operator === '0' ? 'and' : $operator;

                    $condition = $this->conditionMet($table, $node);

                    switch ($operator) {
                        case 'not':
                            $shouldExecute = $shouldExecute && !$condition;
                            break;

                        case 'or':
                            $shouldExecute = $shouldExecute || $condition;
                            break;

                        case 'nor':
                            $shouldExecute = !$shouldExecute && !$condition;
                            break;

                        case 'xor':
                            $shouldExecute = ($shouldExecute xor $condition);
                            break;

                        case 'maybe':
                            $shouldExecute = $condition ? true : $shouldExecute;
                            break;

                        default:
                            $shouldExecute = $shouldExecute && $condition;
                            break;
                    }
                }

                // DO NOT USE BOOLEAN SHORT CIRCUIT EVALUATION!
                // if (!$shouldExecute) break;
            }

            // Do I have to only collect the tables from CREATE TABLE queries?
            $onlyCollectTables = !$shouldExecute && $autoCollationConversion && $hasUtf8mb4Support;

            // Make sure all conditions are met OR I have to collect tables from CREATE TABLE queries.
            if (!$shouldExecute && !$onlyCollectTables) {
                continue;
            }

            // Execute queries
            foreach ($action->children() as $node) {
                if ('query' === $node->getName()) {
                    $query = (string) $node;

                    if ($autoCollationConversion && $hasUtf8mb4Support) {
                        $this->extractTablesToConvert($query, $tablesToConvert);
                    }

                    // If we're only collecting tables do not run the queries
                    if ($onlyCollectTables) {
                        continue;
                    }

                    $canFail = $node->attributes->canfail ? (string) $node->attributes->canfail : $canFailAction;

                    if (is_string($canFail)) {
                        $canFail = strtoupper($canFail);
                    }

                    $canFail = (in_array($canFail, [true, 1, 'YES', 'TRUE']));

                    // Do I need to automatically convert the collation of all CREATE / ALTER queries?
                    if ($autoCollationConversion) {
                        $query = $hasUtf8mb4Support ? $this->convertUtf8QueryToUtf8mb4($query) : $this->convertUtf8mb4QueryToUtf8($query);
                    }

                    $this->xtf0FDatabase->setQuery($query);

                    try {
                        $this->xtf0FDatabase->execute();
                    } catch (Exception $e) {
                        // If we are not allowed to fail, throw back the exception we caught
                        if (!$canFail) {
                            throw $e;
                        }
                    }
                }
            }
        }

        // Auto-convert the collation of tables if we are told to do so, have utf8mb4 support and a list of tables.
        if ($autoCollationConversion && $hasUtf8mb4Support && !empty($tablesToConvert)) {
            $this->convertTablesToUtf8mb4($tablesToConvert);
        }
    }

    /**
     * Uninstalls the database schema
     *
     * @return void
     */
    public function removeSchema()
    {
        // Get the schema XML file
        $xml = $this->findSchemaXml();

        if (empty($xml)) {
            return;
        }

        // Make sure there are SQL commands in this file
        if (!$xml->sql) {
            return;
        }

        // Walk the sql > action tags to find all tables
        $tables = [];
        /** @var SimpleXMLElement $actions */
        $actions = $xml->sql->children();

        /** @var SimpleXMLElement $action */
        foreach ($actions as $action) {
            $attributes = $action->attributes();
            $tables[] = (string) $attributes->table;
        }

        // Simplify the tables list
        $tables = array_unique($tables);

        // Start dropping tables
        foreach ($tables as $table) {
            try {
                $this->xtf0FDatabase->dropTable($table);
            } catch (Exception $e) {
                // Do not fail if I can't drop the table
            }
        }
    }

    /**
     * Find an suitable schema XML file for this database type and return the SimpleXMLElement holding its information
     *
     * @return SimpleXMLElement|null Null if no suitable schema XML file is found
     */
    protected function findSchemaXml()
    {
        $driverType = $this->xtf0FDatabase->name;
        $xml = null;

        // And now look for the file
        foreach ($this->xmlFiles as $xmlFile) {
            // Remove any accidental whitespace
            $xmlFile = trim($xmlFile);

            // Get the full path to the file
            $fileName = $this->xmlDirectory.'/'.$xmlFile.'.xml';

            // Make sure the file exists
            if (!@file_exists($fileName)) {
                continue;
            }

            // Make sure the file is a valid XML document
            try {
                $xml = new SimpleXMLElement($fileName, \LIBXML_NONET, true);
            } catch (Exception $e) {
                $xml = null;
                continue;
            }

            // Make sure the file is an XML schema file
            if ('schema' !== $xml->getName()) {
                $xml = null;
                continue;
            }

            if (!$xml->meta) {
                $xml = null;
                continue;
            }

            if (!$xml->meta->drivers) {
                $xml = null;
                continue;
            }

            /** @var SimpleXMLElement $drivers */
            $drivers = $xml->meta->drivers;

            // Strict driver name match
            foreach ($drivers->children() as $driverTypeTag) {
                $thisDriverType = (string) $driverTypeTag;

                if ($thisDriverType == $driverType) {
                    return $xml;
                }
            }

            // Some custom database drivers use a non-standard $name variable. Let try a relaxed match.
            foreach ($drivers->children() as $driverTypeTag) {
                $thisDriverType = (string) $driverTypeTag;

                if (
                    // e.g. $driverType = 'mysqlistupid', $thisDriverType = 'mysqli' => driver matched
                    0 === strpos($driverType, $thisDriverType)
                    // e.g. $driverType = 'stupidmysqli', $thisDriverType = 'mysqli' => driver matched
                    || (substr($driverType, -strlen($thisDriverType)) === $thisDriverType)
                ) {
                    return $xml;
                }
            }

            $xml = null;
        }

        return $xml;
    }

    /**
     * Checks if a condition is met
     *
     * @param string           $table The table we're operating on
     * @param SimpleXMLElement $node  The condition definition node
     *
     * @return bool
     */
    protected function conditionMet($table, SimpleXMLElement $node)
    {
        if (empty(static::$allTables)) {
            static::$allTables = $this->xtf0FDatabase->getTableList();
        }

        // Does the table exist?
        $tableNormal = $this->xtf0FDatabase->replacePrefix($table);
        $tableExists = in_array($tableNormal, static::$allTables);

        // Initialise
        $condition = false;

        // Get the condition's attributes
        $attributes = $node->attributes();
        $type = $attributes->type ?: null;
        $value = $attributes->value ? (string) $attributes->value : null;

        switch ($type) {
            // Check if a table or column is missing
            case 'missing':
                $fieldName = (string) $value;

                if ($fieldName === '' || $fieldName === '0') {
                    $condition = !$tableExists;
                } else {
                    try {
                        $tableColumns = $this->xtf0FDatabase->getTableColumns($tableNormal, true);
                    } catch (\Exception $e) {
                        $tableColumns = [];
                    }

                    $condition = !array_key_exists($fieldName, $tableColumns);
                }

                break;

                // Check if a column type matches the "coltype" attribute
            case 'type':
                try {
                    $tableColumns = $this->xtf0FDatabase->getTableColumns($tableNormal, true);
                } catch (\Exception $e) {
                    $tableColumns = [];
                }

                $condition = false;

                if (array_key_exists($value, $tableColumns)) {
                    $coltype = $attributes->coltype ?: null;

                    if (!empty($coltype)) {
                        $coltype = strtolower($coltype);
                        $currentType = strtolower($tableColumns[$value]->Type);

                        $condition = ($coltype === $currentType);
                    }
                }

                break;

                // Check if a (named) index exists on the table. Currently only supported on MySQL.
            case 'index':
                $indexName = (string) $value;
                $condition = true;

                if ($indexName !== '' && $indexName !== '0') {
                    $indexName = str_replace('#__', $this->xtf0FDatabase->getPrefix(), $indexName);
                    $condition = $this->hasIndex($tableNormal, $indexName);
                }

                break;

                // Check if a table or column needs to be upgraded to utf8mb4
            case 'utf8mb4upgrade':
                $condition = false;

                // Check if the driver and the database connection have UTF8MB4 support
                try {
                    $hasUtf8mb4Support = $this->xtf0FDatabase->hasUTF8mb4Support();
                } catch (\Exception $e) {
                    $hasUtf8mb4Support = false;
                }

                if ($hasUtf8mb4Support) {
                    $fieldName = (string) $value;

                    if ($fieldName === '' || $fieldName === '0') {
                        $collation = $this->getTableCollation($tableNormal);
                    } else {
                        $collation = $this->getColumnCollation($tableNormal, $fieldName);
                    }

                    $parts = explode('_', $collation, 3);
                    $encoding = empty($parts[0]) ? '' : strtolower($parts[0]);

                    $condition = 'utf8mb4' !== $encoding;
                }

                break;

                // Check if the result of a query matches our expectation
            case 'equals':
                $query = (string) $node;
                $this->xtf0FDatabase->setQuery($query);

                try {
                    $result = $this->xtf0FDatabase->loadResult();
                    $condition = ($result == $value);
                } catch (Exception $e) {
                    return false;
                }

                break;

                // Always returns true
            case 'true':
                return true;
                break;

            default:
                return false;
                break;
        }

        return $condition;
    }

    /**
     * Get the collation of a table. Uses an internal cache for efficiency.
     *
     * @param string $tableName The name of the table
     *
     * @return string The collation, e.g. "utf8_general_ci"
     */
    private function getTableCollation($tableName)
    {
        static $cache = [];

        $tableName = $this->xtf0FDatabase->replacePrefix($tableName);

        if (!isset($cache[$tableName])) {
            $cache[$tableName] = $this->realGetTableCollation($tableName);
        }

        return $cache[$tableName];
    }

    /**
     * Get the collation of a table. This is the internal method used by getTableCollation.
     *
     * @param string $tableName The name of the table
     *
     * @return string The collation, e.g. "utf8_general_ci"
     */
    private function realGetTableCollation($tableName)
    {
        try {
            $utf8Support = $this->xtf0FDatabase->hasUTFSupport();
        } catch (\Exception $exception) {
            $utf8Support = false;
        }

        try {
            $utf8mb4Support = $utf8Support && $this->xtf0FDatabase->hasUTF8mb4Support();
        } catch (\Exception $exception) {
            $utf8mb4Support = false;
        }

        $collation = $utf8mb4Support ? 'utf8mb4_unicode_ci' : ($utf8Support ? 'utf_general_ci' : 'latin1_swedish_ci');

        $query = 'SHOW TABLE STATUS LIKE '.$this->xtf0FDatabase->q($tableName);

        try {
            $row = $this->xtf0FDatabase->setQuery($query)->loadAssoc();
        } catch (\Exception $exception) {
            return $collation;
        }

        if (empty($row)) {
            return $collation;
        }

        if (!isset($row['Collation'])) {
            return $collation;
        }

        if (empty($row['Collation'])) {
            return $collation;
        }

        return $row['Collation'];
    }

    /**
     * Get the collation of a column. Uses an internal cache for efficiency.
     *
     * @param string $tableName  The name of the table
     * @param string $columnName The name of the column
     *
     * @return string The collation, e.g. "utf8_general_ci"
     */
    private function getColumnCollation($tableName, $columnName)
    {
        static $cache = [];

        $tableName = $this->xtf0FDatabase->replacePrefix($tableName);
        $columnName = $this->xtf0FDatabase->replacePrefix($columnName);

        if (!isset($cache[$tableName])) {
            $cache[$tableName] = [];
        }

        if (!isset($cache[$tableName][$columnName])) {
            $cache[$tableName][$columnName] = $this->realGetColumnCollation($tableName, $columnName);
        }

        return $cache[$tableName][$columnName];
    }

    /**
     * Get the collation of a column. This is the internal method used by getColumnCollation.
     *
     * @param string $tableName  The name of the table
     * @param string $columnName The name of the column
     *
     * @return string The collation, e.g. "utf8_general_ci"
     */
    private function realGetColumnCollation($tableName, $columnName)
    {
        $collation = $this->getTableCollation($tableName);

        $query = 'SHOW FULL COLUMNS FROM '.$this->xtf0FDatabase->qn($tableName).' LIKE '.$this->xtf0FDatabase->q($columnName);

        try {
            $row = $this->xtf0FDatabase->setQuery($query)->loadAssoc();
        } catch (\Exception $exception) {
            return $collation;
        }

        if (empty($row)) {
            return $collation;
        }

        if (!isset($row['Collation'])) {
            return $collation;
        }

        if (empty($row['Collation'])) {
            return $collation;
        }

        return $row['Collation'];
    }

    /**
     * Automatically downgrade a CREATE TABLE or ALTER TABLE query from utf8mb4 (UTF-8 Multibyte) to plain utf8.
     *
     * We use our own method so we can be site it works even on Joomla! 3.4 or earlier, where UTF8MB4 support is not
     * implemented.
     *
     * @param string $query The query to convert
     *
     * @return string The converted query
     */
    private function convertUtf8mb4QueryToUtf8($query)
    {
        // If it's not an ALTER TABLE or CREATE TABLE command there's nothing to convert
        $beginningOfQuery = substr($query, 0, 12);
        $beginningOfQuery = strtoupper($beginningOfQuery);

        if (!in_array($beginningOfQuery, ['ALTER TABLE ', 'CREATE TABLE'])) {
            return $query;
        }

        // Replace utf8mb4 with utf8
        $from = [
            'utf8mb4_unicode_ci',
            'utf8mb4_',
            'utf8mb4',
        ];

        $to = [
            'utf8_general_ci', // Yeah, we convert utf8mb4_unicode_ci to utf8_general_ci per Joomla!'s conventions
            'utf8_',
            'utf8',
        ];

        return str_replace($from, $to, $query);
    }

    /**
     * Automatically upgrade a CREATE TABLE or ALTER TABLE query from plain utf8 to utf8mb4 (UTF-8 Multibyte).
     *
     * @param string $query The query to convert
     *
     * @return string The converted query
     */
    private function convertUtf8QueryToUtf8mb4($query)
    {
        // If it's not an ALTER TABLE or CREATE TABLE command there's nothing to convert
        $beginningOfQuery = substr($query, 0, 12);
        $beginningOfQuery = strtoupper($beginningOfQuery);

        if (!in_array($beginningOfQuery, ['ALTER TABLE ', 'CREATE TABLE'])) {
            return $query;
        }

        // Replace utf8 with utf8mb4
        $from = [
            'utf8_general_ci',
            'utf8_',
            'utf8',
        ];

        $to = [
            'utf8mb4_unicode_ci', // Yeah, we convert utf8_general_ci to utf8mb4_unicode_ci per Joomla!'s conventions
            'utf8mb4_',
            'utf8mb4',
        ];

        return str_replace($from, $to, $query);
    }

    /**
     * Analyzes a query. If it's a CREATE TABLE query the table is added to the $tables array.
     *
     * @param string $query  The query to analyze
     * @param string $tables The array where the name of the detected table is added
     *
     * @return void
     */
    private function extractTablesToConvert($query, &$tables)
    {
        // Normalize the whitespace of the query
        $query = trim($query);
        $query = str_replace(["\r\n", "\r", "\n"], ' ', $query);

        while (false !== strstr($query, '  ')) {
            $query = str_replace('  ', ' ', $query);
        }

        // Is it a create table query?
        $queryStart = substr($query, 0, 12);
        $queryStart = strtoupper($queryStart);

        if ('CREATE TABLE' !== $queryStart) {
            return;
        }

        // Remove the CREATE TABLE keyword. Also, If there's an IF NOT EXISTS clause remove it.
        $query = substr($query, 12);
        $query = str_ireplace('IF NOT EXISTS', '', $query);
        $query = trim($query);

        // Make sure there is a space between the table name and its definition, denoted by an open parenthesis
        $query = str_replace('(', ' (', $query);

        // Now we should have the name of the table, a space and the rest of the query. Extract the table name.
        $parts = explode(' ', $query, 2);
        $tableName = $parts[0];

        /**
         * The table name may be quoted. Since UTF8MB4 is only supported in MySQL, the table name can only be
         * quoted with surrounding backticks. Therefore we can trim backquotes from the table name to unquote it!
         **/
        $tableName = trim($tableName, '`');

        // Finally, add the table name to $tables if it doesn't already exist.
        if (!in_array($tableName, $tables)) {
            $tables[] = $tableName;
        }
    }

    /**
     * Converts the collation of tables listed in $tablesToConvert to utf8mb4_unicode_ci
     *
     * @param array $tablesToConvert The list of tables to convert
     *
     * @return void
     */
    private function convertTablesToUtf8mb4($tablesToConvert)
    {
        try {
            $utf8mb4Support = $this->xtf0FDatabase->hasUTF8mb4Support();
        } catch (\Exception $exception) {
            $utf8mb4Support = false;
        }

        // Make sure the database driver REALLY has support for converting character sets
        if (!$utf8mb4Support) {
            return;
        }

        asort($tablesToConvert);

        foreach ($tablesToConvert as $tableToConvert) {
            $collation = $this->getTableCollation($tableToConvert);

            $parts = explode('_', $collation, 3);
            $encoding = empty($parts[0]) ? '' : strtolower($parts[0]);

            if ('utf8mb4' !== $encoding) {
                $queries = $this->xtf0FDatabase->getAlterTableCharacterSet($tableToConvert);

                try {
                    foreach ($queries as $query) {
                        $this->xtf0FDatabase->setQuery($query)->execute();
                    }
                } catch (\Exception $e) {
                    // We ignore failed conversions. Remember, you MUST change your indices MANUALLY.
                }
            }
        }
    }

    /**
     * Returns true if table $tableName has an index named $indexName or if it's impossible to retrieve index names for
     * the table (not enough privileges, not a MySQL database, ...)
     *
     * @param string $tableName The name of the table
     * @param string $indexName The name of the index
     *
     * @return bool
     */
    private function hasIndex($tableName, $indexName)
    {
        static $isMySQL = null;
        static $cache = [];

        if (null === $isMySQL) {
            $driverType = $this->xtf0FDatabase->name;
            $driverType = strtolower($driverType);
            $isMySQL = true;

            if (
                0 === !strpos($driverType, 'mysql')
                && 'mysql' !== substr($driverType, -5)
                && 'mysqli' !== substr($driverType, -6)
            ) {
                $isMySQL = false;
            }
        }

        // Not MySQL? Lie and return true.
        if (!$isMySQL) {
            return true;
        }

        if (!isset($cache[$tableName])) {
            $cache[$tableName] = [];
        }

        if (!isset($cache[$tableName][$indexName])) {
            $cache[$tableName][$indexName] = true;

            try {
                $indices = [];
                $query = 'SHOW INDEXES FROM '.$this->xtf0FDatabase->qn($tableName);
                $indexDefinitions = $this->xtf0FDatabase->setQuery($query)->loadAssocList();

                if (!empty($indexDefinitions) && is_array($indexDefinitions)) {
                    foreach ($indexDefinitions as $indexDefinition) {
                        $indices[] = $indexDefinition['Key_name'];
                    }

                    $indices = array_unique($indices);
                }

                $cache[$tableName][$indexName] = in_array($indexName, $indices);
            } catch (\Exception $e) {
                // Ignore errors
            }
        }

        return $cache[$tableName][$indexName];
    }

    private static function getJoomlaInput()
    {
        if (version_compare(JVERSION, '4', '<')) {
            // Joomla 3 code
            jimport('joomla.filter.input');

            $input = JFactory::getApplication()->input;
            $data = $input->serialize();
            $jinput = new \Joomla\CMS\Input\Input([]);
            $jinput->unserialize($data);

            return $jinput;
        }

        $input = Joomla\CMS\Factory::getApplication()->input;
        $data = $input->getArray();
        $jinput = new \Joomla\CMS\Input\Input($data);

        return $jinput;
    }
}

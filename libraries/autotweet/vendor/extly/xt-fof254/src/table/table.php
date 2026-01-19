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
 * Normally this shouldn't be required. Some PHP versions, however, seem to
 * require this. Why? No idea whatsoever. If I remove it, XTF0F crashes on some
 * hosts. Same PHP version on another host and no problem occurs. Any takers?
 */
if (class_exists('XTF0FTable', false)) {
    return;
}

/**
 * FrameworkOnFramework Table class. The Table is one part controller, one part
 * model and one part data adapter. It's supposed to handle operations for single
 * records.
 *
 * @since    1.0
 */
class XTF0FTable extends XTF0FUtilsObject implements JTableInterface
{
    /**
     * Cache array for instances
     *
     * @var array
     */
    protected static $instances = [];

    /**
     * Include paths for searching for XTF0FTable classes.
     *
     * @var array
     */
    protected static $_includePaths = [];

    /**
     * The configuration parameters array
     *
     * @var array
     */
    protected $config = [];

    /**
     * Name of the database table to model.
     *
     * @var string
     */
    protected $_tbl = '';

    /**
     * Name of the primary key field in the table.
     *
     * @var string
     */
    protected $_tbl_key = '';

    /**
     * XTF0FDatabaseDriver object.
     *
     * @var XTF0FDatabaseDriver
     */
    protected $_db;

    /**
     * Should rows be tracked as ACL assets?
     *
     * @var bool
     */
    protected $_trackAssets = false;

    /**
     * Does the resource support joomla tags?
     *
     * @var bool
     */
    protected $_has_tags = false;

    /**
     * The rules associated with this record.
     *
     * @var JAccessRules a JAccessRules object
     */
    protected $_rules;

    /**
     * Indicator that the tables have been locked.
     *
     * @var bool
     */
    protected $_locked = false;

    /**
     * If this is set to true, it triggers automatically plugin events for
     * table actions
     *
     * @var bool
     */
    protected $_trigger_events = false;

    /**
     * Table alias used in queries
     *
     * @var string
     */
    protected $_tableAlias = false;

    /**
     * Array with alias for "special" columns such as ordering, hits etc etc
     *
     * @var array
     */
    protected $_columnAlias = [];

    /**
     * If set to true, it enabled automatic checks on fields based on columns properties
     *
     * @var bool
     */
    protected $_autoChecks = false;

    /**
     * Array with fields that should be skipped by automatic checks
     *
     * @var array
     */
    protected $_skipChecks = [];

    /**
     * Does the table actually exist? We need that to avoid PHP notices on
     * table-less views.
     *
     * @var bool
     */
    protected $_tableExists = true;

    /**
     * The asset key for items in this table. It's usually something in the
     * com_example.viewname format. They asset name will be this key appended
     * with the item's ID, e.g. com_example.viewname.123
     *
     * @var string
     */
    protected $_assetKey = '';

    /**
     * The input data
     *
     * @var \Joomla\CMS\Input\Input
     */
    protected $input = null;

    /**
     * Extended query including joins with other tables
     *
     * @var XTF0FDatabaseQuery
     */
    protected $_queryJoin = null;

    /**
     * The prefix for the table class
     *
     * @var string
     */
    protected $_tablePrefix = '';

    /**
     * The known fields for this table
     *
     * @var array
     */
    protected $knownFields = [];

    /**
     * A list of table fields, keyed per table
     *
     * @var array
     */
    protected static $tableFieldCache = [];

    /**
     * A list of tables in the database
     *
     * @var array
     */
    protected static $tableCache = [];

    /**
     * An instance of XTF0FConfigProvider to provision configuration overrides
     *
     * @var XTF0FConfigProvider
     */
    protected $configProvider = null;

    /**
     * XTF0FTableDispatcherBehavior for dealing with extra behaviors
     *
     * @var XTF0FTableDispatcherBehavior
     */
    protected $tableDispatcher = null;

    /**
     * List of default behaviors to apply to the table
     *
     * @var array
     */
    protected $default_behaviors = ['tags', 'assets'];

    /**
     * The relations object of the table. It's lazy-loaded by getRelations().
     *
     * @var XTF0FTableRelations
     */
    protected $_relations = null;

    /**
     * The configuration provider's key for this table, e.g. foobar.tables.bar for the #__foobar_bars table. This is set
     * automatically by the constructor
     *
     * @var string
     */
    protected $_configProviderKey = '';

    /**
     * The content type of the table. Required if using tags or content history behaviour
     *
     * @var string
     */
    protected $contentType = null;

    /**
     * Class Constructor.
     *
     * @param string              $table  name of the database table to model
     * @param string              $key    name of the primary key field in the table
     * @param XTF0FDatabaseDriver &$db    Database driver
     * @param array               $config The configuration parameters array
     */
    public function __construct($table, $key, &$db, $config = [])
    {
        $this->_tbl = $table;
        $this->_tbl_key = $key;
        $this->_db = $db;

        // Make sure the use XTF0F cache information is in the config
        if (!array_key_exists('use_table_cache', $config)) {
            $config['use_table_cache'] = XTF0FPlatform::getInstance()->isGlobalXTF0FCacheEnabled();
        }

        $this->config = $config;

        // Load the configuration provider
        $this->configProvider = new XTF0FConfigProvider();

        // Load the behavior dispatcher
        $this->tableDispatcher = new XTF0FTableDispatcherBehavior();

        // Initialise the table properties.

        if ($fields = $this->getTableFields()) {
            // Do I have anything joined?
            $j_fields = $this->getQueryJoinFields();

            if ($j_fields) {
                $fields = array_merge($fields, $j_fields);
            }

            $this->setKnownFields(array_keys($fields), true);
            $this->reset();
        } else {
            $this->_tableExists = false;
        }

        // Get the input
        if (array_key_exists('input', $config)) {
            if ($config['input'] instanceof \Joomla\CMS\Input\Input) {
                $this->input = $config['input'];
            } else {
                $this->input = new \Joomla\CMS\Input\Input($config['input']);
            }
        } else {
            $this->input = self::getJoomlaInput();
        }

        // Set the $name/$_name variable
        $component = $this->input->getCmd('option', 'com_foobar');

        if (array_key_exists('option', $config)) {
            $component = $config['option'];
        }

        $this->input->set('option', $component);

        // Apply table behaviors
        $type = explode('_', $this->_tbl);
        $type = $type[count($type) - 1];

        $this->_configProviderKey = $component.'.tables.'.XTF0FInflector::singularize($type);

        $configKey = $this->_configProviderKey.'.behaviors';

        if (isset($config['behaviors'])) {
            $behaviors = (array) $config['behaviors'];
        } elseif ($behaviors = $this->configProvider->get($configKey, null)) {
            $behaviors = explode(',', $behaviors);
        } else {
            $behaviors = $this->default_behaviors;
        }

        if (is_array($behaviors) && count($behaviors)) {
            foreach ($behaviors as $behavior) {
                $this->addBehavior($behavior);
            }
        }

        // If we are tracking assets, make sure an access field exists and initially set the default.
        $asset_id_field = $this->getColumnAlias('asset_id');
        $access_field = $this->getColumnAlias('access');

        if (in_array($asset_id_field, $this->getKnownFields())) {
            JLoader::import('joomla.access.rules');
            $this->_trackAssets = true;
        }

        // If the access property exists, set the default.
        if (in_array($access_field, $this->getKnownFields())) {
            $this->$access_field = (int) XTF0FPlatform::getInstance()->getConfig()->get('access');
        }

        $this->config = $config;
    }

    /**
     * Returns a static object instance of a particular table type
     *
     * @param string $type   The table name
     * @param string $prefix The prefix of the table class
     * @param array  $config Optional configuration variables
     *
     * @return XTF0FTable
     */
    public static function getInstance($type, $prefix = 'JTable', $config = [])
    {
        return self::getAnInstance($type, $prefix, $config);
    }

    /**
     * Returns a static object instance of a particular table type
     *
     * @param string $type   The table name
     * @param string $prefix The prefix of the table class
     * @param array  $config Optional configuration variables
     *
     * @return XTF0FTable
     */
    public static function &getAnInstance($type = null, $prefix = 'JTable', $config = [])
    {
        // Make sure $config is an array
        if (is_object($config)) {
            $config = (array) $config;
        } elseif (!is_array($config)) {
            $config = [];
        }

        // Guess the component name
        if (!array_key_exists('input', $config)) {
            $config['input'] = self::getJoomlaInput();
        }

        if ($config['input'] instanceof \Joomla\CMS\Input\Input) {
            $tmpInput = $config['input'];
        } else {
            $tmpInput = new \Joomla\CMS\Input\Input($config['input']);
        }

        $option = $tmpInput->getCmd('option', '');
        $tmpInput->set('option', $option);
        $config['input'] = $tmpInput;

        if (!in_array($prefix, ['Table', 'JTable'])) {
            preg_match('/(.*)Table$/', $prefix, $m);
            $option = 'com_'.strtolower($m[1]);
        }

        if (array_key_exists('option', $config)) {
            $option = $config['option'];
        }

        $config['option'] = $option;

        if (!array_key_exists('view', $config)) {
            $config['view'] = $config['input']->getCmd('view', 'cpanel');
        }

        if (null === $type) {
            if ('JTable' == $prefix) {
                $prefix = 'Table';
            }

            $type = $config['view'];
        }

        $type = preg_replace('/[^A-Z0-9_\.-]/i', '', $type);
        $tableClass = $prefix.ucfirst($type);

        $config['_table_type'] = $type;
        $config['_table_class'] = $tableClass;

        $xtf0FConfigProvider = new XTF0FConfigProvider();
        $configProviderKey = $option.'.views.'.XTF0FInflector::singularize($type).'.config.';

        if (!array_key_exists($tableClass, self::$instances)) {
            if (!class_exists($tableClass)) {
                $componentPaths = XTF0FPlatform::getInstance()->getComponentBaseDirs($config['option']);

                $searchPaths = [
                    $componentPaths['main'].'/tables',
                    $componentPaths['admin'].'/tables',
                ];

                if (array_key_exists('tablepath', $config)) {
                    array_unshift($searchPaths, $config['tablepath']);
                }

                $altPath = $xtf0FConfigProvider->get($configProviderKey.'table_path', null);

                if ($altPath) {
                    array_unshift($searchPaths, $componentPaths['admin'].'/'.$altPath);
                }

                $filesystem = XTF0FPlatform::getInstance()->getIntegrationObject('filesystem');

                $path = $filesystem->pathFind(
                    $searchPaths, strtolower($type).'.php'
                );

                if ($path) {
                    require_once $path;
                }
            }

            if (!class_exists($tableClass)) {
                $tableClass = 'XTF0FTable';
            }

            $component = str_replace('com_', '', $config['option']);
            $tbl_common = $component.'_';

            if (!array_key_exists('tbl', $config)) {
                $config['tbl'] = strtolower('#__'.$tbl_common.strtolower(XTF0FInflector::pluralize($type)));
            }

            $altTbl = $xtf0FConfigProvider->get($configProviderKey.'tbl', null);

            if ($altTbl) {
                $config['tbl'] = $altTbl;
            }

            if (!array_key_exists('tbl_key', $config)) {
                $keyName = XTF0FInflector::singularize($type);
                $config['tbl_key'] = strtolower($tbl_common.$keyName.'_id');
            }

            $altTblKey = $xtf0FConfigProvider->get($configProviderKey.'tbl_key', null);

            if ($altTblKey) {
                $config['tbl_key'] = $altTblKey;
            }

            if (!array_key_exists('db', $config)) {
                $config['db'] = XTF0FPlatform::getInstance()->getDbo();
            }

            // Assign the correct table alias
            if (array_key_exists('table_alias', $config)) {
                $table_alias = $config['table_alias'];
            } else {
                $configProviderTableAliasKey = $option.'.tables.'.XTF0FInflector::singularize($type).'.tablealias';
                $table_alias = $xtf0FConfigProvider->get($configProviderTableAliasKey, false);
            }

            // Can we use the XTF0F cache?
            if (!array_key_exists('use_table_cache', $config)) {
                $config['use_table_cache'] = XTF0FPlatform::getInstance()->isGlobalXTF0FCacheEnabled();
            }

            $alt_use_table_cache = $xtf0FConfigProvider->get($configProviderKey.'use_table_cache', null);

            if (null !== $alt_use_table_cache) {
                $config['use_table_cache'] = $alt_use_table_cache;
            }

            // Create a new table instance
            $instance = new $tableClass($config['tbl'], $config['tbl_key'], $config['db'], $config);
            $instance->setInput($tmpInput);
            $instance->setTablePrefix($prefix);
            $instance->setTableAlias($table_alias);

            // Determine and set the asset key for this table
            $assetKey = 'com_'.$component.'.'.strtolower(XTF0FInflector::singularize($type));
            $assetKey = $xtf0FConfigProvider->get($configProviderKey.'asset_key', $assetKey);
            $instance->setAssetKey($assetKey);

            if (array_key_exists('trigger_events', $config)) {
                $instance->setTriggerEvents($config['trigger_events']);
            }

            if (version_compare(JVERSION, '3.1', 'ge')) {
                if (array_key_exists('has_tags', $config)) {
                    $instance->setHasTags($config['has_tags']);
                }

                $altHasTags = $xtf0FConfigProvider->get($configProviderKey.'has_tags', null);

                if ($altHasTags) {
                    $instance->setHasTags($altHasTags);
                }
            } else {
                $instance->setHasTags(false);
            }

            $configProviderFieldmapKey = $option.'.tables.'.XTF0FInflector::singularize($type).'.field';
            $aliases = $xtf0FConfigProvider->get($configProviderFieldmapKey, $instance->_columnAlias);
            $instance->_columnAlias = array_merge($instance->_columnAlias, $aliases);

            self::$instances[$tableClass] = $instance;
        }

        return self::$instances[$tableClass];
    }

    /**
     * Force an instance inside class cache. Setting arguments to null nukes all or part of the cache
     *
     * @param string|null     $key      TableClass to replace. Set it to null to nuke the entire cache
     * @param XTF0FTable|null $instance Instance to replace. Set it to null to nuke $key instances
     *
     * @return bool Did I correctly switch the instance?
     */
    public static function forceInstance($key = null, $instance = null)
    {
        if (null === $key) {
            self::$instances = [];

            return true;
        } elseif ($key && isset(self::$instances[$key])) {
            // I'm forcing an instance, but it's not a XTF0FTable, abort! abort!
            if (!$instance || ($instance && $instance instanceof self)) {
                self::$instances[$key] = $instance;

                return true;
            }
        }

        return false;
    }

    /**
     * Replace the entire known fields array
     *
     * @param array $fields     A simple array of known field names
     * @param bool  $initialise Should we initialise variables to null?
     *
     * @return void
     */
    public function setKnownFields($fields, $initialise = false)
    {
        $this->knownFields = $fields;

        if ($initialise) {
            foreach ($this->knownFields as $knownField) {
                $this->{$knownField} = null;
            }
        }
    }

    /**
     * Get the known fields array
     *
     * @return array
     */
    public function getKnownFields()
    {
        return $this->knownFields;
    }

    /**
     * Does the specified field exist?
     *
     * @param string $fieldName The field name to search (it's OK to use aliases)
     *
     * @return bool
     */
    public function hasField($fieldName)
    {
        $search = $this->getColumnAlias($fieldName);

        return in_array($search, $this->knownFields);
    }

    /**
     * Add a field to the known fields array
     *
     * @param string $field      The name of the field to add
     * @param bool   $initialise Should we initialise the variable to null?
     *
     * @return void
     */
    public function addKnownField($field, $initialise = false)
    {
        if (!in_array($field, $this->knownFields)) {
            $this->knownFields[] = $field;

            if ($initialise) {
                $this->$field = null;
            }
        }
    }

    /**
     * Remove a field from the known fields array
     *
     * @param string $field The name of the field to remove
     *
     * @return void
     */
    public function removeKnownField($field)
    {
        if (in_array($field, $this->knownFields)) {
            $pos = array_search($field, $this->knownFields, true);
            unset($this->knownFields[$pos]);
        }
    }

    /**
     * Adds a behavior to the table
     *
     * @param string $name   The name of the behavior
     * @param array  $config Optional Behavior configuration
     *
     * @return bool
     */
    public function addBehavior($name, $config = [])
    {
        // First look for ComponentnameTableViewnameBehaviorName (e.g. FoobarTableItemsBehaviorTags)
        if (isset($this->config['option'])) {
            $option_name = str_replace('com_', '', $this->config['option']);
            $behaviorClass = $this->config['_table_class'].'Behavior'.ucfirst(strtolower($name));

            if (class_exists($behaviorClass)) {
                $behavior = new $behaviorClass($this->tableDispatcher, $config);

                return true;
            }

            // Then look for ComponentnameTableBehaviorName (e.g. FoobarTableBehaviorTags)
            $option_name = str_replace('com_', '', $this->config['option']);
            $behaviorClass = ucfirst($option_name).'TableBehavior'.ucfirst(strtolower($name));

            if (class_exists($behaviorClass)) {
                $behavior = new $behaviorClass($this->tableDispatcher, $config);

                return true;
            }
        }

        // Nothing found? Return false.

        $behaviorClass = 'XTF0FTableBehavior'.ucfirst(strtolower($name));

        if (class_exists($behaviorClass) && $this->tableDispatcher) {
            $behavior = new $behaviorClass($this->tableDispatcher, $config);

            return true;
        }

        return false;
    }

    /**
     * Sets the events trigger switch state
     *
     * @param bool $newState The new state of the switch (what else could it be?)
     *
     * @return void
     */
    public function setTriggerEvents($newState = false)
    {
        $this->_trigger_events = $newState;
    }

    /**
     * Gets the events trigger switch state
     *
     * @return bool
     */
    public function getTriggerEvents()
    {
        return $this->_trigger_events;
    }

    /**
     * Gets the has tags switch state
     *
     * @return bool
     */
    public function hasTags()
    {
        return $this->_has_tags;
    }

    /**
     * Sets the has tags switch state
     *
     * @param bool $newState
     */
    public function setHasTags($newState = false)
    {
        $this->_has_tags = false;

        // Tags are available only in 3.1+
        if (version_compare(JVERSION, '3.1', 'ge')) {
            $this->_has_tags = $newState;
        }
    }

    /**
     * Set the class prefix
     *
     * @param string $prefix The prefix
     */
    public function setTablePrefix($prefix)
    {
        $this->_tablePrefix = $prefix;
    }

    /**
     * Sets fields to be skipped from automatic checks.
     *
     * @param   array/string  $skip  Fields to be skipped by automatic checks
     *
     * @return void
     */
    public function setSkipChecks($skip)
    {
        $this->_skipChecks = (array) $skip;
    }

    /**
     * Method to load a row from the database by primary key and bind the fields
     * to the XTF0FTable instance properties.
     *
     * @param mixed $keys  An optional primary key value to load the row by, or an array of fields to match.  If not
     *                     set the instance property value is used.
     * @param bool  $reset true to reset the default values before loading the new row
     *
     * @return bool True if successful. False if row not found.
     *
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    public function load($keys = null, $reset = true)
    {
        if (!$this->_tableExists) {
            $result = false;

            return $this->onAfterLoad($result);
        }

        if (empty($keys)) {
            // If empty, use the value of the current key
            $keyName = $this->_tbl_key;

            $keyValue = $this->$keyName ?? null;

            // If empty primary key there's is no need to load anything

            if (empty($keyValue)) {
                $result = true;

                return $this->onAfterLoad($result);
            }

            $keys = [$keyName => $keyValue];
        } elseif (!is_array($keys)) {
            // Load by primary key.
            $keys = [$this->_tbl_key => $keys];
        }

        if ($reset) {
            $this->reset();
        }

        // Initialise the query.
        $xtf0FDatabaseQuery = $this->_db->getQuery(true);
        $xtf0FDatabaseQuery->select($this->_tbl.'.*');
        $xtf0FDatabaseQuery->from($this->_tbl);

        // Joined fields are ok, since I initialized them in the constructor
        $fields = $this->getKnownFields();

        foreach ($keys as $field => $value) {
            // Check that $field is in the table.

            if (!in_array($field, $fields)) {
                throw new UnexpectedValueException(sprintf('Missing field in table %s : %s.', $this->_tbl, $field));
            }

            // Add the search tuple to the query.
            $xtf0FDatabaseQuery->where($this->_db->qn($this->_tbl.'.'.$field).' = '.$this->_db->q($value));
        }

        // Do I have any joined table?
        $j_query = $this->getQueryJoin();

        if ($j_query) {
            if ($j_query->select && $j_query->select->getElements()) {
                // $query->select($this->normalizeSelectFields($j_query->select->getElements(), true));
                $xtf0FDatabaseQuery->select($j_query->select->getElements());
            }

            if ($j_query->join) {
                foreach ($j_query->join as $join) {
                    $t = (string) $join;

                    // Joomla doesn't provide any access to the "name" variable, so I have to work with strings...
                    if (false !== stripos($t, 'inner')) {
                        $xtf0FDatabaseQuery->innerJoin($join->getElements());
                    } elseif (false !== stripos($t, 'left')) {
                        $xtf0FDatabaseQuery->leftJoin($join->getElements());
                    } elseif (false !== stripos($t, 'right')) {
                        $xtf0FDatabaseQuery->rightJoin($join->getElements());
                    } elseif (false !== stripos($t, 'outer')) {
                        $xtf0FDatabaseQuery->outerJoin($join->getElements());
                    }
                }
            }
        }

        $this->_db->setQuery($xtf0FDatabaseQuery);

        $row = $this->_db->loadAssoc();

        // Check that we have a result.
        if (empty($row)) {
            $result = false;

            return $this->onAfterLoad($result);
        }

        // Bind the object with the row and return.
        $result = $this->bind($row);

        $this->onAfterLoad($result);

        return $result;
    }

    /**
     * Based on fields properties (nullable column), checks if the field is required or not
     *
     * @return bool
     */
    public function check()
    {
        if (!$this->_autoChecks) {
            return true;
        }

        $fields = $this->getTableFields();

        // No fields? Why in the hell am I here?
        if (!$fields) {
            return false;
        }

        $result = true;
        $known = $this->getKnownFields();
        $skipFields[] = $this->_tbl_key;

        if (in_array($this->getColumnAlias('title'), $known)
            && in_array($this->getColumnAlias('slug'), $known)) {
            $skipFields[] = $this->getColumnAlias('slug');
        }

        if (in_array($this->getColumnAlias('hits'), $known)) {
            $skipFields[] = $this->getColumnAlias('hits');
        }

        if (in_array($this->getColumnAlias('created_on'), $known)) {
            $skipFields[] = $this->getColumnAlias('created_on');
        }

        if (in_array($this->getColumnAlias('created_by'), $known)) {
            $skipFields[] = $this->getColumnAlias('created_by');
        }

        if (in_array($this->getColumnAlias('modified_on'), $known)) {
            $skipFields[] = $this->getColumnAlias('modified_on');
        }

        if (in_array($this->getColumnAlias('modified_by'), $known)) {
            $skipFields[] = $this->getColumnAlias('modified_by');
        }

        if (in_array($this->getColumnAlias('locked_by'), $known)) {
            $skipFields[] = $this->getColumnAlias('locked_by');
        }

        if (in_array($this->getColumnAlias('locked_on'), $known)) {
            $skipFields[] = $this->getColumnAlias('locked_on');
        }

        // Let's merge it with custom skips
        $skipFields = array_merge($skipFields, $this->_skipChecks);

        foreach ($fields as $field) {
            $fieldName = $field->Field;

            if (empty($fieldName)) {
                $fieldName = $field->column_name;
            }

            // Field is not nullable but it's null, set error

            if ('NO' == $field->Null && $this->$fieldName == '' && !in_array($fieldName, $skipFields)) {
                $text = str_replace('#__', 'COM_', $this->getTableName()).'_ERR_'.$fieldName;
                $this->setError(JText::_(strtoupper($text)));
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Method to reset class properties to the defaults set in the class
     * definition. It will ignore the primary key as well as any private class
     * properties.
     *
     * @return void
     */
    public function reset()
    {
        if (!$this->onBeforeReset()) {
            return false;
        }

        // Get the default values for the class from the table.
        $fields = $this->getTableFields();
        $j_fields = $this->getQueryJoinFields();

        if ($j_fields) {
            $fields = array_merge($fields, $j_fields);
        }

        if (is_array($fields) && $fields !== []) {
            foreach ($fields as $k => $v) {
                // If the property is not the primary key or private, reset it.
                if ($k != $this->_tbl_key && (0 !== strpos($k, '_'))) {
                    $this->$k = $v->Default;
                }
            }

            if (!$this->onAfterReset()) {
                return false;
            }
        }

        return null;
    }

    /**
     * Clones the current object, after resetting it
     *
     * @return static
     */
    public function getClone()
    {
        $clone = clone $this;
        $clone->reset();

        $key = $this->getKeyName();
        $clone->$key = null;

        return $clone;
    }

    /**
     * Generic check for whether dependencies exist for this object in the db schema
     *
     * @param int   $oid   The primary key of the record to delete
     * @param array $joins Any joins to foreign table, used to determine if dependent records exist
     *
     * @return bool True if the record can be deleted
     */
    public function canDelete($oid = null, $joins = null)
    {
        $k = $this->_tbl_key;

        if ($oid) {
            $this->$k = (int) $oid;
        }

        if (is_array($joins)) {
            $db = $this->_db;
            $query = $db->getQuery(true)
                ->select($db->qn('master').'.'.$db->qn($k))
                ->from($db->qn($this->_tbl).' AS '.$db->qn('master'));
            $tableNo = 0;

            foreach ($joins as $table) {
                $tableNo++;
                $query->select(
                    [
                        'COUNT(DISTINCT '.$db->qn('t'.$tableNo).'.'.$db->qn($table['idfield']).') AS '.$db->qn($table['idalias']),
                    ]
                );
                $query->join('LEFT', $db->qn($table['name']).
                    ' AS '.$db->qn('t'.$tableNo).
                    ' ON '.$db->qn('t'.$tableNo).'.'.$db->qn($table['joinfield']).
                    ' = '.$db->qn('master').'.'.$db->qn($k)
                );
            }

            $query->where($db->qn('master').'.'.$db->qn($k).' = '.$db->q($this->$k));
            $query->group($db->qn('master').'.'.$db->qn($k));
            $this->_db->setQuery((string) $query);

            if (version_compare(JVERSION, '3.0', 'ge')) {
                try {
                    $obj = $this->_db->loadObject();
                } catch (Exception $e) {
                    $this->setError($e->getMessage());
                }
            } elseif (!$obj = $this->_db->loadObject()) {
                $this->setError($this->_db->getErrorMsg());
                return false;
            }

            $msg = [];
            $i = 0;

            foreach ($joins as $join) {
                $k = $join['idalias'];

                if ($obj->$k > 0) {
                    $msg[] = JText::_($join['label']);
                }

                $i++;
            }

            if ($msg !== []) {
                $option = $this->input->getCmd('option', 'com_foobar');
                $comName = str_replace('com_', '', $option);
                $tview = str_replace('#__'.$comName.'_', '', $this->_tbl);
                $prefix = $option.'_'.$tview.'_NODELETE_';

                foreach ($msg as $key) {
                    $this->setError(JText::_($prefix.$key));
                }

                return false;
            } else {
                return true;
            }
        }

        return true;
    }

    /**
     * Method to bind an associative array or object to the XTF0FTable instance.This
     * method only binds properties that are publicly accessible and optionally
     * takes an array of properties to ignore when binding.
     *
     * @param mixed $src    an associative array or object to bind to the XTF0FTable instance
     * @param mixed $ignore an optional array or space separated list of properties to ignore while binding
     *
     * @return bool true on success
     *
     * @throws InvalidArgumentException
     */
    public function bind($src, $ignore = [])
    {
        if (!$this->onBeforeBind($src)) {
            return false;
        }

        // If the source value is not an array or object return false.
        if (!is_object($src) && !is_array($src)) {
            throw new InvalidArgumentException(sprintf('%s::bind(*%s*)', static::class, gettype($src)));
        }

        // If the source value is an object, get its accessible properties.
        if (is_object($src)) {
            $src = get_object_vars($src);
        }

        // If the ignore value is a string, explode it over spaces.
        if (!is_array($ignore)) {
            $ignore = explode(' ', $ignore);
        }

        // Bind the source value, excluding the ignored fields.
        foreach ($this->getKnownFields() as $k) {
            // Only process fields not in the ignore array.
            if (!in_array($k, $ignore) && isset($src[$k])) {
                $this->$k = $src[$k];
            }
        }

        $result = $this->onAfterBind($src);

        return $result;
    }

    /**
     * Method to store a row in the database from the XTF0FTable instance properties.
     * If a primary key value is set the row with that primary key value will be
     * updated with the instance property values.  If no primary key value is set
     * a new row will be inserted into the database with the properties from the
     * XTF0FTable instance.
     *
     * @param bool $updateNulls true to update fields even if they are null
     *
     * @return bool true on success
     */
    public function store($updateNulls = false)
    {
        if (!$this->onBeforeStore($updateNulls)) {
            return false;
        }

        $k = $this->_tbl_key;

        if ($this->$k == 0) {
            $this->$k = null;
        }

        // Create the object used for inserting/updating data to the database
        $fields = $this->getTableFields();
        $properties = $this->getKnownFields();
        $keys = [];

        foreach ($properties as $property) {
            // 'input' property is a reserved name

            if (isset($fields[$property])) {
                $keys[] = $property;
            }
        }

        $updateObject = [];
        foreach ($keys as $key) {
            $updateObject[$key] = $this->$key;
        }

        $updateObject = (object) $updateObject;

        // If a primary key exists update the object, otherwise insert it.
        if ($this->$k) {
            $result = $this->_db->updateObject($this->_tbl, $updateObject, $this->_tbl_key, $updateNulls);
        } else {
            $result = $this->_db->insertObject($this->_tbl, $updateObject, $this->_tbl_key);
        }

        // if ($result !== true) - EXTLY Fix - $result can be boolean or cursor
        if (false === $result) {
            $this->setError($this->_db->getErrorMsg());

            return false;
        }

        $this->bind($updateObject);

        if ($this->_locked) {
            $this->_unlock();
        }

        $result = $this->onAfterStore();

        return $result;
    }

    /**
     * Method to move a row in the ordering sequence of a group of rows defined by an SQL WHERE clause.
     * Negative numbers move the row up in the sequence and positive numbers move it down.
     *
     * @param int    $delta the direction and magnitude to move the row in the ordering sequence
     * @param string $where WHERE clause to use for limiting the selection of rows to compact the
     *                      ordering values
     *
     * @return mixed boolean  True on success
     *
     * @throws UnexpectedValueException
     */
    public function move($delta, $where = '')
    {
        if (!$this->onBeforeMove($delta, $where)) {
            return false;
        }

        // If there is no ordering field set an error and return false.
        $ordering_field = $this->getColumnAlias('ordering');

        if (!in_array($ordering_field, $this->getKnownFields())) {
            throw new UnexpectedValueException(sprintf('%s does not support ordering.', $this->_tbl));
        }

        // If the change is none, do nothing.
        if (empty($delta)) {
            $result = $this->onAfterMove();

            return $result;
        }

        $k = $this->_tbl_key;
        $row = null;
        $query = $this->_db->getQuery(true);

        // If the table is not loaded, return false
        if (empty($this->$k)) {
            return false;
        }

        // Select the primary key and ordering values from the table.
        $query->select([$this->_db->qn($this->_tbl_key), $this->_db->qn($ordering_field)]);
        $query->from($this->_tbl);

        // If the movement delta is negative move the row up.

        if ($delta < 0) {
            $query->where($this->_db->qn($ordering_field).' < '.$this->_db->q((int) $this->$ordering_field));
            $query->order($this->_db->qn($ordering_field).' DESC');
        }

        // If the movement delta is positive move the row down.

        elseif ($delta > 0) {
            $query->where($this->_db->qn($ordering_field).' > '.$this->_db->q((int) $this->$ordering_field));
            $query->order($this->_db->qn($ordering_field).' ASC');
        }

        // Add the custom WHERE clause if set.

        if ($where) {
            $query->where($where);
        }

        // Select the first row with the criteria.
        $this->_db->setQuery($query, 0, 1);
        $row = $this->_db->loadObject();

        // If a row is found, move the item.

        if (!empty($row)) {
            // Update the ordering field for this instance to the row's ordering value.
            $query = $this->_db->getQuery(true);
            $query->update($this->_tbl);
            $query->set($this->_db->qn($ordering_field).' = '.$this->_db->q((int) $row->$ordering_field));
            $query->where($this->_tbl_key.' = '.$this->_db->q($this->$k));
            $this->_db->setQuery($query);
            $this->_db->execute();

            // Update the ordering field for the row to this instance's ordering value.
            $query = $this->_db->getQuery(true);
            $query->update($this->_tbl);
            $query->set($this->_db->qn($ordering_field).' = '.$this->_db->q((int) $this->$ordering_field));
            $query->where($this->_tbl_key.' = '.$this->_db->q($row->$k));
            $this->_db->setQuery($query);
            $this->_db->execute();

            // Update the instance value.
            $this->$ordering_field = $row->$ordering_field;
        } else {
            // Update the ordering field for this instance.
            $query = $this->_db->getQuery(true);
            $query->update($this->_tbl);
            $query->set($this->_db->qn($ordering_field).' = '.$this->_db->q((int) $this->$ordering_field));
            $query->where($this->_tbl_key.' = '.$this->_db->q($this->$k));
            $this->_db->setQuery($query);
            $this->_db->execute();
        }

        $result = $this->onAfterMove();

        return $result;
    }

    /**
     * Change the ordering of the records of the table
     *
     * @param string $where The WHERE clause of the SQL used to fetch the order
     *
     * @return bool True is successful
     *
     * @throws UnexpectedValueException
     */
    public function reorder($where = '')
    {
        if (!$this->onBeforeReorder($where)) {
            return false;
        }

        // If there is no ordering field set an error and return false.

        $order_field = $this->getColumnAlias('ordering');

        if (!in_array($order_field, $this->getKnownFields())) {
            throw new UnexpectedValueException(sprintf('%s does not support ordering.', $this->_tbl_key));
        }

        $k = $this->_tbl_key;

        // Get the primary keys and ordering values for the selection.
        $query = $this->_db->getQuery(true);
        $query->select($this->_tbl_key.', '.$this->_db->qn($order_field));
        $query->from($this->_tbl);
        $query->where($this->_db->qn($order_field).' >= '.$this->_db->q(0));
        $query->order($this->_db->qn($order_field));

        // Setup the extra where and ordering clause data.

        if ($where) {
            $query->where($where);
        }

        $this->_db->setQuery($query);
        $rows = $this->_db->loadObjectList();

        // Compact the ordering values.

        foreach ($rows as $i => $row) {
            // Make sure the ordering is a positive integer.
            // Only update rows that are necessary.
            if ($row->$order_field >= 0 && $row->$order_field != $i + 1) {
                // Update the row ordering field.
                $query = $this->_db->getQuery(true);
                $query->update($this->_tbl);
                $query->set($this->_db->qn($order_field).' = '.$this->_db->q($i + 1));
                $query->where($this->_tbl_key.' = '.$this->_db->q($row->$k));
                $this->_db->setQuery($query);
                $this->_db->execute();
            }
        }

        $result = $this->onAfterReorder();

        return $result;
    }

    /**
     * Check out (lock) a record
     *
     * @param int $userId The locking user's ID
     * @param int $oid    The primary key value of the record to lock
     *
     * @return bool True on success
     */
    public function checkout($userId, $oid = null)
    {
        $fldLockedBy = $this->getColumnAlias('locked_by');
        $fldLockedOn = $this->getColumnAlias('locked_on');

        if (!in_array($fldLockedBy, $this->getKnownFields()) && !in_array($fldLockedOn, $this->getKnownFields())) {
            return true;
        }

        $k = $this->_tbl_key;

        if (null !== $oid) {
            $this->$k = $oid;
        }

        // No primary key defined, stop here
        if (!$this->$k) {
            return false;
        }

        $jDate = XTF0FPlatform::getInstance()->getDate();

        $time = method_exists($jDate, 'toSql') ? $jDate->toSql() : $jDate->toMySQL();

        $xtf0FDatabaseQuery = $this->_db->getQuery(true)
            ->update($this->_db->qn($this->_tbl))
            ->set(
                [
                    $this->_db->qn($fldLockedBy).' = '.$this->_db->q((int) $userId),
                    $this->_db->qn($fldLockedOn).' = '.$this->_db->q($time),
                ]
            )
            ->where($this->_db->qn($this->_tbl_key).' = '.$this->_db->q($this->$k));
        $this->_db->setQuery((string) $xtf0FDatabaseQuery);

        $this->$fldLockedBy = $userId;
        $this->$fldLockedOn = $time;

        return $this->_db->execute();
    }

    /**
     * Check in (unlock) a record
     *
     * @param int $oid The primary key value of the record to unlock
     *
     * @return bool True on success
     */
    public function checkin($oid = null)
    {
        $fldLockedBy = $this->getColumnAlias('locked_by');
        $fldLockedOn = $this->getColumnAlias('locked_on');

        if (!in_array($fldLockedBy, $this->getKnownFields()) && !in_array($fldLockedOn, $this->getKnownFields())) {
            return true;
        }

        $k = $this->_tbl_key;

        if (null !== $oid) {
            $this->$k = $oid;
        }

        if ($this->$k == null) {
            return false;
        }

        $xtf0FDatabaseQuery = $this->_db->getQuery(true)
            ->update($this->_db->qn($this->_tbl))
            ->set(
                [
                    $this->_db->qn($fldLockedBy).' = 0',
                    $this->_db->qn($fldLockedOn).' = '.$this->_db->q($this->_db->getNullDate()),
                ]
            )
            ->where($this->_db->qn($this->_tbl_key).' = '.$this->_db->q($this->$k));
        $this->_db->setQuery((string) $xtf0FDatabaseQuery);

        $this->$fldLockedBy = 0;
        $this->$fldLockedOn = '';

        return $this->_db->execute();
    }

    /**
     * Is a record locked?
     *
     * @param int $with           The userid to preform the match with. If an item is checked
     *                            out by this user the function will return false.
     * @param int $unused_against Junk inherited from JTable; ignore
     *
     * @return bool True if the record is locked by another user
     *
     * @throws UnexpectedValueException
     */
    public function isCheckedOut($with = 0, $unused_against = null)
    {
        $against = null;
        $fldLockedBy = $this->getColumnAlias('locked_by');

        $k = $this->_tbl_key;

        // If no primary key is given, return false.

        if ($this->$k === null) {
            throw new UnexpectedValueException('Null primary key not allowed.');
        }

        if (isset($this) && is_a($this, 'XTF0FTable') && !$against) {
            $against = $this->get($fldLockedBy);
        }

        // Item is not checked out, or being checked out by the same user

        if (!$against || $against == $with) {
            return false;
        }

        $session = JTable::getInstance('session');

        return $session->exists($against);
    }

    /**
     * Copy (duplicate) one or more records
     *
     * @param int|array $cid The primary key value (or values) or the record(s) to copy
     *
     * @return bool True on success
     */
    public function copy($cid = null)
    {
        // We have to cast the id as array, or the helper function will return an empty set
        if ($cid) {
            $cid = (array) $cid;
        }

        XTF0FUtilsArray::toInteger($cid);
        $k = $this->_tbl_key;

        if (count($cid) < 1) {
            if ($this->$k) {
                $cid = [$this->$k];
            } else {
                $this->setError('No items selected.');

                return false;
            }
        }

        $created_by = $this->getColumnAlias('created_by');
        $created_on = $this->getColumnAlias('created_on');
        $modified_by = $this->getColumnAlias('modified_by');
        $modified_on = $this->getColumnAlias('modified_on');

        $locked_byName = $this->getColumnAlias('locked_by');
        $checkin = in_array($locked_byName, $this->getKnownFields());

        foreach ($cid as $item) {
            // Prevent load with id = 0

            if (!$item) {
                continue;
            }

            $this->load($item);

            // We're using the checkin and the record is used by someone else
            if ($checkin && $this->isCheckedOut($item)) {
                continue;
            }

            if (!$this->onBeforeCopy($item)) {
                continue;
            }

            $this->$k = null;
            $this->$created_by = null;
            $this->$created_on = null;
            $this->$modified_on = null;
            $this->$modified_by = null;

            // Let's fire the event only if everything is ok
            if ($this->store()) {
                $this->onAfterCopy($item);
            }

            $this->reset();
        }

        return true;
    }

    /**
     * Publish or unpublish records
     *
     * @param int|array $cid     The primary key value(s) of the item(s) to publish/unpublish
     * @param int       $publish 1 to publish an item, 0 to unpublish
     * @param int       $user_id the user ID of the user (un)publishing the item
     *
     * @return bool True on success, false on failure (e.g. record is locked)
     */
    public function publish($cid = null, $publish = 1, $user_id = 0)
    {
        $enabledName = $this->getColumnAlias('enabled');
        $locked_byName = $this->getColumnAlias('locked_by');

        // Mhm... you called the publish method on a table without publish support...
        if (!in_array($enabledName, $this->getKnownFields())) {
            return false;
        }

        // We have to cast the id as array, or the helper function will return an empty set
        if ($cid) {
            $cid = (array) $cid;
        }

        XTF0FUtilsArray::toInteger($cid);
        $user_id = (int) $user_id;
        $publish = (int) $publish;
        $k = $this->_tbl_key;

        if (count($cid) < 1) {
            if ($this->$k) {
                $cid = [$this->$k];
            } else {
                $this->setError('No items selected.');

                return false;
            }
        }

        if (!$this->onBeforePublish($cid, $publish)) {
            return false;
        }

        $xtf0FDatabaseQuery = $this->_db->getQuery(true)
            ->update($this->_db->qn($this->_tbl))
            ->set($this->_db->qn($enabledName).' = '.(int) $publish);

        $checkin = in_array($locked_byName, $this->getKnownFields());

        if ($checkin) {
            $xtf0FDatabaseQuery->where(
                ' ('.$this->_db->qn($locked_byName).
                    ' = 0 OR '.$this->_db->qn($locked_byName).' = '.(int) $user_id.')', 'AND'
            );
        }

        // TODO Rewrite this statment using IN. Check if it work in SQLServer and PostgreSQL
        $cids = $this->_db->qn($k).' = '.implode(' OR '.$this->_db->qn($k).' = ', $cid);

        $xtf0FDatabaseQuery->where('('.$cids.')');

        $this->_db->setQuery((string) $xtf0FDatabaseQuery);

        if (version_compare(JVERSION, '3.0', 'ge')) {
            try {
                $this->_db->execute();
            } catch (Exception $e) {
                $this->setError($e->getMessage());
            }
        } elseif (!$this->_db->execute()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        if (1 == count($cid) && $checkin && 1 == $this->_db->getAffectedRows()) {
            $this->checkin($cid[0]);
            if ($this->$k == $cid[0]) {
                $this->$enabledName = $publish;
            }
        }

        $this->setError('');

        return true;
    }

    /**
     * Delete a record
     *
     * @param int $oid The primary key value of the item to delete
     *
     * @return bool True on success
     *
     * @throws UnexpectedValueException
     */
    public function delete($oid = null)
    {
        if ($oid) {
            $this->load($oid);
        }

        $k = $this->_tbl_key;
        $pk = $oid ?: $this->$k;

        // If no primary key is given, return false.
        if (!$pk) {
            throw new UnexpectedValueException('Null primary key not allowed.');
        }

        // Execute the logic only if I have a primary key, otherwise I could have weird results
        if (!$this->onBeforeDelete($oid)) {
            return false;
        }

        // Delete the row by primary key.
        $xtf0FDatabaseQuery = $this->_db->getQuery(true);
        $xtf0FDatabaseQuery->delete();
        $xtf0FDatabaseQuery->from($this->_tbl);
        $xtf0FDatabaseQuery->where($this->_tbl_key.' = '.$this->_db->q($pk));

        $this->_db->setQuery($xtf0FDatabaseQuery);

        $this->_db->execute();

        $result = $this->onAfterDelete($oid);

        return $result;
    }

    /**
     * Register a hit on a record
     *
     * @param int  $oid The primary key value of the record
     * @param bool $log Should I log the hit?
     *
     * @return bool True on success
     */
    public function hit($oid = null, $log = false)
    {
        if (!$this->onBeforeHit($oid, $log)) {
            return false;
        }

        // If there is no hits field, just return true.
        $hits_field = $this->getColumnAlias('hits');

        if (!in_array($hits_field, $this->getKnownFields())) {
            return true;
        }

        $k = $this->_tbl_key;
        $pk = ($oid) ?: $this->$k;

        // If no primary key is given, return false.
        if (!$pk) {
            $result = false;
        } else {
            // Check the row in by primary key.
            $query = $this->_db->getQuery(true)
                          ->update($this->_tbl)
                          ->set($this->_db->qn($hits_field).' = ('.$this->_db->qn($hits_field).' + 1)')
                          ->where($this->_tbl_key.' = '.$this->_db->q($pk));

            $this->_db->setQuery($query)->execute();

            // In order to update the table object, I have to load the table
            if (!$this->$k) {
                $query = $this->_db->getQuery(true)
                              ->select($this->_db->qn($hits_field))
                              ->from($this->_db->qn($this->_tbl))
                              ->where($this->_db->qn($this->_tbl_key).' = '.$this->_db->q($pk));

                $this->$hits_field = $this->_db->setQuery($query)->loadResult();
            } else {
                // Set table values in the object.
                $this->$hits_field++;
            }

            $result = true;
        }

        if ($result) {
            $result = $this->onAfterHit($oid);
        }

        return $result;
    }

    /**
     * Export the item as a CSV line
     *
     * @param string $separator CSV separator. Tip: use "\t" to get a TSV file instead.
     *
     * @return string The CSV line
     */
    public function toCSV($separator = ',')
    {
        $csv = [];

        foreach (get_object_vars($this) as $k => $v) {
            if (!in_array($k, $this->getKnownFields())) {
                continue;
            }

            $csv[] = '"'.str_replace('"', '""', $v).'"';
        }

        $csv = implode($separator, $csv);

        return $csv;
    }

    /**
     * Exports the table in array format
     *
     * @return array
     */
    public function getData()
    {
        $ret = [];

        foreach (get_object_vars($this) as $k => $v) {
            if (!in_array($k, $this->getKnownFields())) {
                continue;
            }

            $ret[$k] = $v;
        }

        return $ret;
    }

    /**
     * Get the header for exporting item list to CSV
     *
     * @param string $separator CSV separator. Tip: use "\t" to get a TSV file instead.
     *
     * @return string The CSV file's header
     */
    public function getCSVHeader($separator = ',')
    {
        $csv = [];

        foreach (array_keys(get_object_vars($this)) as $k) {
            if (!in_array($k, $this->getKnownFields())) {
                continue;
            }

            $csv[] = '"'.str_replace('"', '\"', $k).'"';
        }

        $csv = implode($separator, $csv);

        return $csv;
    }

    /**
     * Get the columns from a database table.
     *
     * @param string $tableName Table name. If null current table is used
     *
     * @return mixed an array of the field names, or false if an error occurs
     */
    public function getTableFields($tableName = null)
    {
        // Should I load the cached data?
        $useCache = array_key_exists('use_table_cache', $this->config) ? $this->config['use_table_cache'] : false;

        // Make sure we have a list of tables in this db

        if (empty(self::$tableCache)) {
            if ($useCache) {
                // Try to load table cache from a cache file
                $cacheData = XTF0FPlatform::getInstance()->getCache('tables', null);

                // Unserialise the cached data, or set the table cache to empty
                // if the cache data wasn't loaded.
                self::$tableCache = null !== $cacheData ? json_decode($cacheData, true) : [];
            }

            // This check is true if the cache data doesn't exist / is not loaded
            if (empty(self::$tableCache)) {
                self::$tableCache = $this->_db->getTableList();

                if ($useCache) {
                    XTF0FPlatform::getInstance()->setCache('tables', json_encode(self::$tableCache));
                }
            }
        }

        // Make sure the cached table fields cache is loaded
        if (empty(self::$tableFieldCache) && $useCache) {
            // Try to load table cache from a cache file
            $cacheData = XTF0FPlatform::getInstance()->getCache('tablefields', null);
            // Unserialise the cached data, or set to empty if the cache
            // data wasn't loaded.
            if (null !== $cacheData) {
                $decoded = json_decode($cacheData, true);
                $tableCache = [];

                if (count($decoded) > 0) {
                    foreach ($decoded as $myTableName => $tableFields) {
                        $temp = [];

                        if (is_array($tableFields)) {
                            foreach ($tableFields as $field => $def) {
                                $temp[$field] = (object) $def;
                            }

                            $tableCache[$myTableName] = $temp;
                        } elseif (is_object($tableFields) || is_bool($tableFields)) {
                            $tableCache[$myTableName] = $tableFields;
                        }
                    }
                }

                self::$tableFieldCache = $tableCache;
            } else {
                self::$tableFieldCache = [];
            }
        }

        if (!$tableName) {
            $tableName = $this->_tbl;
        }

        // Try to load again column specifications if the table is not loaded OR if it's loaded and
        // the previous call returned an error
        if (!array_key_exists($tableName, self::$tableFieldCache) ||
            (isset(self::$tableFieldCache[$tableName]) && !self::$tableFieldCache[$tableName])) {
            // Lookup the fields for this table only once.
            $name = $tableName;

            $prefix = $this->_db->getPrefix();

            $checkName = '#__' === substr($name, 0, 3) ? $prefix.substr($name, 3) : $name;

            if (!in_array($checkName, self::$tableCache)) {
                // The table doesn't exist. Return false.
                self::$tableFieldCache[$tableName] = false;
            } elseif (version_compare(JVERSION, '3.0', 'ge')) {
                $fields = $this->_db->getTableColumns($name, false);

                if (empty($fields)) {
                    $fields = false;
                }

                self::$tableFieldCache[$tableName] = $fields;
            } else {
                $fields = $this->_db->getTableFields($name, false);

                if (!isset($fields[$name])) {
                    $fields = false;
                }

                self::$tableFieldCache[$tableName] = $fields[$name];
            }

            // PostgreSQL date type compatibility
            if (('postgresql' == $this->_db->name) && (false != self::$tableFieldCache[$tableName])) {
                foreach (self::$tableFieldCache[$tableName] as $field) {
                    if ('timestamp without time zone' === strtolower($field->type) && stristr($field->Default, "'::timestamp without time zone")) {
                        [$date, $junk] = explode('::', $field->Default, 2);
                        $field->Default = trim($date, "'");
                    }
                }
            }

            // Save the data for this table into the cache
            if ($useCache) {
                $cacheData = XTF0FPlatform::getInstance()->setCache('tablefields', json_encode(self::$tableFieldCache));
            }
        }

        return self::$tableFieldCache[$tableName];
    }

    public function getTableAlias()
    {
        return $this->_tableAlias;
    }

    public function setTableAlias($string)
    {
        $string = preg_replace('#[^A-Z0-9_]#i', '', $string);
        $this->_tableAlias = $string;
    }

    /**
     * Method to return the real name of a "special" column such as ordering, hits, published
     * etc etc. In this way you are free to follow your db naming convention and use the
     * built in Joomla functions.
     *
     * @param string $column Name of the "special" column (ie ordering, hits etc etc)
     *
     * @return string The string that identify the special
     */
    public function getColumnAlias($column)
    {
        $return = $this->_columnAlias[$column] ?? $column;

        $return = preg_replace('#[^A-Z0-9_]#i', '', $return);

        return $return;
    }

    /**
     * Method to register a column alias for a "special" column.
     *
     * @param string $column      The "special" column (ie ordering)
     * @param string $columnAlias The real column name (ie foo_ordering)
     *
     * @return void
     */
    public function setColumnAlias($column, $columnAlias)
    {
        $column = strtolower($column);

        $column = preg_replace('#[^A-Z0-9_]#i', '', $column);
        $this->_columnAlias[$column] = $columnAlias;
    }

    /**
     * Get a JOIN query, used to join other tables
     *
     * @param bool $asReference Return an object reference instead of a copy
     *
     * @return XTF0FDatabaseQuery Query used to join other tables
     */
    public function getQueryJoin($asReference = false)
    {
        if ($asReference) {
            return $this->_queryJoin;
        } elseif ($this->_queryJoin) {
            return clone $this->_queryJoin;
        } else {
            return null;
        }
    }

    /**
     * Sets the query with joins to other tables
     *
     * @param XTF0FDatabaseQuery $query The JOIN query to use
     *
     * @return void
     */
    public function setQueryJoin($query)
    {
        $this->_queryJoin = $query;
    }

    /**
     * Replace the input object of this table with the provided \Joomla\CMS\Input\Input object
     *
     * @param \Joomla\CMS\Input\Input $input The new input object
     *
     * @return void
     */
    public function setInput(Joomla\CMS\Input\Input $input)
    {
        $this->input = $input;
    }

    /**
     * Get the columns from database table.
     *
     * @return mixed an array of the field names, or false if an error occurs
     *
     * @deprecated  2.1
     */
    public function getFields()
    {
        return $this->getTableFields();
    }

    /**
     * Add a filesystem path where XTF0FTable should search for table class files.
     * You may either pass a string or an array of paths.
     *
     * @param mixed $path a filesystem path or array of filesystem paths to add
     *
     * @return array an array of filesystem paths to find XTF0FTable classes in
     */
    public static function addIncludePath($path = null)
    {
        // If the internal paths have not been initialised, do so with the base table path.
        if (empty(self::$_includePaths)) {
            self::$_includePaths = [__DIR__];
        }

        // Convert the passed path(s) to add to an array.
        $path = (array) $path;

        // If we have new paths to add, do so.
        if ($path !== [] && !in_array($path, self::$_includePaths)) {
            // Check and add each individual new path.
            foreach ($path as $dir) {
                // Sanitize path.
                $dir = trim($dir);

                // Add to the front of the list so that custom paths are searched first.
                array_unshift(self::$_includePaths, $dir);
            }
        }

        return self::$_includePaths;
    }

    /**
     * Method to compute the default name of the asset.
     * The default name is in the form table_name.id
     * where id is the value of the primary key of the table.
     *
     * @return string
     *
     * @throws UnexpectedValueException
     */
    public function getAssetName()
    {
        $k = $this->_tbl_key;

        // If there is no assetKey defined, stop here, or we'll get a wrong name
        if (!$this->_assetKey || !$this->$k) {
            throw new UnexpectedValueException('Table must have an asset key defined and a value for the table id in order to track assets');
        }

        return $this->_assetKey.'.'.(int) $this->$k;
    }

    /**
     * Method to compute the default name of the asset.
     * The default name is in the form table_name.id
     * where id is the value of the primary key of the table.
     *
     * @return string
     *
     * @throws UnexpectedValueException
     */
    public function getAssetKey()
    {
        return $this->_assetKey;
    }

    /**
     * Method to return the title to use for the asset table.  In
     * tracking the assets a title is kept for each asset so that there is some
     * context available in a unified access manager.  Usually this would just
     * return $this->title or $this->name or whatever is being used for the
     * primary name of the row. If this method is not overridden, the asset name is used.
     *
     * @return string the string to use as the title in the asset table
     */
    public function getAssetTitle()
    {
        return $this->getAssetName();
    }

    /**
     * Method to get the parent asset under which to register this one.
     * By default, all assets are registered to the ROOT node with ID,
     * which will default to 1 if none exists.
     * The extended class can define a table and id to lookup.  If the
     * asset does not exist it will be created.
     *
     * @param XTF0FTable $table a XTF0FTable object for the asset parent
     * @param int        $id    Id to look up
     *
     * @return int
     */
    public function getAssetParentId($table = null, $id = null)
    {
        // For simple cases, parent to the asset root.
        $assets = JTable::getInstance('Asset', 'JTable', ['dbo' => $this->getDbo()]);
        $rootId = $assets->getRootId();

        if (!empty($rootId)) {
            return $rootId;
        }

        return 1;
    }

    /**
     * This method sets the asset key for the items of this table. Obviously, it
     * is only meant to be used when you have a table with an asset field.
     *
     * @param string $assetKey The name of the asset key to use
     *
     * @return void
     */
    public function setAssetKey($assetKey)
    {
        $this->_assetKey = $assetKey;
    }

    /**
     * Method to get the database table name for the class.
     *
     * @return string the name of the database table being modeled
     */
    public function getTableName()
    {
        return $this->_tbl;
    }

    /**
     * Method to get the primary key field name for the table.
     *
     * @return string the name of the primary key for the table
     */
    public function getKeyName()
    {
        return $this->_tbl_key;
    }

    /**
     * Returns the identity value of this record
     */
    public function getId()
    {
        $key = $this->getKeyName();

        return $this->$key;
    }

    /**
     * Method to get the XTF0FDatabaseDriver object.
     *
     * @return XTF0FDatabaseDriver the internal database driver object
     */
    public function getDbo()
    {
        return $this->_db;
    }

    /**
     * Method to set the XTF0FDatabaseDriver object.
     *
     * @param XTF0FDatabaseDriver $db a XTF0FDatabaseDriver object to be used by the table object
     *
     * @return bool true on success
     */
    public function setDBO($db)
    {
        $this->_db = $db;

        return true;
    }

    /**
     * Method to set rules for the record.
     *
     * @param mixed $input a JAccessRules object, JSON string, or array
     *
     * @return void
     */
    public function setRules($input)
    {
        $this->_rules = $input instanceof JAccessRules ? $input : new JAccessRules($input);
    }

    /**
     * Method to get the rules for the record.
     *
     * @return JAccessRules object
     */
    public function getRules()
    {
        return $this->_rules;
    }

    /**
     * Method to check if the record is treated as an ACL asset
     *
     * @return bool [description]
     */
    public function isAssetsTracked()
    {
        return $this->_trackAssets;
    }

    /**
     * Method to manually set this record as ACL asset or not.
     * We have to do this since the automatic check is made in the constructor, but here we can't set any alias.
     * So, even if you have an alias for `asset_id`, it wouldn't be reconized and assets won't be tracked.
     */
    public function setAssetsTracked($state)
    {
        $state = (bool) $state;

        if ($state) {
            JLoader::import('joomla.access.rules');
        }

        $this->_trackAssets = $state;
    }

    /**
     * Method to provide a shortcut to binding, checking and storing a XTF0FTable
     * instance to the database table.  The method will check a row in once the
     * data has been stored and if an ordering filter is present will attempt to
     * reorder the table rows based on the filter.  The ordering filter is an instance
     * property name.  The rows that will be reordered are those whose value matches
     * the XTF0FTable instance for the property specified.
     *
     * @param mixed  $src            an associative array or object to bind to the XTF0FTable instance
     * @param string $orderingFilter Filter for the order updating
     * @param mixed  $ignore         an optional array or space separated list of properties
     *                               to ignore while binding
     *
     * @return bool true on success
     */
    public function save($src, $orderingFilter = '', $ignore = '')
    {
        // Attempt to bind the source to the instance.
        if (!$this->bind($src, $ignore)) {
            return false;
        }

        // Run any sanity checks on the instance and verify that it is ready for storage.
        if (!$this->check()) {
            return false;
        }

        // Attempt to store the properties to the database table.
        if (!$this->store()) {
            return false;
        }

        // Attempt to check the row in, just in case it was checked out.
        if (!$this->checkin()) {
            return false;
        }

        // If an ordering filter is set, attempt reorder the rows in the table based on the filter and value.
        if ($orderingFilter) {
            $filterValue = $this->$orderingFilter;
            $this->reorder($orderingFilter ? $this->_db->qn($orderingFilter).' = '.$this->_db->q($filterValue) : '');
        }

        // Set the error to empty and return true.
        $this->setError('');

        return true;
    }

    /**
     * Method to get the next ordering value for a group of rows defined by an SQL WHERE clause.
     * This is useful for placing a new item last in a group of items in the table.
     *
     * @param string $where WHERE clause to use for selecting the MAX(ordering) for the table
     *
     * @return mixed boolean false an failure or the next ordering value as an integer
     */
    public function getNextOrder($where = '')
    {
        // If there is no ordering field set an error and return false.
        $ordering = $this->getColumnAlias('ordering');
        if (!in_array($ordering, $this->getKnownFields())) {
            throw new UnexpectedValueException(sprintf('%s does not support ordering.', static::class));
        }

        // Get the largest ordering value for a given where clause.
        $xtf0FDatabaseQuery = $this->_db->getQuery(true);
        $xtf0FDatabaseQuery->select('MAX('.$this->_db->qn($ordering).')');
        $xtf0FDatabaseQuery->from($this->_tbl);

        if ($where) {
            $xtf0FDatabaseQuery->where($where);
        }

        $this->_db->setQuery($xtf0FDatabaseQuery);
        $max = (int) $this->_db->loadResult();

        // Return the largest ordering value + 1.
        return $max + 1;
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get the content type for ucm
     *
     * @return string The content type alias
     */
    public function getContentType()
    {
        if ($this->contentType) {
            return $this->contentType;
        }

        /**
         * When tags was first introduced contentType variable didn't exist - so we guess one
         * This will fail if content history behvaiour is enabled. This code is deprecated
         * and will be removed in XTF0F 3.0 in favour of the content type class variable
         */
        $component = $this->input->get('option');

        $view = XTF0FInflector::singularize($this->input->get('view'));
        $alias = $component.'.'.$view;

        return $alias;
    }

    /**
     * Returns the table relations object of the current table, lazy-loading it if necessary
     *
     * @return XTF0FTableRelations
     */
    public function getRelations()
    {
        if (null === $this->_relations) {
            $this->_relations = new XTF0FTableRelations($this);
        }

        return $this->_relations;
    }

    /**
     * Gets a reference to the configuration parameters provider for this table
     *
     * @return XTF0FConfigProvider
     */
    public function getConfigProvider()
    {
        return $this->configProvider;
    }

    /**
     * Returns the configuration parameters provider's key for this table
     *
     * @return string
     */
    public function getConfigProviderKey()
    {
        return $this->_configProviderKey;
    }

    /**
     * Check if a UCM content type exists for this resource, and
     * create it if it does not
     *
     * @param string $alias The content type alias (optional)
     *
     * @return null
     */
    public function checkContentType($alias = null)
    {
        $jTableContenttype = new JTableContenttype($this->getDbo());

        if (!$alias) {
            $alias = $this->getContentType();
        }

        $aliasParts = explode('.', $alias);

        // Fetch the extension name
        $component = $aliasParts[0];
        $component = JComponentHelper::getComponent($component);

        // Fetch the name using the menu item
        $xtf0FDatabaseQuery = $this->getDbo()->getQuery(true);
        $xtf0FDatabaseQuery->select('title')->from('#__menu')->where('component_id = '.(int) $component->id);
        $this->getDbo()->setQuery($xtf0FDatabaseQuery);
        $component_name = JText::_($this->getDbo()->loadResult());

        $name = $component_name.' '.ucfirst($aliasParts[1]);

        // Create a new content type for our resource
        if (!$jTableContenttype->load(['type_alias' => $alias])) {
            $jTableContenttype->type_title = $name;
            $jTableContenttype->type_alias = $alias;
            $jTableContenttype->table = json_encode(
                [
                    'special' => [
                        'dbtable' => $this->getTableName(),
                        'key'     => $this->getKeyName(),
                        'type'    => $name,
                        'prefix'  => $this->_tablePrefix,
                        'class'   => 'XTF0FTable',
                        'config'  => 'array()',
                    ],
                    'common' => [
                        'dbtable' => '#__ucm_content',
                        'key' => 'ucm_id',
                        'type' => 'CoreContent',
                        'prefix' => 'JTable',
                        'config' => 'array()',
                    ],
                ]
            );

            $jTableContenttype->field_mappings = json_encode(
                [
                    'common' => [
                        0 => [
                            'core_content_item_id' => $this->getKeyName(),
                            'core_title'           => $this->getUcmCoreAlias('title'),
                            'core_state'           => $this->getUcmCoreAlias('enabled'),
                            'core_alias'           => $this->getUcmCoreAlias('alias'),
                            'core_created_time'    => $this->getUcmCoreAlias('created_on'),
                            'core_modified_time'   => $this->getUcmCoreAlias('created_by'),
                            'core_body'            => $this->getUcmCoreAlias('body'),
                            'core_hits'            => $this->getUcmCoreAlias('hits'),
                            'core_publish_up'      => $this->getUcmCoreAlias('publish_up'),
                            'core_publish_down'    => $this->getUcmCoreAlias('publish_down'),
                            'core_access'          => $this->getUcmCoreAlias('access'),
                            'core_params'          => $this->getUcmCoreAlias('params'),
                            'core_featured'        => $this->getUcmCoreAlias('featured'),
                            'core_metadata'        => $this->getUcmCoreAlias('metadata'),
                            'core_language'        => $this->getUcmCoreAlias('language'),
                            'core_images'          => $this->getUcmCoreAlias('images'),
                            'core_urls'            => $this->getUcmCoreAlias('urls'),
                            'core_version'         => $this->getUcmCoreAlias('version'),
                            'core_ordering'        => $this->getUcmCoreAlias('ordering'),
                            'core_metakey'         => $this->getUcmCoreAlias('metakey'),
                            'core_metadesc'        => $this->getUcmCoreAlias('metadesc'),
                            'core_catid'           => $this->getUcmCoreAlias('cat_id'),
                            'core_xreference'      => $this->getUcmCoreAlias('xreference'),
                            'asset_id'             => $this->getUcmCoreAlias('asset_id'),
                        ],
                    ],
                    'special' => [
                        0 => [
                        ],
                    ],
                ]
            );

            $ignoreFields = [
                $this->getUcmCoreAlias('modified_on', null),
                $this->getUcmCoreAlias('modified_by', null),
                $this->getUcmCoreAlias('locked_by', null),
                $this->getUcmCoreAlias('locked_on', null),
                $this->getUcmCoreAlias('hits', null),
                $this->getUcmCoreAlias('version', null),
            ];

            $jTableContenttype->content_history_options = json_encode(
                [
                    'ignoreChanges' => array_filter($ignoreFields, 'strlen'),
                ]
            );

            $jTableContenttype->router = '';

            $jTableContenttype->store();
        }
    }

    /**
     * Extracts the fields from the join query
     *
     * @return array Fields contained in the join query
     */
    protected function getQueryJoinFields()
    {
        $xtf0FDatabaseQuery = $this->getQueryJoin();

        if (!$xtf0FDatabaseQuery) {
            return [];
        }

        $tables = [];
        $j_tables = [];
        $j_fields = [];

        // Get joined tables. Ignore FROM clause, since it should not be used (the starting point is the table "table")
        $joins = $xtf0FDatabaseQuery->join;

        foreach ($joins as $join) {
            $tables = array_merge($tables, $join->getElements());
        }

        // Clean up table names
        foreach ($tables as $table) {
            preg_match('#(.*)((\w)*(on|using))(.*)#i', $table, $matches);

            if ($matches && isset($matches[1])) {
                // I always want the first part, no matter what
                $parts = explode(' ', $matches[1]);
                $t_table = $parts[0];

                if ($this->isQuoted($t_table)) {
                    $t_table = substr($t_table, 1, strlen($t_table) - 2);
                }

                if (!in_array($t_table, $j_tables)) {
                    $j_tables[] = $t_table;
                }
            }
        }

        // Do I have the current table inside the query join? Remove it (its fields are already ok)
        $find = array_search($this->getTableName(), $j_tables, true);
        if (false !== $find) {
            unset($j_tables[$find]);
        }

        // Get table fields
        $fields = [];

        foreach ($j_tables as $j_table) {
            $t_fields = $this->getTableFields($j_table);

            if ($t_fields) {
                $fields = array_merge($fields, $t_fields);
            }
        }

        // Remove any fields that aren't in the joined select
        $j_select = $xtf0FDatabaseQuery->select;

        if ($j_select && $j_select->getElements()) {
            $j_fields = $this->normalizeSelectFields($j_select->getElements());
        }

        // I can intesect the keys
        $fields = array_intersect_key($fields, $j_fields);

        // Now I walk again the array to change the key of columns that have an alias
        foreach ($j_fields as $column => $alias) {
            if ($column != $alias) {
                $fields[$alias] = $fields[$column];
                unset($fields[$column]);
            }
        }

        return $fields;
    }

    /**
     * Normalizes the fields, returning an associative array with all the fields.
     * Ie array('foobar as foo, bar') becomes array('foobar' => 'foo', 'bar' => 'bar')
     *
     * @param array $fields Array with column fields
     *
     * @return array Normalized array
     */
    protected function normalizeSelectFields($fields)
    {
        $xtf0FDatabaseDriver = XTF0FPlatform::getInstance()->getDbo();
        $return = [];

        foreach ($fields as $field) {
            $t_fields = explode(',', $field);

            foreach ($t_fields as $t_field) {
                // Is there any alias?
                $parts = preg_split('#\sas\s#i', $t_field);

                // Do I have a table.column situation? Let's get the field name
                $tableField = explode('.', $parts[0]);

                $column = isset($tableField[1]) ? trim($tableField[1]) : trim($tableField[0]);

                // Is this field quoted? If so, remove the quotes
                if ($this->isQuoted($column)) {
                    $column = substr($column, 1, strlen($column) - 2);
                }

                if (isset($parts[1])) {
                    $alias = trim($parts[1]);

                    // Is this field quoted? If so, remove the quotes
                    if ($this->isQuoted($alias)) {
                        $alias = substr($alias, 1, strlen($alias) - 2);
                    }
                } else {
                    $alias = $column;
                }

                $return[$column] = $alias;
            }
        }

        return $return;
    }

    /**
     * Is the field quoted?
     *
     * @param string $column Column field
     *
     * @return bool Is the field quoted?
     */
    protected function isQuoted($column)
    {
        // Empty string, un-quoted by definition
        if (!$column) {
            return false;
        }

        // I need some "magic". If the first char is not a letter, a number
        // an underscore or # (needed for table), then most likely the field is quoted
        preg_match_all('/^[a-z0-9_#]/i', $column, $matches);
        return !$matches[0];
    }

    /**
     * The event which runs before binding data to the table
     *
     * NOTE TO 3RD PARTY DEVELOPERS:
     *
     * When you override the following methods in your child classes,
     * be sure to call parent::method *AFTER* your code, otherwise the
     * plugin events do NOT get triggered
     *
     * Example:
     * protected function onBeforeBind(){
     *       // Your code here
     *     return parent::onBeforeBind() && $your_result;
     * }
     *
     * Do not do it the other way around, e.g. return $your_result && parent::onBeforeBind()
     * Due to  PHP short-circuit boolean evaluation the parent::onBeforeBind()
     * will not be called if $your_result is false.
     *
     * @param object|array &$from The data to bind
     *
     * @return bool True on success
     */
    protected function onBeforeBind(&$from)
    {
        // Call the behaviors
        $result = $this->tableDispatcher->trigger('onBeforeBind', [&$this, &$from]);

        if (in_array(false, $result, true)) {
            // Behavior failed, return false
            return false;
        }

        if ($this->_trigger_events) {
            $name = XTF0FInflector::pluralize($this->getKeyName());

            $result = XTF0FPlatform::getInstance()->runPlugins('onBeforeBind'.ucfirst($name), [&$this, &$from]);

            if (in_array(false, $result, true)) {
                return false;
            } else {
                return true;
            }
        }

        return true;
    }

    /**
     * The event which runs after loading a record from the database
     *
     * @param bool &$result Did the load succeeded?
     *
     * @return void
     */
    protected function onAfterLoad(&$result)
    {
        // Call the behaviors
        $eventResult = $this->tableDispatcher->trigger('onAfterLoad', [&$this, &$result]);

        if (in_array(false, $eventResult, true)) {
            // Behavior failed, return false
            $result = false;

            return false;
        }

        if ($this->_trigger_events) {
            $name = XTF0FInflector::pluralize($this->getKeyName());

            XTF0FPlatform::getInstance()->runPlugins('onAfterLoad'.ucfirst($name), [&$this, &$result]);
        }

        return null;
    }

    /**
     * The event which runs before storing (saving) data to the database
     *
     * @param bool $updateNulls Should nulls be saved as nulls (true) or just skipped over (false)?
     *
     * @return bool True to allow saving
     */
    protected function onBeforeStore($updateNulls)
    {
        // Do we have a "Created" set of fields?
        $created_on = $this->getColumnAlias('created_on');
        $created_by = $this->getColumnAlias('created_by');
        $modified_on = $this->getColumnAlias('modified_on');
        $modified_by = $this->getColumnAlias('modified_by');
        $locked_on = $this->getColumnAlias('locked_on');
        $locked_by = $this->getColumnAlias('locked_by');
        $title = $this->getColumnAlias('title');
        $slug = $this->getColumnAlias('slug');

        $hasCreatedOn = in_array($created_on, $this->getKnownFields());
        $hasCreatedBy = in_array($created_by, $this->getKnownFields());

        if ($hasCreatedOn && $hasCreatedBy) {
            $hasModifiedOn = in_array($modified_on, $this->getKnownFields());
            $hasModifiedBy = in_array($modified_by, $this->getKnownFields());

            $nullDate = $this->_db->getNullDate();

            if (empty($this->$created_by) || ($this->$created_on == $nullDate) || empty($this->$created_on)) {
                $uid = XTF0FPlatform::getInstance()->getUser()->id;

                if ($uid) {
                    $this->$created_by = XTF0FPlatform::getInstance()->getUser()->id;
                }

                $date = XTF0FPlatform::getInstance()->getDate('now', null, false);

                $this->$created_on = method_exists($date, 'toSql') ? $date->toSql() : $date->toMySQL();
            } elseif ($hasModifiedOn && $hasModifiedBy) {
                $uid = XTF0FPlatform::getInstance()->getUser()->id;

                if ($uid) {
                    $this->$modified_by = XTF0FPlatform::getInstance()->getUser()->id;
                }

                $date = XTF0FPlatform::getInstance()->getDate('now', null, false);

                $this->$modified_on = method_exists($date, 'toSql') ? $date->toSql() : $date->toMySQL();
            }
        }

        // Do we have a set of title and slug fields?
        $hasTitle = in_array($title, $this->getKnownFields());
        $hasSlug = in_array($slug, $this->getKnownFields());

        if ($hasTitle && $hasSlug) {
            if (empty($this->$slug)) {
                // Create a slug from the title
                $this->$slug = XTF0FStringUtils::toSlug($this->$title);
            } else {
                // Filter the slug for invalid characters
                $this->$slug = XTF0FStringUtils::toSlug($this->$slug);
            }

            // Make sure we don't have a duplicate slug on this table
            $db = $this->getDbo();
            $query = $db->getQuery(true)
                ->select($db->qn($slug))
                ->from($this->_tbl)
                ->where($db->qn($slug).' = '.$db->q($this->$slug))
                ->where('NOT '.$db->qn($this->_tbl_key).' = '.$db->q($this->{$this->_tbl_key}));
            $db->setQuery($query);
            $existingItems = $db->loadAssocList();

            $count = 0;
            $newSlug = $this->$slug;

            while (!empty($existingItems)) {
                $count++;
                $newSlug = $this->$slug.'-'.$count;
                $query = $db->getQuery(true)
                    ->select($db->qn($slug))
                    ->from($this->_tbl)
                    ->where($db->qn($slug).' = '.$db->q($newSlug))
                    ->where('NOT '.$db->qn($this->_tbl_key).' = '.$db->q($this->{$this->_tbl_key}));
                $db->setQuery($query);
                $existingItems = $db->loadAssocList();
            }

            $this->$slug = $newSlug;
        }

        // Call the behaviors
        $result = $this->tableDispatcher->trigger('onBeforeStore', [&$this, $updateNulls]);

        if (in_array(false, $result, true)) {
            // Behavior failed, return false
            return false;
        }

        // Execute onBeforeStore<tablename> events in loaded plugins
        if ($this->_trigger_events) {
            $name = XTF0FInflector::pluralize($this->getKeyName());
            $result = XTF0FPlatform::getInstance()->runPlugins('onBeforeStore'.ucfirst($name), [&$this, $updateNulls]);

            if (in_array(false, $result, true)) {
                return false;
            } else {
                return true;
            }
        }

        return true;
    }

    /**
     * The event which runs after binding data to the class
     *
     * @param object|array &$src The data to bind
     *
     * @return bool True to allow binding without an error
     */
    protected function onAfterBind(&$src)
    {
        // Call the behaviors
        $options = [
            'component' 	=> $this->input->get('option'),
            'view'			=> $this->input->get('view'),
            'table_prefix'	=> $this->_tablePrefix,
        ];

        $result = $this->tableDispatcher->trigger('onAfterBind', [&$this, &$src, $options]);

        if (in_array(false, $result, true)) {
            // Behavior failed, return false
            return false;
        }

        if ($this->_trigger_events) {
            $name = XTF0FInflector::pluralize($this->getKeyName());

            $result = XTF0FPlatform::getInstance()->runPlugins('onAfterBind'.ucfirst($name), [&$this, &$src]);

            if (in_array(false, $result, true)) {
                return false;
            } else {
                return true;
            }
        }

        return true;
    }

    /**
     * The event which runs after storing (saving) data to the database
     *
     * @return bool True to allow saving without an error
     */
    protected function onAfterStore()
    {
        // Call the behaviors
        $result = $this->tableDispatcher->trigger('onAfterStore', [&$this]);

        if (in_array(false, $result, true)) {
            // Behavior failed, return false
            return false;
        }

        if ($this->_trigger_events) {
            $name = XTF0FInflector::pluralize($this->getKeyName());

            $result = XTF0FPlatform::getInstance()->runPlugins('onAfterStore'.ucfirst($name), [&$this]);

            if (in_array(false, $result, true)) {
                return false;
            } else {
                return true;
            }
        }

        return true;
    }

    /**
     * The event which runs before moving a record
     *
     * @param bool $updateNulls Should nulls be saved as nulls (true) or just skipped over (false)?
     *
     * @return bool True to allow moving
     */
    protected function onBeforeMove($updateNulls)
    {
        // Call the behaviors
        $result = $this->tableDispatcher->trigger('onBeforeMove', [&$this, $updateNulls]);

        if (in_array(false, $result, true)) {
            // Behavior failed, return false
            return false;
        }

        if ($this->_trigger_events) {
            $name = XTF0FInflector::pluralize($this->getKeyName());

            $result = XTF0FPlatform::getInstance()->runPlugins('onBeforeMove'.ucfirst($name), [&$this, $updateNulls]);

            if (in_array(false, $result, true)) {
                return false;
            } else {
                return true;
            }
        }

        return true;
    }

    /**
     * The event which runs after moving a record
     *
     * @return bool True to allow moving without an error
     */
    protected function onAfterMove()
    {
        // Call the behaviors
        $result = $this->tableDispatcher->trigger('onAfterMove', [&$this]);

        if (in_array(false, $result, true)) {
            // Behavior failed, return false
            return false;
        }

        if ($this->_trigger_events) {
            $name = XTF0FInflector::pluralize($this->getKeyName());

            $result = XTF0FPlatform::getInstance()->runPlugins('onAfterMove'.ucfirst($name), [&$this]);

            if (in_array(false, $result, true)) {
                return false;
            } else {
                return true;
            }
        }

        return true;
    }

    /**
     * The event which runs before reordering a table
     *
     * @param string $where The WHERE clause of the SQL query to run on reordering (record filter)
     *
     * @return bool True to allow reordering
     */
    protected function onBeforeReorder($where = '')
    {
        // Call the behaviors
        $result = $this->tableDispatcher->trigger('onBeforeReorder', [&$this, $where]);

        if (in_array(false, $result, true)) {
            // Behavior failed, return false
            return false;
        }

        if ($this->_trigger_events) {
            $name = XTF0FInflector::pluralize($this->getKeyName());

            $result = XTF0FPlatform::getInstance()->runPlugins('onBeforeReorder'.ucfirst($name), [&$this, $where]);

            if (in_array(false, $result, true)) {
                return false;
            } else {
                return true;
            }
        }

        return true;
    }

    /**
     * The event which runs after reordering a table
     *
     * @return bool True to allow the reordering to complete without an error
     */
    protected function onAfterReorder()
    {
        // Call the behaviors
        $result = $this->tableDispatcher->trigger('onAfterReorder', [&$this]);

        if (in_array(false, $result, true)) {
            // Behavior failed, return false
            return false;
        }

        if ($this->_trigger_events) {
            $name = XTF0FInflector::pluralize($this->getKeyName());

            $result = XTF0FPlatform::getInstance()->runPlugins('onAfterReorder'.ucfirst($name), [&$this]);

            if (in_array(false, $result, true)) {
                return false;
            } else {
                return true;
            }
        }

        return true;
    }

    /**
     * The event which runs before deleting a record
     *
     * @param int $oid The PK value of the record to delete
     *
     * @return bool True to allow the deletion
     */
    protected function onBeforeDelete($oid)
    {
        // Call the behaviors
        $result = $this->tableDispatcher->trigger('onBeforeDelete', [&$this, $oid]);

        if (in_array(false, $result, true)) {
            // Behavior failed, return false
            return false;
        }

        if ($this->_trigger_events) {
            $name = XTF0FInflector::pluralize($this->getKeyName());

            $result = XTF0FPlatform::getInstance()->runPlugins('onBeforeDelete'.ucfirst($name), [&$this, $oid]);

            if (in_array(false, $result, true)) {
                return false;
            } else {
                return true;
            }
        }

        return true;
    }

    /**
     * The event which runs after deleting a record
     *
     * @param int $oid The PK value of the record which was deleted
     *
     * @return bool True to allow the deletion without errors
     */
    protected function onAfterDelete($oid)
    {
        // Call the behaviors
        $result = $this->tableDispatcher->trigger('onAfterDelete', [&$this, $oid]);

        if (in_array(false, $result, true)) {
            // Behavior failed, return false
            return false;
        }

        if ($this->_trigger_events) {
            $name = XTF0FInflector::pluralize($this->getKeyName());

            $result = XTF0FPlatform::getInstance()->runPlugins('onAfterDelete'.ucfirst($name), [&$this, $oid]);

            if (in_array(false, $result, true)) {
                return false;
            } else {
                return true;
            }
        }

        return true;
    }

    /**
     * The event which runs before hitting a record
     *
     * @param int  $oid The PK value of the record to hit
     * @param bool $log Should we log the hit?
     *
     * @return bool True to allow the hit
     */
    protected function onBeforeHit($oid, $log)
    {
        // Call the behaviors
        $result = $this->tableDispatcher->trigger('onBeforeHit', [&$this, $oid, $log]);

        if (in_array(false, $result, true)) {
            // Behavior failed, return false
            return false;
        }

        if ($this->_trigger_events) {
            $name = XTF0FInflector::pluralize($this->getKeyName());

            $result = XTF0FPlatform::getInstance()->runPlugins('onBeforeHit'.ucfirst($name), [&$this, $oid, $log]);

            if (in_array(false, $result, true)) {
                return false;
            } else {
                return true;
            }
        }

        return true;
    }

    /**
     * The event which runs after hitting a record
     *
     * @param int $oid The PK value of the record which was hit
     *
     * @return bool True to allow the hitting without errors
     */
    protected function onAfterHit($oid)
    {
        // Call the behaviors
        $result = $this->tableDispatcher->trigger('onAfterHit', [&$this, $oid]);

        if (in_array(false, $result, true)) {
            // Behavior failed, return false
            return false;
        }

        if ($this->_trigger_events) {
            $name = XTF0FInflector::pluralize($this->getKeyName());

            $result = XTF0FPlatform::getInstance()->runPlugins('onAfterHit'.ucfirst($name), [&$this, $oid]);

            if (in_array(false, $result, true)) {
                return false;
            } else {
                return true;
            }
        }

        return true;
    }

    /**
     * The even which runs before copying a record
     *
     * @param int $oid The PK value of the record being copied
     *
     * @return bool True to allow the copy to take place
     */
    protected function onBeforeCopy($oid)
    {
        // Call the behaviors
        $result = $this->tableDispatcher->trigger('onBeforeCopy', [&$this, $oid]);

        if (in_array(false, $result, true)) {
            // Behavior failed, return false
            return false;
        }

        if ($this->_trigger_events) {
            $name = XTF0FInflector::pluralize($this->getKeyName());

            $result = XTF0FPlatform::getInstance()->runPlugins('onBeforeCopy'.ucfirst($name), [&$this, $oid]);

            if (in_array(false, $result, true)) {
                return false;
            } else {
                return true;
            }
        }

        return true;
    }

    /**
     * The even which runs after copying a record
     *
     * @param int $oid The PK value of the record which was copied (not the new one)
     *
     * @return bool True to allow the copy without errors
     */
    protected function onAfterCopy($oid)
    {
        // Call the behaviors
        $result = $this->tableDispatcher->trigger('onAfterCopy', [&$this, $oid]);

        if (in_array(false, $result, true)) {
            // Behavior failed, return false
            return false;
        }

        if ($this->_trigger_events) {
            $name = XTF0FInflector::pluralize($this->getKeyName());

            $result = XTF0FPlatform::getInstance()->runPlugins('onAfterCopy'.ucfirst($name), [&$this, $oid]);

            if (in_array(false, $result, true)) {
                return false;
            } else {
                return true;
            }
        }

        return true;
    }

    /**
     * The event which runs before a record is (un)published
     *
     * @param int|array &$cid    The PK IDs of the records being (un)published
     * @param int       $publish 1 to publish, 0 to unpublish
     *
     * @return bool True to allow the (un)publish to proceed
     */
    protected function onBeforePublish(&$cid, $publish)
    {
        // Call the behaviors
        $result = $this->tableDispatcher->trigger('onBeforePublish', [&$this, &$cid, $publish]);

        if (in_array(false, $result, true)) {
            // Behavior failed, return false
            return false;
        }

        if ($this->_trigger_events) {
            $name = XTF0FInflector::pluralize($this->getKeyName());

            $result = XTF0FPlatform::getInstance()->runPlugins('onBeforePublish'.ucfirst($name), [&$this, &$cid, $publish]);

            if (in_array(false, $result, true)) {
                return false;
            } else {
                return true;
            }
        }

        return true;
    }

    /**
     * The event which runs after the object is reset to its default values.
     *
     * @return bool True to allow the reset to complete without errors
     */
    protected function onAfterReset()
    {
        // Call the behaviors
        $result = $this->tableDispatcher->trigger('onAfterReset', [&$this]);

        if (in_array(false, $result, true)) {
            // Behavior failed, return false
            return false;
        }

        if ($this->_trigger_events) {
            $name = XTF0FInflector::pluralize($this->getKeyName());

            $result = XTF0FPlatform::getInstance()->runPlugins('onAfterReset'.ucfirst($name), [&$this]);

            if (in_array(false, $result, true)) {
                return false;
            } else {
                return true;
            }
        }

        return true;
    }

    /**
     * The even which runs before the object is reset to its default values.
     *
     * @return bool True to allow the reset to complete
     */
    protected function onBeforeReset()
    {
        // Call the behaviors
        $result = $this->tableDispatcher->trigger('onBeforeReset', [&$this]);

        if (in_array(false, $result, true)) {
            // Behavior failed, return false
            return false;
        }

        if ($this->_trigger_events) {
            $name = XTF0FInflector::pluralize($this->getKeyName());

            $result = XTF0FPlatform::getInstance()->runPlugins('onBeforeReset'.ucfirst($name), [&$this]);

            if (in_array(false, $result, true)) {
                return false;
            } else {
                return true;
            }
        }

        return true;
    }

    /**
     * Loads the asset table related to this table.
     * This will help tests, too, since we can mock this function.
     *
     * @return bool|JTableAsset False on failure, otherwise JTableAsset
     */
    protected function getAsset()
    {
        $name = $this->_getAssetName();

        // Do NOT touch JTable here -- we are loading the core asset table which is a JTable, not a XTF0FTable
        $asset = JTable::getInstance('Asset');

        if (!$asset->loadByName($name)) {
            return false;
        }

        return $asset;
    }

    /**
     * Method to lock the database table for writing.
     *
     * @return bool true on success
     *
     * @throws RuntimeException
     */
    protected function _lock()
    {
        $this->_db->lockTable($this->_tbl);
        $this->_locked = true;

        return true;
    }

    /**
     * Method to unlock the database table for writing.
     *
     * @return bool true on success
     */
    protected function _unlock()
    {
        $this->_db->unlockTables();
        $this->_locked = false;

        return true;
    }

    /**
     * Utility methods that fetches the column name for the field.
     * If it does not exists, returns a "null" string
     *
     * @param string $alias The alias for the column
     * @param string $null  What to return if no column exists
     *
     * @return string The column name
     */
    protected function getUcmCoreAlias($alias, $null = 'null')
    {
        $alias = $this->getColumnAlias($alias);

        if (in_array($alias, $this->getKnownFields())) {
            return $alias;
        }

        return $null;
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

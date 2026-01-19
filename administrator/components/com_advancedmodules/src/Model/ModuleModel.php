<?php
/**
 * @package         Advanced Module Manager
 * @version         10.4.8
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

namespace RegularLabs\Component\AdvancedModules\Administrator\Model;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Object\CMSObject as JCMSObject;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Table\Table;
use Joomla\Database\ParameterType;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\Path;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\Utilities\ArrayHelper as JArray;
use RegularLabs\Component\AdvancedModules\Administrator\Helper\ModulesHelper;
use RegularLabs\Component\Conditions\Administrator\Model\ItemModel as Condition;
use RegularLabs\Library\Color as RL_Color;
use RegularLabs\Library\DB as RL_DB;
use RegularLabs\Library\Input as RL_Input;
use RegularLabs\Library\Language as RL_Language;
use RegularLabs\Library\Parameters as RL_Parameters;
use RegularLabs\Library\SimpleCategory as RL_SimpleCategory;
use RuntimeException;

class ModuleModel extends AdminModel
{
    /**
     * The type alias for this content type.
     *
     * @var      string
     */
    public $typeAlias = 'com_advancedmodules.module';
    /**
     * Allowed batch commands
     *
     * @var array
     */
    protected $batch_commands = [
        'assetgroup_id' => 'batchAccess',
        'language_id'   => 'batchLanguage',
        'category'      => 'batchCategory',
        'color'         => 'batchColor',
    ];
    /**
     * Batch copy/move command. If set to false,
     * the batch copy/move command is not supported
     *
     * @var string
     */
    protected $batch_copymove = 'position_id';
    protected $client;
    /**
     * @var    string  The help screen key for the module.
     */
    protected $helpKey = '';
    /**
     * @var    string  The help screen base URL for the module.
     */
    protected $helpURL;
    /**
     * @var    string  The prefix to use with controller messages.
     */
    protected $text_prefix = 'COM_MODULES';

    /**
     * @param array $config An optional associative array of configuration settings.
     */
    public function __construct($config = [])
    {
        RL_Language::load('com_modules', JPATH_ADMINISTRATOR);

        $config = [
            'event_after_delete'  => 'onExtensionAfterDelete',
            'event_after_save'    => 'onExtensionAfterSave',
            'event_before_delete' => 'onExtensionBeforeDelete',
            'event_before_save'   => 'onExtensionBeforeSave',
            'events_map'          => [
                'save'   => 'extension',
                'delete' => 'extension',
            ],
            ...$config,
        ];

        parent::__construct($config);
    }

    /**
     * Method to delete rows.
     *
     * @param array  &$pks An array of item ids.
     *
     * @return  boolean  Returns true on success, false on failure.
     *
     * @throws  Exception
     */
    public function delete(&$pks)
    {
        $app     = Factory::getApplication();
        $pks     = (array) $pks;
        $user    = Factory::getUser();
        $table   = $this->getTable();
        $context = $this->option . '.' . $this->name;

        // Include the plugins for the on delete events.
        PluginHelper::importPlugin($this->events_map['delete']);

        // Iterate the items to delete each one.
        foreach ($pks as $pk)
        {
            if ($table->load($pk))
            {
                // Access checks.
                if ( ! $user->authorise('core.delete', 'com_modules.module.' . (int) $pk) || $table->published != -2)
                {
                    Factory::getApplication()->enqueueMessage(Text::_('JERROR_CORE_DELETE_NOT_PERMITTED'), 'error');

                    return;
                }

                // Trigger the before delete event.
                $result = $app->triggerEvent($this->event_before_delete, [$context, $table]);

                if (in_array(false, $result, true) || ! $table->delete($pk))
                {
                    throw new Exception($table->getError());
                }
                else
                {
                    // Delete the menu assignments
                    $pk    = (int) $pk;
                    $db    = RL_DB::get();
                    $query = $db->getQuery(true)
                        ->delete($db->quoteName('#__modules_menu'))
                        ->where($db->quoteName('moduleid') . ' = :moduleid')
                        ->bind(':moduleid', $pk, ParameterType::INTEGER);
                    $db->setQuery($query);
                    $db->execute();

                    // Trigger the after delete event.
                    $app->triggerEvent($this->event_after_delete, [$context, $table]);
                }

                // Clear module cache
                parent::cleanCache($table->module);
            }
            else
            {
                throw new Exception($table->getError());
            }
        }

        $this->removeConditions($pks);

        // Clear modules cache
        $this->cleanCache();

        return true;
    }

    /**
     * Method to duplicate modules.
     *
     * @param array  &$pks An array of primary key IDs.
     *
     * @return  boolean  Boolean true on success
     *
     * @throws  Exception
     */
    public function duplicate(&$pks)
    {
        $user = Factory::getUser();
        $db   = RL_DB::get();

        // Access checks.
        if ( ! $user->authorise('core.create', 'com_modules'))
        {
            throw new Exception(Text::_('JERROR_CORE_CREATE_NOT_PERMITTED'));
        }

        $table = $this->getTable();

        foreach ($pks as $pk)
        {
            $pk = (int) $pk;

            if ( ! $table->load($pk, true))
            {
                throw new Exception($table->getError());
            }

            // Reset the id to create a new record.
            $table->id = 0;

            // Alter the title.
            $m = null;

            if (preg_match('#\((\d+)\)$#', $table->title, $m))
            {
                $table->title = preg_replace('#\(\d+\)$#', '(' . ($m[1] + 1) . ')', $table->title);
            }

            $data         = $this->generateNewTitle(0, $table->title, $table->position);
            $table->title = $data[0];

            // Unpublish duplicate module
            $table->published = 0;

            if ( ! $table->check() || ! $table->store())
            {
                throw new Exception($table->getError());
            }

            $extra_data = (array) $this->getExtraParams($pk);

            if ( ! $this->saveExtraParams($table->id, $extra_data))
            {
                return false;
            }

            $this->copyConditions($pk, $table->id);
        }

        // Clear modules cache
        $this->cleanCache();

        return true;
    }

    /**
     * Method to get the client object
     *
     * @return  object|array|void  Object describing the client, array containing all the clients or void if $id not known
     */
    public function getClient()
    {
        if (is_null($this->client))
        {
            $client_id    = $this->getState('client_id', $this->getState('item.client_id', 0));
            $this->client = ApplicationHelper::getClientInfo($client_id);
        }

        return $this->client;
    }

    /**
     * @return  object
     */
    public function getExtraParams($id)
    {
        $table = $this->getTable('AdvancedModule', 'RegularLabs\Component\AdvancedModules\Administrator\Table\\');

        $table->load($id);

        $properties = $table->getProperties(1);
        $item       = JArray::toObject($properties, JCMSObject::class);

        if (empty($item))
        {
            return (object) [];
        }

        $data           = (object) json_decode(($item->params ?? '') ?: '{}');
        $data->category = $item->category ?? '';
        $data->color    = $item->color ?? '';

        return $data;
    }

    /**
     * Method to get the record form.
     *
     * @param array   $data     Data for the form.
     * @param boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  Form|bool  A Form object on success, false on failure
     */
    public function getForm($data = [], $loadData = true)
    {
        // The folder and element vars are passed when saving the form.
        if (empty($data))
        {
            $item     = $this->getItem();
            $clientId = $item->client_id;
            $module   = $item->module;
            $id       = $item->id;
        }
        else
        {
            $clientId = ArrayHelper::getValue($data, 'client_id');
            $module   = ArrayHelper::getValue($data, 'module');
            $id       = ArrayHelper::getValue($data, 'id');
        }

        // Add the default fields directory
        $baseFolder = $clientId ? JPATH_ADMINISTRATOR : JPATH_SITE;
        Form::addFieldPath($baseFolder . '/modules/' . $module . '/field');

        // These variables are used to add data from the plugin XML files.
        $this->setState('item.client_id', $clientId);
        $this->setState('item.module', $module);

        // Get the form.
        if ($clientId == 1)
        {
            $form = $this->loadForm('com_advancedmodules.module.admin', 'moduleadmin', [
                'control'   => 'jform',
                'load_data' => $loadData,
            ], true);

            // Display language field to filter admin custom menus per language
            if ( ! ModuleHelper::isAdminMultilang())
            {
                $form->setFieldAttribute('language', 'type', 'hidden');
            }
        }
        else
        {
            $form = $this->loadForm('com_advancedmodules.module', 'module', [
                'control'   => 'jform',
                'load_data' => $loadData,
            ], true);
        }

        if (empty($form))
        {
            return false;
        }

        $user = Factory::getUser();

        /**
         * Check for existing module
         * Modify the form based on Edit State access controls.
         */
        if (
            $id != 0 && ( ! $user->authorise('core.edit.state', 'com_modules.module.' . (int) $id))
            || ($id == 0 && ! $user->authorise('core.edit.state', 'com_modules'))
        )
        {
            // Disable fields for display.
            $form->setFieldAttribute('ordering', 'disabled', 'true');
            $form->setFieldAttribute('published', 'disabled', 'true');
            $form->setFieldAttribute('publish_up', 'disabled', 'true');
            $form->setFieldAttribute('publish_down', 'disabled', 'true');

            // Disable fields while saving.
            // The controller has already verified this is a record you can edit.
            $form->setFieldAttribute('ordering', 'filter', 'unset');
            $form->setFieldAttribute('published', 'filter', 'unset');
            $form->setFieldAttribute('publish_up', 'filter', 'unset');
            $form->setFieldAttribute('publish_down', 'filter', 'unset');
        }

        return $form;
    }

    /**
     * Get the necessary data to load an item help screen.
     *
     * @return  object  An object with key, url, and local properties for loading the item help screen.
     */
    public function getHelp()
    {
        return (object) ['key' => $this->helpKey, 'url' => $this->helpURL];
    }

    /**
     * Method to get a single record.
     *
     * @param integer $pk The id of the primary key.
     *
     * @return  mixed  Object on success, false on failure.
     */
    public function getItem($pk = null)
    {
        $pk = ( ! empty($pk)) ? (int) $pk : (int) $this->getState('module.id');
        $db = RL_DB::get();

        if ( ! isset($this->_cache[$pk]))
        {
            // Get a row instance.
            $table = $this->getTable();

            // Attempt to load the row.
            $return = $table->load($pk);

            // Check for a table object error.
            $error = $table->getError();

            if ($return === false && $error)
            {
                $this->setError($error);

                return false;
            }

            // Check if we are creating a new extension.
            if (empty($pk))
            {
                $extensionId = (int) $this->getState('extension.id');

                if ($extensionId)
                {
                    $query = $db->getQuery(true)
                        ->select($db->quoteName(['element', 'client_id']))
                        ->from($db->quoteName('#__extensions'))
                        ->where($db->quoteName('extension_id') . ' = :extensionid')
                        ->where($db->quoteName('type') . ' = ' . $db->quote('module'))
                        ->bind(':extensionid', $extensionId, ParameterType::INTEGER);
                    $db->setQuery($query);

                    try
                    {
                        $extension = $db->loadObject();
                    }
                    catch (RuntimeException $e)
                    {
                        $this->setError($e->getMessage());

                        return false;
                    }

                    if (empty($extension))
                    {
                        $this->setError('COM_MODULES_ERROR_CANNOT_FIND_MODULE');

                        return false;
                    }

                    // Extension found, prime some module values.
                    $table->module    = $extension->element;
                    $table->client_id = $extension->client_id;
                }
                else
                {
                    Factory::getApplication()->redirect(Route::_('index.php?option=com_advancedmodules&view=modules', false));

                    return false;
                }
            }

            // Convert to the \Joomla\CMS\Object\CMSObject before adding other data.
            $properties        = $table->getProperties(1);
            $this->_cache[$pk] = ArrayHelper::toObject($properties, CMSObject::class);

            // Convert the params field to an array.
            $registry                  = new Registry($table->params);
            $this->_cache[$pk]->params = $registry->toArray();

            $this->_cache[$pk]->extra = (array) $this->getExtraParams($pk);

            // Get the module XML.
            $client = ApplicationHelper::getClientInfo($table->client_id);
            $path   = Path::clean($client->path . '/modules/' . $table->module . '/' . $table->module . '.xml');

            if (file_exists($path))
            {
                $this->_cache[$pk]->xml = simplexml_load_file($path);
            }
            else
            {
                $this->_cache[$pk]->xml = null;
            }
        }

        return $this->_cache[$pk];
    }

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param string $type   The table type to instantiate
     * @param string $prefix A prefix for the table class name. Optional.
     * @param array  $config Configuration array for model. Optional.
     *
     * @return  Table  A database object
     */
    public function getTable($type = 'Module', $prefix = 'JTable', $config = [])
    {
        return Table::getInstance($type, $prefix, $config);
    }

    /**
     * Method to save the form data.
     *
     * @param array $data The form data.
     *
     * @return  boolean  True on success.
     */
    public function save($data)
    {
        $task    = RL_Input::getCmd('task');
        $table   = $this->getTable();
        $pk      = ( ! empty($data['id'])) ? $data['id'] : (int) $this->getState('module.id');
        $isNew   = true;
        $context = $this->option . '.' . $this->name;

        // Include the plugins for the save event.
        PluginHelper::importPlugin($this->events_map['save']);

        // Load the row if saving an existing record.
        if ($pk > 0)
        {
            $table->load($pk);
            $isNew = false;
        }

        // Alter the title and published state for Save as Copy
        if ($task == 'save2copy')
        {
            $orig_table = clone $this->getTable();
            $orig_table->load(RL_Input::getInt('id'));
            $data['published'] = 0;

            if ($data['title'] == $orig_table->title)
            {
                $data['title'] = StringHelper::increment($data['title']);
            }
        }

        // Bind the data.
        if ( ! $table->bind($data))
        {
            $this->setError($table->getError());

            return false;
        }

        // Prepare the row for saving
        $this->prepareTable($table);

        // Check the data.
        if ( ! $table->check())
        {
            $this->setError($table->getError());

            return false;
        }

        // Trigger the before save event.
        $result = Factory::getApplication()->triggerEvent($this->event_before_save, [$context, &$table, $isNew]);

        if (in_array(false, $result, true))
        {
            $this->setError($table->getError());

            return false;
        }

        // Store the data.
        if ( ! $table->store())
        {
            $this->setError($table->getError());

            return false;
        }

        $table->id = (int) $table->id;

        // Delete old module to menu item associations
        $db    = RL_DB::get();
        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__modules_menu'))
            ->where($db->quoteName('moduleid') . ' = :moduleid')
            ->bind(':moduleid', $table->id, ParameterType::INTEGER);
        $db->setQuery($query);

        try
        {
            $db->execute();
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());

            return false;
        }

        // Trigger the after save event.
        Factory::getApplication()->triggerEvent($this->event_after_save, [$context, &$table, $isNew]);

        // Compute the extension id of this module in case the controller wants it.
        $query->clear()
            ->select($db->quoteName('extension_id'))
            ->from($db->quoteName('#__extensions', 'e'))
            ->join(
                'LEFT',
                $db->quoteName('#__modules', 'm') . ' ON ' . $db->quoteName('e.client_id') . ' = ' . (int) $table->client_id .
                ' AND ' . $db->quoteName('e.element') . ' = ' . $db->quoteName('m.module')
            )
            ->where($db->quoteName('m.id') . ' = :id')
            ->bind(':id', $table->id, ParameterType::INTEGER);
        $db->setQuery($query);

        try
        {
            $extensionId = $db->loadResult();
        }
        catch (RuntimeException $e)
        {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');

            return false;
        }

        $query->clear()
            ->insert($db->quoteName('#__modules_menu'))
            ->columns($db->quoteName(['moduleid', 'menuid']))
            ->values($table->id . ',0');

        $db->setQuery($query);

        try
        {
            $db->execute();
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());

            return false;
        }

        if ( ! $this->saveExtraParams($table->id, $data['extra'] ?? []))
        {
            return false;
        }

        if ($task == 'save2copy')
        {
            $this->copyConditions($orig_table->id, $table->id);
        }

        $this->setState('module.extension_id', $extensionId);
        $this->setState('module.id', $table->id);

        // Clear modules cache
        $this->cleanCache();

        // Clean module cache
        parent::cleanCache($table->module);

        return true;
    }

    public function saveExtraParams($id, $extra)
    {
        $table = $this->getTable('AdvancedModule', 'RegularLabs\Component\AdvancedModules\Administrator\Table\\');

        $category = $extra['category'] ?? '';
        unset($extra['category']);

        $color = $extra['color'] ?? '';
        $color = substr($color, 0, 8);
        unset($extra['color']);

        $extra = json_encode($extra);

        $data = [
            'module_id' => $id,
            'category'  => $category,
            'color'     => $color,
            'params'    => $extra,
        ];

        // Bind the data.
        if ( ! $table->bind($data))
        {
            $this->setError($table->getError());

            return false;
        }

        // Check the data.
        if ( ! $table->check())
        {
            $this->setError($table->getError());

            return false;
        }

        if ( ! $table->store())
        {
            $this->setError($table->getError());

            return false;
        }

        return true;
    }

    /**
     * Loads ContentHelper for filters before validating data.
     *
     * @param object $form  The form to validate against.
     * @param array  $data  The data to validate.
     * @param string $group The name of the group(defaults to null).
     *
     * @return  mixed  Array of filtered data if valid, false otherwise.
     */
    public function validate($form, $data, $group = null)
    {
        if ( ! Factory::getUser()->authorise('core.admin', 'com_modules'))
        {
            if (isset($data['rules']))
            {
                unset($data['rules']);
            }
        }

        return parent::validate($form, $data, $group);
    }

    /**
     * Batch category changes for a group of rows.
     *
     * @param string $value    The new value matching a language.
     * @param array  $pks      An array of row IDs.
     * @param array  $contexts An array of item contexts.
     *
     * @return  boolean  True if successful, false otherwise and internal error is set.
     */
    protected function batchCategory($value, $pks, $contexts)
    {
        if ($value === '---')
        {
            $value = '';
        }

        // Initialize re-usable member properties, and re-usable local variables
        $this->initBatch();

        foreach ($pks as $pk)
        {
            if ( ! $this->user->authorise('core.edit', $contexts[$pk]))
            {
                $this->setError(Text::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));

                return false;
            }

            RL_SimpleCategory::save('advancedmodules', $pk, $value, 'module_id');
        }

        // Clean the cache
        $this->cleanCache();

        return true;
    }

    /**
     * Batch color changes for a group of rows.
     *
     * @param string $value    The new value matching a language.
     * @param array  $pks      An array of row IDs.
     * @param array  $contexts An array of item contexts.
     *
     * @return  boolean  True if successful, false otherwise and internal error is set.
     */
    protected function batchColor($value, $pks, $contexts)
    {
        // Initialize re-usable member properties, and re-usable local variables
        $this->initBatch();

        foreach ($pks as $pk)
        {
            if ( ! $this->user->authorise('core.edit', $contexts[$pk]))
            {
                $this->setError(Text::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));

                return false;
            }

            RL_Color::save('advancedmodules', $pk, $value, 'module_id');
        }

        // Clean the cache
        $this->cleanCache();

        return true;
    }

    /**
     * Batch move modules to a new position or current.
     *
     * @param integer $value    The new value matching a module position.
     * @param array   $pks      An array of row IDs.
     * @param array   $contexts An array of item contexts.
     *
     * @return  boolean  True if successful, false otherwise and internal error is set.
     */
    protected function batchMove($value, $pks, $contexts)
    {
        // Set the variables
        $user  = Factory::getUser();
        $table = $this->getTable();

        foreach ($pks as $pk)
        {
            if ( ! $user->authorise('core.edit', 'com_modules'))
            {
                $this->setError(Text::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));

                return false;
            }

            $table->reset();
            $table->load($pk);

            // Set the new position
            if ($value == 'noposition')
            {
                $position = '';
            }
            elseif ($value == 'nochange')
            {
                $position = $table->position;
            }
            else
            {
                $position = $value;
            }

            $table->position = $position;

            if ( ! $table->store())
            {
                $this->setError($table->getError());

                return false;
            }
        }

        // Clean the cache
        $this->cleanCache();

        return true;
    }

    /**
     * Method to test whether a record can have its state edited.
     *
     * @param object $record A record object.
     *
     * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
     */
    protected function canEditState($record)
    {
        // Check for existing module.
        if ( ! empty($record->id))
        {
            return Factory::getUser()->authorise('core.edit.state', 'com_modules.module.' . (int) $record->id);
        }

        // Default to component settings if module not known.
        return parent::canEditState($record);
    }

    /**
     * Custom clean cache method for different clients
     *
     * @param string  $group    The name of the plugin group to import (defaults to null).
     * @param integer $clientId @deprecated   5.0   No longer used.
     *
     * @return  void
     */
    protected function cleanCache($group = null, $clientId = 0)
    {
        parent::cleanCache('com_modules');
        parent::cleanCache('com_advancedmodules');
    }

    /**
     * Method to change the title.
     *
     * @param integer $categoryId The id of the category. Not used here.
     * @param string  $title      The title.
     * @param string  $position   The position.
     *
     * @return  array  Contains the modified title.
     */
    protected function generateNewTitle($categoryId, $title, $position)
    {
        // Alter the title & alias
        $table = $this->getTable();

        while ($table->load(['position' => $position, 'title' => $title]))
        {
            $title = StringHelper::increment($title);
        }

        return [$title];
    }

    /**
     * A protected method to get a set of ordering conditions.
     *
     * @param object $table A record object.
     *
     * @return  array  An array of conditions to add to ordering queries.
     */
    protected function getReorderConditions($table)
    {
        $db = $this->getDatabase();

        return [
            $db->quoteName('client_id') . ' = ' . (int) $table->client_id,
            $db->quoteName('position') . ' = ' . $db->quote($table->position),
        ];
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     */
    protected function loadFormData()
    {
        $app = Factory::getApplication();

        // Check the session for previously entered form data.
        $data = $app->getUserState('com_advancedmodules.edit.module.data', []);

        if (empty($data))
        {
            $data = $this->getItem();

            // Pre-select some filters (Status, Module Position, Language, Access Level) in edit form if those have been selected in Module Manager
            if ( ! $data->id)
            {
                $default_state = RL_Parameters::getComponent('advancedmodules')->default_state;

                $clientId = RL_Input::getInt('client_id', 0);
                $filters  = (array) $app->getUserState('com_advancedmodules.modules.' . $clientId . '.filter');
                $data->set('published', RL_Input::getInt('published', ((isset($filters['state']) && $filters['state'] !== '') ? $filters['state'] : $default_state)));
                $data->set('position', RL_Input::getInt('position', (! empty($filters['position']) ? $filters['position'] : null)));
                $data->set('language', RL_Input::getString('language', (! empty($filters['language']) ? $filters['language'] : null)));
                $data->set('access', RL_Input::getInt('access', (! empty($filters['access']) ? $filters['access'] : $app->get('access'))));
            }

            // Avoid to delete params of a second module opened in a new browser tab while new one is not saved yet.
            if (empty($data->params))
            {
                // This allows us to inject parameter settings into a new module.
                $params = $app->getUserState('com_advancedmodules.add.module.params');

                if (is_array($params))
                {
                    $data->set('params', $params);
                }
            }
        }

        $this->preprocessData('com_advancedmodules.module', $data);

        return $data;
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @return  void
     */
    protected function populateState()
    {
        $app = Factory::getApplication();

        // Load the User state.
        $pk = RL_Input::getInt('id');

        if ( ! $pk)
        {
            $extensionId = (int) $app->getUserState('com_advancedmodules.add.module.extension_id');

            if ($extensionId)
            {
                $this->setState('extension.id', $extensionId);
            }
        }

        $this->setState('module.id', $pk);

        // Load the parameters.
        $params = ComponentHelper::getParams('com_modules');
        $this->setState('params', $params);
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @param Table $table The database object
     *
     * @return  void
     */
    protected function prepareTable($table)
    {
        $table->title    = htmlspecialchars_decode($table->title, ENT_QUOTES);
        $table->position = trim($table->position);
    }

    /**
     * Method to preprocess the form
     *
     * @param Form   $form  A form object.
     * @param mixed  $data  The data expected for the form.
     * @param string $group The name of the plugin group to import (defaults to "content").
     *
     * @return  void
     *
     * @throws  Exception if there is an error loading the form.
     */
    protected function preprocessForm(Form $form, $data, $group = 'content')
    {
        $this->loadModuleLanguage();
        $this->addModuleFields($form);
        $this->addAdvancedFields($form);
        $this->addCustomChromeFieldsGlobal($form);
        $this->addCustomChromeFieldsTemplates($form);

        // Trigger the default form events.
        parent::preprocessForm($form, $data, $group);
    }

    private function addAdvancedFields(&$form)
    {
        // Load the default advanced params
        Form::addFormPath(JPATH_ADMINISTRATOR . '/components/com_advancedmodules/models/forms');
        $form->loadFile('extra', false);
        $form->loadFile('advanced', false);
    }

    private function addCustomChromeFieldsByTemplate(&$form, $template)
    {
        $client = $this->getClient();

        $chromePath = $client->path . '/templates/' . $template->element . '/html/layouts/chromes';

        // Skip if there is no chrome folder in that template.
        if ( ! is_dir($chromePath))
        {
            return;
        }

        $chromeFormFiles = Folder::files($chromePath, '.*\.xml');

        if ( ! $chromeFormFiles)
        {
            return;
        }

        Form::addFormPath($chromePath);

        foreach ($chromeFormFiles as $formFile)
        {
            $form->loadFile(basename($formFile, '.xml'), false);
        }
    }

    private function addCustomChromeFieldsGlobal(&$form)
    {
        // Load chrome specific params for global files
        $chromePath      = JPATH_SITE . '/layouts/chromes';
        $chromeFormFiles = Folder::files($chromePath, '.*\.xml');

        if ( ! $chromeFormFiles)
        {
            return;
        }

        Form::addFormPath($chromePath);

        foreach ($chromeFormFiles as $formFile)
        {
            $form->loadFile(basename($formFile, '.xml'), false);
        }
    }

    private function addCustomChromeFieldsTemplates(&$form)
    {
        $clientId = $this->getState('item.client_id');

        // Load chrome specific params for template files
        $templates = ModulesHelper::getTemplates($clientId);

        foreach ($templates as $template)
        {
            $this->addCustomChromeFieldsByTemplate($form, $template);
        }
    }

    private function addModuleFields(&$form)
    {
        $module = $this->getState('item.module');
        $client = $this->getClient();

        $formFile = Path::clean($client->path . '/modules/' . $module . '/' . $module . '.xml');

        if ( ! file_exists($formFile))
        {
            return;
        }

        // Get the module form.
        if ( ! $form->loadFile($formFile, false, '//config'))
        {
            throw new Exception(Text::_('JERROR_LOADFILE_FAILED'));
        }

        $this->getHelpData($formFile);
    }

    private function copyConditions($from_id, $to_id)
    {
    }

    private function getHelpData($formFile)
    {
        // Attempt to load the xml file.
        $xml = simplexml_load_file($formFile);

        if ( ! $xml)
        {
            throw new Exception(Text::_('JERROR_LOADFILE_FAILED'));
        }

        // Get the help data from the XML file if present.
        $help = $xml->xpath('/extension/help');

        if (empty($help))
        {
            return;
        }

        $helpKey = trim((string) $help[0]['key']);
        $helpURL = trim((string) $help[0]['url']);

        $this->helpKey = $helpKey ?: $this->helpKey;
        $this->helpURL = $helpURL ?: $this->helpURL;
    }

    private function loadModuleLanguage()
    {
        $module = $this->getState('item.module');
        $client = $this->getClient();

        $lang = Factory::getApplication()->getLanguage();

        // Load the core and/or local language file(s).
        $lang->load($module, $client->path)
        || $lang->load($module, $client->path . '/modules/' . $module);
    }

    private function removeConditions($ids)
    {
    }
}

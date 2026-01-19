<?php
/**
 * @package         Conditions
 * @version         25.11.2254
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

namespace RegularLabs\Component\Conditions\Administrator\Model;

use Exception;
use Joomla\CMS\Event\AbstractEvent;
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Form\Form as JForm;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\AdminModel as JAdminModel;
use Joomla\CMS\Router\Route as JRoute;
use Joomla\Event\DispatcherInterface as JDispatcherInterface;
use Joomla\Utilities\ArrayHelper as JArray;
use RegularLabs\Component\Conditions\Administrator\Helper\Cache;
use RegularLabs\Component\Conditions\Administrator\Helper\Helper;
use RegularLabs\Library\Alias as RL_Alias;
use RegularLabs\Library\ArrayHelper as RL_Array;
use RegularLabs\Library\DB as RL_DB;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Input as RL_Input;
use RegularLabs\Library\ObjectHelper as RL_Object;
use RegularLabs\Library\Parameters as RL_Parameters;
use RegularLabs\Library\StringHelper as RL_String;
use RegularLabs\Library\User as RL_User;

defined('_JEXEC') or die;

class ItemModel extends JAdminModel
{
    protected $name = 'condition';
    /**
     * @var        string    The prefix to use with controller messages.
     */
    protected $text_prefix = 'RL';
    /* @var GroupModel $group_model */
    private $group_model;

    /**
     * @param array                $config      An array of configuration options (name, state, dbo, table_path, ignore_request).
     * @param MVCFactoryInterface  $factory     The factory.
     * @param FormFactoryInterface $formFactory The form factory.
     *
     * @throws  Exception
     */
    public function __construct(
        $config = [],
        ?MVCFactoryInterface $factory = null,
        ?FormFactoryInterface $formFactory = null
    )
    {
        parent::__construct($config, $factory, $formFactory);

        $this->config = RL_Parameters::getComponent('conditions');

        $this->group_model = JFactory::getApplication()->bootComponent('com_conditions')
            ->getMVCFactory()->createModel('Group', 'Administrator', ['ignore_request' => true]);
    }

    public static function copyMapping($extension, $from_id, $to_id)
    {
        if ( ! $from_id || ! $to_id)
        {
            return;
        }

        $mapping = self::getMappingByExtensionItem($extension, $from_id);

        if ( ! $mapping)
        {
            return;
        }

        self::map(
            $mapping->condition_id,
            $extension,
            $to_id,
            $mapping->table,
            $mapping->name_column
        );
    }

    public static function getIdByExtensionItem($extension, $item_id)
    {
        if ( ! $extension || ! $item_id)
        {
            return null;
        }

        $cache = new Cache;

        if ($cache->exists())
        {
            return $cache->get();
        }

        $condition_ids = self::getAllConditionIdsByExtension($extension);

        return $condition_ids[$item_id] ?? null;
    }

    public static function getIdByMixed($condition)
    {
        if ( ! $condition)
        {
            return null;
        }

        $cache = new Cache;

        if ($cache->exists())
        {
            return $cache->get();
        }

        if (is_numeric($condition))
        {
            return $cache->set((int) $condition);
        }

        $db = RL_DB::get();

        $query = $db->getQuery(true)
            ->select('c.id')
            ->from('#__conditions as c')
            ->where(RL_DB::combine([
                RL_DB::is('c.alias', $condition),
                RL_DB::is('c.name', $condition),
            ]))
            ->setLimit(1);

        $db->setQuery($query);

        return $cache->set($db->loadResult());
    }

    public static function hasOtherUsesByExtensionItem($extension, $item_id)
    {
        $condition_id = self::getIdByExtensionItem($extension, $item_id);

        return self::hasOtherUsesByConditionId($condition_id, $extension, $item_id);
    }

    public static function map(
        $condition_id,
        $extension,
        $item_id,
        $table = '',
        $name_column = 'name'
    )
    {
        if ( ! $condition_id || ! $extension || ! $item_id)
        {
            return null;
        }

        $db = RL_DB::get();

        $table       = RL_Input::getCmd('table', $table);
        $name_column = RL_Input::getCmd('name_column', $name_column);

        self::removeMapping($extension, $item_id);

        $data = (object) compact('condition_id', 'extension', 'item_id', 'table', 'name_column');

        $db->insertObject('#__conditions_map', $data);

        $data->item_name = Helper::getItemNameFromDB($item_id, $table, $name_column);

        $dispatcher = JFactory::getContainer()->get(JDispatcherInterface::class);
        $dispatcher->dispatch(
            'onConditionAfterMap',
            AbstractEvent::create(
                'onConditionAfterMap',
                ['subject' => $data]
            )
        );
    }

    public static function removeMapping($extension, $item_ids)
    {
        if (empty($extension) || empty($item_ids))
        {
            return null;
        }

        $db = RL_DB::get();

        $query = $db->getQuery(true)
            ->delete('#__conditions_map')
            ->where(RL_DB::is('extension', $extension))
            ->where(RL_DB::is('item_id', $item_ids));

        $db->setQuery($query);
        $db->execute();
    }

    /**
     * Method to delete one or more records.
     *
     * @param array  &$pks An array of record primary keys.
     *
     * @return  boolean  True if successful, false if an error occurs.
     */
    public function delete(&$pks)
    {
        $condition_ids = JArray::toInteger((array) $pks);

        foreach ($condition_ids as $condition_id)
        {
            self::removeMappings($condition_id);
            $this->group_model->deleteByConditionId($condition_id);

            $pks = [$condition_id];

            if ( ! parent::delete($pks))
            {
                return false;
            }
        }

        return true;
    }

    public function disableRuleTypes(&$condition, $enabled_types)
    {
        if ( ! $condition || empty($condition->groups))
        {
            return;
        }

        $disabled_types = RL_Array::toArray($this->config->disabled_rule_types);
        $enabled_types  = RL_Array::toArray(RL_Input::getString('enabled_types'));

        if (empty($disabled_types) && empty($enabled_types))
        {
            return;
        }

        foreach ($condition->groups as &$group)
        {
            if (empty($group) || empty($group->rules))
            {
                continue;
            }

            foreach ($group->rules as $i => &$rule)
            {
                if (in_array($rule->type, $disabled_types))
                {
                    if (is_array($group->rules))
                    {
                        unset($group->rules[$i]);
                    }

                    if (is_object($group->rules))
                    {
                        unset($group->rules->{$i});
                    }

                    continue;
                }

                if ( ! empty($enabled_types)
                    && ! in_array($rule->type, $enabled_types)
                )
                {
                    $rule->disabled = true;
                }
            }
        }
    }

    /**
     * @param int $id
     *
     * @return  mixed    Object on success, false on failure.
     */
    public function duplicate($id, $publish = false, $name = '')
    {
        $item = $this->getItem($id);

        unset($item->typeAlias);

        $item->id        = 0;
        $item->published = $publish ? 1 : 0;

        if ($name)
        {
            $item->name  = $name;
            $item->alias = '';
        }

        $this->incrementName($item->name, $item->alias, $item->id);

        $item = $this->validate(null, (array) $item);

        if ( ! $this->save($item))
        {
            return false;
        }

        return $this->getItem($this->getItemIdByAlias($item['alias']));
    }

    public function getConditionByExtensionItem(
        $extension,
        $item_id,
        $prepare_form = true,
        $enabled_types = ''
    ): ?object
    {
        $condition_id = self::getIdByExtensionItem($extension, $item_id);

        if ( ! $condition_id)
        {
            return null;
        }

        return $this->getConditionById($condition_id, $prepare_form, $enabled_types);
    }

    public function getConditionById(
        $condition_id,
        $prepare_form = true,
        $enabled_types = ''
    ): ?object
    {
        $condition = $this->getItem($condition_id, $prepare_form);

        if ( ! $condition)
        {
            return null;
        }

        $this->disableRuleTypes($condition, $enabled_types);

        return $condition;
    }

    public function getConditionFromData($data, $enabled_types = '')
    {
        $groups = [];

        $params = $data;

        if (is_array($data) && isset($data['params']))
        {
            $params = json_decode($data['params']);
        }

        $match_all = (int) $this->getValue($data, 'match_all', 1);

        $data_groups = $this->getValue($params, 'groups');

        $condition = (object) [
            'id'          => $this->getValue($data, 'id', 0),
            'alias'       => $this->getValue($data, 'alias'),
            'name'        => $this->getValue($data, 'name'),
            'description' => $this->getValue($params, 'description', $this->getValue($data, 'description')),
            'category'    => $this->getValue($params, 'category', $this->getValue($data, 'category')),
            'published'   => $this->getValue($params, 'published', $this->getValue($data, 'published', 1)),
            'match_all'   => $match_all,
            'groups'      => $groups,
            'hash'        => md5(json_encode([$match_all, $groups])),
        ];

        if (empty($data_groups))
        {
            return $condition;
        }

        $enabled_types = RL_Array::toArray($enabled_types);

        $form = (array) RL_Parameters::getDataFromXmlPath(JPATH_ADMINISTRATOR . '/components/com_conditions/forms/item_rule.xml', true, true);

        $group_ordering = 0;

        foreach ($data_groups as $group)
        {
            $data_group_rules = $this->getValue($group, 'rules');

            if (empty($data_group_rules))
            {
                continue;
            }

            $group_rules   = [];
            $rule_ordering = 0;

            foreach ($data_group_rules as $rule)
            {
                if (empty($this->getValue($rule, 'type')))
                {
                    continue;
                }

                $type   = $this->getValue($rule, 'type');
                $prefix = $type . '__';

                $params = [];

                if (
                    isset($form[$type])
                    && $form[$type]->type !== 'Hidden'
                    && $form[$type]->multiple === 'true'
                )
                {
                    $params['selection'] = [];
                }

                foreach ($rule as $key => $value)
                {
                    if ( ! isset($form[$key]) || $form[$key]->type === 'Hidden')
                    {
                        continue;
                    }

                    if (isset($form[$key]) && $form[$key]->multiple === 'true')
                    {
                        // Handle comma separated strings that should be saved as an array
                        if (is_array($value) && count($value) === 1)
                        {
                            $value = RL_Array::toArray($value[0]);
                        }

                        $value = RL_Array::toArray($value);
                    }

                    if ($key === $type)
                    {
                        $params['selection'] = $value;
                        continue;
                    }

                    if ( ! str_starts_with($key, $prefix))
                    {
                        continue;
                    }

                    $key          = substr($key, strlen($prefix));
                    $params[$key] = $value;
                }

                $group_rules[] = (object) [
                    'type'     => $type,
                    'exclude'  => $this->getValue($rule, 'exclude', 0),
                    'params'   => (object) $params,
                    'ordering' => $rule_ordering,
                    'disabled' => ! empty($enabled_types) && ! in_array($type, $enabled_types),
                ];

                $rule_ordering++;
            }

            if (empty($group_rules))
            {
                continue;
            }

            $match_all = (int) $this->getValue($group, 'match_all', 1);

            $groups[] = (object) [
                'match_all' => $match_all,
                'rules'     => $group_rules,
                'ordering'  => $group_ordering,
            ];

            $group_ordering++;
        }

        $condition->groups = $groups;

        return $condition;
    }

    /**
     * @param array   $data     Data for the form.
     * @param boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  JForm|false   A Form object on success, false on failure
     */
    public function getForm($data = [], $loadData = true)
    {
        // Get the form.
        JForm::addFormPath(JPATH_ADMINISTRATOR . '/components/com_conditions/forms');

        $form = $this->loadForm('com_conditions.item', 'item', [
            'control'   => 'jform',
            'load_data' => $loadData,
        ]);

        if (empty($form))
        {
            return false;
        }

        // Modify the form based on access controls.
        if ($this->canEditState((object) $data) != true)
        {
            // Disable fields for display.
            $form->setFieldAttribute('published', 'disabled', 'true');

            // Disable fields while saving.
            // The controller has already verified this is a record you can edit.
            $form->setFieldAttribute('published', 'filter', 'unset');
        }

        if (
            empty($form->getValue('name'))
            && RL_Input::getCmd('name_column')
        )
        {
            $extension   = RL_Input::getCmd('extension');
            $item_id     = RL_Input::getInt('item_id');
            $table       = RL_Input::getCmd('table');
            $name_column = RL_Input::getCmd('name_column', 'name');

            $name = Helper::getForItemText($extension, $item_id, $table, $name_column);

            $name && $form->setValue('name', '', $name);
        }

        return $form;
    }

    /**
     * @return  mixed    Object on success, false on failure.
     */
    public function getItem($pk = null, $prepare_form = true)
    {
        // Initialise variables.
        $pk = (int) ($pk ?? $this->getState($this->getName() . '.id'));

        $extension = RL_Input::getCmd('extension');
        $item_id   = RL_Input::getInt('item_id');

        if ( ! $pk && $extension && $item_id)
        {
            $pk = self::getIdByExtensionItem($extension, $item_id);
        }

        $table = $this->getTable();

        $cache = (new Cache([__METHOD__, $pk, $prepare_form, $table->getTableName()]))
            ->useFiles();

        if ($cache->exists())
        {
            return $cache->get();
        }

        if ($pk > 0)
        {
            // Attempt to load the row.
            $return = $table->load($pk);

            // Check for a table object error.
            if ($return === false && $table->getError())
            {
                $this->setError($table->getError());

                return $cache->set(false);
            }
        }

        $properties = $table->getProperties(1);
        $item       = JArray::toObject($properties);

        $item->published = (int) $item->published;
        $item->groups    = $this->getGroups($item->id);
        $item->usage     = self::getUsage($item->id);

        $item->nr_of_uses = 0;

        foreach ($item->usage as $extension_usage)
        {
            $item->nr_of_uses += count($extension_usage);
        }

        if ($prepare_form)
        {
            $this->prepareItemForForm($item);
        }

        return $cache->set($item);
    }

    public function prepareItemForForm(object &$item): void
    {
        $this->setGroupsForForm($item);
    }

    public function removeMappings($condition_id)
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->delete('#__conditions_map')
            ->where(RL_DB::is('condition_id', $condition_id));

        $db->setQuery($query);
        $db->execute();
    }

    /**
     * @param array $data The form data.
     *
     * @return  boolean  True on success.
     */
    public function save($data)
    {
        $task = RL_Input::getCmd('task');

        if ($task == 'save2copy')
        {
            $data['published'] = 0;
        }

        $condition = $this->getConditionFromData($data);
        $extension = RL_Input::getCmd('extension');
        $item_id   = RL_Input::getInt('item_id');

        return $this->saveByObject($condition, $extension, $item_id);
    }

    public function saveByObject(
        &$condition,
        $extension,
        $item_id,
        $table = '',
        $name_column = 'name'
    )
    {
        $condition->id          ??= 0;
        $condition->alias       ??= '';
        $condition->description ??= '';

        $this->setState($this->getName() . '.id', $condition->id);

        $this->incrementName($condition->name, $condition->alias, (int) $condition->id);

        // temporarily empty the hash, to make sure it isn't saved before the groups are saved successfully
        $hash            = md5(json_encode([$condition->match_all, $condition->groups]));
        $condition->hash = '';

        $result = $this->saveCondition($condition, true);

        if ( ! $result)
        {
            return false;
        }

        $condition->id   = $condition->id ?: (int) $this->getState($this->getName() . '.id');
        $condition->hash = $hash;

        if ($extension && $item_id)
        {
            self::map(
                $condition->id,
                $extension,
                $item_id,
                $table,
                $name_column
            );
        }

        $previous_hash = $this->getState($this->getName() . '.hash');

        if ($condition->hash === $previous_hash)
        {
            if (RL_Input::getCmd('option') == 'com_conditions')
            {
                RL_Input::set('id', $condition->id);
            }

            return $this->saveCondition($condition);
        }

        if ( ! $this->saveGroups($condition))
        {
            return false;
        }

        if (RL_Input::getCmd('option') == 'com_conditions')
        {
            RL_Input::set('id', $condition->id);
        }

        return $this->saveCondition($condition);
    }

    public function setConditionByMixed($condition, $prepare_form = true, $enabled_types = '')
    {
        $condition_id = $this->getIdByMixed($condition);

        return $this->getConditionById($condition_id, $prepare_form, $enabled_types);
    }

    public function trashByExtension($extension, $item_id)
    {
        $condition_id = self::getIdByExtensionItem($extension, $item_id);

        if ( ! $condition_id)
        {
            return false;
        }

        self::removeMapping($extension, $item_id);

        $item = $this->getItem($condition_id);

        $item->published = -2;

        $item = $this->validate(null, (array) $item);

        return parent::save($item);
    }

    /**
     * Method to validate form data.
     */
    public function validate($form, $data, $group = null)
    {
        // Check for valid name
        if (empty($data['name']))
        {
            $this->setError(JText::_('CON_THE_ITEM_MUST_HAVE_A_NAME'));

            return false;
        }

        $db_columns = RL_DB::getTableColumns('#__conditions');

        $newdata = [];
        $params  = [];

        foreach ($data as $key => $val)
        {
            if (str_ends_with($key, '_errors'))
            {
                continue;
            }

            if (isset($db_columns[$key]))
            {
                $newdata[$key] = $val;
                continue;
            }

            $params[$key] = $val;
        }

        $newdata['params'] = json_encode($params);

        return $newdata;
    }

    /**
     * @return  mixed  The data for the form.
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_conditions.edit.item.data', []);

        if (empty($data))
        {
            $data = $this->getItem();
        }

        $this->preprocessData('com_conditions.item', $data);

        return $data;
    }

    private static function aliasExists(string $alias, int $id = 0): bool
    {
        $cache = new Cache;

        if ($cache->exists())
        {
            return $cache->get();
        }

        $db = RL_DB::get();

        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__conditions')
            ->where($db->quoteName('alias') . ' = ' . $db->quote($alias))
            ->where($db->quoteName('published') . ' != -2')
            ->setLimit(1);

        if ($id)
        {
            $query->where($db->quoteName('id') . ' != ' . (int) $id);
        }

        $db->setQuery($query);

        return $cache->set((bool) $db->loadResult());
    }

    private static function getAllConditionIdsByExtension(string $extension): array
    {
        if ( ! $extension)
        {
            return [];
        }

        $cache = new Cache;

        if ($cache->exists())
        {
            return $cache->get();
        }

        $db = RL_DB::get();

        $query = $db->getQuery(true)
            ->select('m.condition_id')
            ->select('m.item_id')
            ->from('#__conditions_map as m')
            ->join('LEFT', '#__conditions as c ON c.id = m.condition_id')
            ->where(RL_DB::is('m.extension', $extension))
            ->where(RL_DB::is('c.published', 1));

        $db->setQuery($query);

        return $cache->set($db->loadAssocList('item_id', 'condition_id'));
    }

    private static function getItemIdByAlias(string $alias): ?int
    {
        $cache = new Cache;

        if ($cache->exists())
        {
            return $cache->get();
        }

        $db = RL_DB::get();

        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__conditions')
            ->where($db->quoteName('alias') . ' = ' . $db->quote($alias))
            ->where($db->quoteName('published') . ' != -2')
            ->setLimit(1);

        $db->setQuery($query);

        return $cache->set($db->loadResult());
    }

    private static function getMappingByExtensionItem(string $extension, string $item_id): ?object
    {
        if ( ! $extension || ! $item_id)
        {
            return null;
        }

        $cache = new Cache;

        if ($cache->exists())
        {
            return $cache->get();
        }

        $db = RL_DB::get();

        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__conditions_map')
            ->where(RL_DB::is('extension', $extension))
            ->where(RL_DB::is('item_id', $item_id))
            ->setLimit(1);

        $db->setQuery($query);

        $item = $cache->set($db->loadObject());

        if ( ! $item)
        {
            return null;
        }

        // Fix incorrectly saved mapping
        if ($item->table == 'array' || $item->table == 'Array')
        {
            $item->table = 'modules';
        }

        if (is_array($item->table))
        {
            $item->table = $item->table[0];
        }

        return $item;
    }

    private static function getUsage(?int $condition_id): array
    {
        $cache = new Cache;

        if ($cache->exists())
        {
            return $cache->get();
        }

        $db = RL_DB::get();

        $query = $db->getQuery(true)
            ->select(['m.extension', 'm.item_id', 'm.table', 'm.name_column'])
            ->from('#__conditions_map as m')
            ->where('m.condition_id = ' . (int) $condition_id)
            ->order(['m.extension', 'm.item_id']);

        $db->setQuery($query);

        $usage = $db->loadObjectList();

        $grouped = [];

        foreach ($usage as &$item)
        {
            if ( ! isset($grouped[$item->extension]))
            {
                $grouped[$item->extension] = [];
            }

            // Fix incorrectly saved mapping
            if ($item->table == 'array' || $item->table == 'Array')
            {
                $item->table = 'modules';
            }

            if (is_array($item->table))
            {
                $item->table = $item->table[0];
            }

            $item->item_name = Helper::getItemNameFromDB($item->item_id, $item->table, $item->name_column);
            $item->published = Helper::getItemPublishStateFromDB($item->item_id, $item->table);
            self::setItemUrl($item);

            $grouped[$item->extension][$item->item_id] = $item;
        }

        ksort($grouped);

        return $cache->set($grouped);
    }

    private static function hasOtherUsesByConditionId(
        ?int   $condition_id,
        string $extension,
        int    $item_id
    ): bool
    {
        if (empty($condition_id) || empty($extension))
        {
            return false;
        }

        $usage = self::getUsage($condition_id);

        foreach ($usage as $usage_extension_name => $extension_usage)
        {
            if ($usage_extension_name != $extension)
            {
                return true;
            }

            foreach ($extension_usage as $item)
            {
                if ($item->item_id != $item_id)
                {
                    return true;
                }
            }
        }

        return false;
    }

    private static function setItemUrl(object &$item): void
    {
        $item->url = '';

        if (RL_Document::isClient('site'))
        {
            return;
        }

        if ( ! $item->item_id)
        {
            return;
        }

        switch ($item->extension)
        {
            case 'com_advancedmodules':
                $canEdit = RL_User::authorise('core.edit', 'com_modules.module.' . $item->item_id);

                if ( ! $canEdit)
                {
                    return;
                }

                $item->url = JRoute::_('index.php?option=' . $item->extension . '&task=module.edit&id=' . $item->item_id);
                break;

            case 'com_rereplacer':
                $canEdit = RL_User::authorise('core.edit', $item->extension . '.item.' . $item->item_id);

                if ( ! $canEdit)
                {
                    return;
                }

                $item->url = JRoute::_('index.php?option=' . $item->extension . '&task=item.edit&id=' . $item->item_id);
                break;

            default:
                break;
        }
    }

    private function getGroups(?int $condition_id): array
    {
        if ( ! $condition_id)
        {
            return [];
        }

        $cache = (new Cache)->useFiles();

        if ($cache->exists())
        {
            return (array) $cache->get();
        }

        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__conditions_groups as g')
            ->where('g.condition_id = ' . (int) $condition_id);

        $db->setQuery($query);

        $groups = (array) $db->loadObjectList();

        foreach ($groups as &$group)
        {
            $group->rules = $this->group_model->getRules($group->id);
            $group->hash  = md5(json_encode([$group->match_all, $group->rules]));
        }

        return $cache->set($groups);
    }

    private function getValue(mixed $object, string $key, mixed $default = ''): mixed
    {
        if (is_array($object))
        {
            return $object[$key] ?? $default;
        }

        if (is_object($object))
        {
            return $object->{$key} ?? $default;
        }

        return $default;
    }

    private function incrementName(string &$name, string &$alias, int $id = 0): void
    {
        $alias = $alias ?: RL_Alias::get($name);

        $name  = RL_String::truncate($name, 100);
        $alias = RL_Alias::get(RL_String::truncate($alias, 100));

        if ( ! self::aliasExists($alias, $id))
        {
            return;
        }

        $name  = RL_String::truncate($name, 90);
        $alias = RL_Alias::get(RL_String::truncate($alias, 90));

        while (self::aliasExists($alias, $id))
        {
            $name  = RL_String::increment($name);
            $alias = RL_String::increment($alias, 'dash');
        }
    }

    private function saveCondition(object $condition, bool $ignore_actionlog = false): bool
    {
        $data = (array) $condition;
        unset($data['groups']);
        $data['ignore_actionlog'] = $ignore_actionlog;

        $table = $this->getTable();
        $key   = $table->getKeyName();
        $pk    = (int) ($data[$key] ?? $this->getState($this->getName() . '.id'));

        if ( ! $pk)
        {
            unset($data[$key]);

            return parent::save($data);
        }

        try
        {
            $table->load($pk);
        }
        catch (Exception $e)
        {
            $this->setError($e->getMessage());

            return false;
        }

        if (isset($table->hash))
        {
            $this->setState($this->getName() . '.hash', $table->hash);
        }

        (new Cache())->resetAll();

        return parent::save($data);
    }

    private function saveGroups(object $condition): bool
    {
        $this->group_model->deleteByConditionId($condition->id);

        $result = true;

        foreach ($condition->groups as $i => $group)
        {
            $group->condition_id = $condition->id;
            $group->ordering     ??= $i;

            if ( ! $this->group_model->save($group))
            {
                $result = false;
                break;
            }
        }

        return $result;
    }

    private function setGroupsForForm(object &$item): void
    {
        $groups       = $item->groups ?? $this->getGroups($item->id);
        $item->groups = (object) [];

        $group_count = 0;

        foreach ($groups as $group)
        {
            $group_name = '__field3' . $group_count;
            $group_count++;

            $item->groups->{$group_name} = RL_Object::clone($group);

            $this->setRulesForFormByGroup($item, $group->rules, $group_name);
        }
    }

    private function setRuleParamsForForm(object &$rule): void
    {
        $rule->{$rule->type} = 1;

        foreach ($rule->params as $key => $value)
        {
            if ($key === 'selection')
            {
                $rule->{$rule->type} = $value;
                continue;
            }

            $rule->{$rule->type . '__' . $key} = $value;
        }

        unset($rule->id);
        unset($rule->group_id);
        unset($rule->params);
    }

    private function setRulesForFormByGroup(
        object &$item,
        array  $rules,
        string $group_name
    ): void
    {
        $item->groups->{$group_name}->rules = (object) [];

        $rule_count = 0;

        foreach ($rules as $group_rule)
        {
            $rule_name = '__field4' . $rule_count;
            $rule_count++;

            $rule = RL_Object::clone($group_rule);
            $this->setRuleParamsForForm($rule);

            $item->groups->{$group_name}->rules->{$rule_name} = $rule;
        }
    }
}

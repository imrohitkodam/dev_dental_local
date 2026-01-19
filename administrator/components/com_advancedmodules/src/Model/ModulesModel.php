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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\DatabaseQuery;
use Joomla\Database\ParameterType;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;
use RegularLabs\Library\DB as RL_DB;
use RegularLabs\Library\Input as RL_Input;
use RegularLabs\Library\Language as RL_Language;
use RegularLabs\Library\Parameters as RL_Parameters;

/**
 * Modules Component Module Model
 */
class ModulesModel extends ListModel
{
    /**
     * @var    string  The prefix to use with controller messages.
     */
    protected $text_prefix = 'COM_MODULES';

    /**
     * Constructor.
     *
     * @param array $config An optional associative array of configuration settings.
     *
     * @see     \JController
     * @since   1.6
     */
    public function __construct($config = [])
    {
        RL_Language::load('com_modules', JPATH_ADMINISTRATOR);

        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = [
                'id', 'a.id',
                'title', 'a.title',
                'checked_out', 'a.checked_out',
                'checked_out_time', 'a.checked_out_time',
                'published', 'a.published', 'state',
                'access', 'a.access',
                'ag.title', 'access_level',
                'ordering', 'a.ordering',
                'module', 'a.module',
                'language', 'a.language',
                'l.title', 'language_title',
                'publish_up', 'a.publish_up',
                'publish_down', 'a.publish_down',
                'client_id', 'a.client_id',
                'position', 'a.position',
                'name', 'e.name',
                'category', 'amm.category',
                'color', 'amm.color',
            ];
        }

        parent::__construct($config);
    }

    function getHasCategories()
    {
        $db    = RL_DB::get();
        $query = $db->getQuery(true);

        $query->select('COUNT(*)')
            ->from($db->quoteName('#__advancedmodules'))
            ->where($db->quoteName('category') . ' != ' . $db->quote(''))
            ->where($db->quoteName('category') . ' IS NOT NULL');

        $db->setQuery($query);

        return (bool) $db->loadResult();
    }

    /**
     * Returns an object list
     *
     * @param DatabaseQuery $query      The query
     * @param int           $limitstart Offset
     * @param int           $limit      The number of records
     *
     * @return  array
     */
    protected function _getList($query, $limitstart = 0, $limit = 0)
    {
        $db     = $this->getDatabase();
        $config = $this->getConfig();
        [$default_ordering, $default_direction] = explode(' ', $config->default_ordering, 2);

        $listOrder = $this->getState('list.ordering', $default_ordering);
        $listDirn  = $this->getState('list.direction', $default_direction);

        // If ordering by fields that need translate we need to sort the array of objects after translating them.
        if (in_array($listOrder, ['name']))
        {
            // Fetch the results.
            $db->setQuery($query);
            $result = $db->loadObjectList();

            // Translate the results.
            $this->translate($result);

            // Sort the array of translated objects.
            $result = ArrayHelper::sortObjects($result, $listOrder, strtolower($listDirn) == 'desc' ? -1 : 1, true, true);

            // Process pagination.
            $total                                      = count($result);
            $this->cache[$this->getStoreId('getTotal')] = $total;

            if ($total < $limitstart)
            {
                $limitstart = 0;
                $this->setState('list.start', 0);
            }

            return array_slice($result, $limitstart, $limit ?: null);
        }

        if ($listOrder == 'amm.color')
        {
            return $this->_getListByColor($query, $limitstart, $limit, $listDirn);
        }

        switch ($listOrder)
        {
            case 'a.ordering':
                $query->order($db->quoteName('a.position') . ' ASC')
                    ->order($db->quoteName($listOrder) . ' ' . $db->escape($listDirn));
                break;
            case 'a.position':
                $query->order($db->quoteName($listOrder) . ' ' . $db->escape($listDirn))
                    ->order($db->quoteName('a.ordering') . ' ASC');
                break;
            default:
                $query->order($db->quoteName($listOrder) . ' ' . $db->escape($listDirn));
                break;
        }

        // Process pagination.
        $result = parent::_getList($query, $limitstart, $limit);

        // Translate the results.
        $this->translate($result);

        return $result;
    }

    protected function _getListByColor($query, $limitstart, $limit, $orderDirn)
    {
        $db = $this->getDatabase();

        $query->order('a.title' . ' ' . $orderDirn);
        $db->setQuery($query);

        $result = $db->loadObjectList();
        $this->translate($result);

        $newresult = [];

        $config = $this->getConfig();

        $colors = str_replace('#', '', strtolower($config->main_colors));
        $colors = explode(',', $colors);
        $colors = array_diff($colors, ['none']);

        foreach ($result as $i => $row)
        {
            $color = str_replace('#', '', $row->color ?? 'none');

            $color = array_search(strtolower($color), $colors);
            $color = $color === false ? 'none' : str_pad($color, 4, '0', STR_PAD_LEFT);

            $newresult[$color . '.' . str_pad($i, 8, '0', STR_PAD_LEFT)] = $row;
        }

        ksort($newresult);

        if ($orderDirn == 'DESC')
        {
            krsort($newresult);
        }

        $newresult = array_values($newresult);
        $total     = count($newresult);

        $this->cache[$this->getStoreId('getTotal')] = $total;

        if ($total < $limitstart)
        {
            $limitstart = 0;
            $this->setState('list.start', 0);
        }

        return array_slice($newresult, $limitstart, $limit ?: null);
    }

    /**
     * Function that gets the config settings
     *
     * @return    Object
     */
    protected function getConfig()
    {
        if (isset($this->config))
        {
            return $this->config;
        }

        $this->config = RL_Parameters::getComponent('advancedmodules');

        return $this->config;
    }

    /**
     * Manipulate the query to be used to evaluate if this is an Empty State to provide specific conditions for this extension.
     *
     * @return DatabaseQuery
     */
    protected function getEmptyStateQuery()
    {
        $db    = $this->getDatabase();
        $query = parent::getEmptyStateQuery();

        $clientId = $this->getFilterClientId();

        $query->where($db->quoteName('a.client_id') . ' = :client_id')
            ->bind(':client_id', $clientId, ParameterType::INTEGER);

        return $query;
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return  DatabaseQuery
     */
    protected function getListQuery()
    {
        // Create a new query object.
        $db    = RL_DB::get();
        $query = $db->getQuery(true);

        // Select the required fields.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.title, a.note, a.position, a.module, a.language,' .
                'a.checked_out, a.checked_out_time, a.published AS published, e.enabled AS enabled, a.access, a.ordering, a.publish_up, a.publish_down'
            )
        );

        // From modules table.
        $query->from($db->quoteName('#__modules', 'a'));

        // Join over the language
        $query->select($db->quoteName('l.title', 'language_title'))
            ->select($db->quoteName('l.image', 'language_image'))
            ->join('LEFT', $db->quoteName('#__languages', 'l') . ' ON ' . $db->quoteName('l.lang_code') . ' = ' . $db->quoteName('a.language'));

        // Join over the users for the checked out user.
        $query->select($db->quoteName('uc.name', 'editor'))
            ->join('LEFT', $db->quoteName('#__users', 'uc') . ' ON ' . $db->quoteName('uc.id') . ' = ' . $db->quoteName('a.checked_out'));

        // Join over the asset groups.
        $query->select($db->quoteName('ag.title', 'access_level'))
            ->join('LEFT', $db->quoteName('#__viewlevels', 'ag') . ' ON ' . $db->quoteName('ag.id') . ' = ' . $db->quoteName('a.access'));

        // Join over the extensions
        $query->select($db->quoteName('e.name', 'name'))
            ->join('LEFT', $db->quoteName('#__extensions', 'e') . ' ON ' . $db->quoteName('e.element') . ' = ' . $db->quoteName('a.module'));

        // Join over the extra data from Advanced Module Manager
        $query->select($db->quoteName('amm.category', 'category'))
            ->select($db->quoteName('amm.color', 'color'))
            ->join('LEFT', $db->quoteName('#__advancedmodules', 'amm') . ' ON ' . $db->quoteName('amm.module_id') . ' = ' . $db->quoteName('a.id'));

        // Group (careful with PostgreSQL)
        $query->group(
            'a.id, a.title, a.note, a.position, a.module, a.language, a.checked_out, '
            . 'a.checked_out_time, a.published, a.access, a.ordering, l.title, l.image, uc.name, ag.title, e.name, '
            . 'l.lang_code, uc.id, ag.id, e.element, a.publish_up, a.publish_down, e.enabled'
        );

        $this->setFiltersOnQuery($query);

        return $query;
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param string $id A prefix for the store id.
     *
     * @return  string    A store id.
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':' . $this->getFilterClientId();
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.state');
        $id .= ':' . $this->getState('filter.position');
        $id .= ':' . $this->getState('filter.module');
        $id .= ':' . $this->getState('filter.category');

        if ($this->isAdminFilter())
        {
            // Only use these for admin modules
            // These assignments are moved to Conditions for frontend modules
            $id .= ':' . $this->getState('filter.access');
            $id .= ':' . $this->getState('filter.language');
            //        $id .= ':' . $this->getState('filter.menuitem');
        }

        return parent::getStoreId($id);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param string $ordering  An optional ordering field.
     * @param string $direction An optional direction (asc|desc).
     *
     * @return  void
     */
    protected function populateState($ordering = '', $direction = '')
    {
        if ( ! $ordering)
        {
            $config = $this->getConfig();
            [$ordering, $direction] = explode(' ', $config->default_ordering, 2);
        }

        $app = Factory::getApplication();

        $layout = RL_Input::getCmd('layout');

        // Adjust the context to support modal layouts.
        if ($layout)
        {
            $this->context .= '.' . $layout;
        }

        $client_id = RL_Input::getInt('client_id', 0);

        // Make context client aware
        $this->context .= '.' . $client_id;

        // Load the filter state.
        $this->setState('filter.search', $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string'));
        $this->setState('filter.position', $this->getUserStateFromRequest($this->context . '.filter.position', 'filter_position', '', 'string'));
        $this->setState('filter.module', $this->getUserStateFromRequest($this->context . '.filter.module', 'filter_module', '', 'string'));
        $this->setState('filter.menuitem', $this->getUserStateFromRequest($this->context . '.filter.menuitem', 'filter_menuitem', '', 'cmd'));
        $this->setState('filter.category', $this->getUserStateFromRequest($this->context . '.filter.category', 'filter_category', '', 'cmd'));

        if ($client_id)
        {
            // Only for admin modules
            $this->setState('filter.access', $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', '', 'cmd'));
        }

        // If in modal layout on the frontend, state and language are always forced.
        if ($app->isClient('site') && $layout === 'modal')
        {
            $this->setState('filter.state', 1);
            // $this->setState('filter.language', 'current');
        }
        // If in backend (modal or not) we get the same fields from the user request.
        else
        {
            $this->setState('filter.state', $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'string'));

            if ($client_id)
            {
                // Only for admin modules
                $this->setState('filter.language', $this->getUserStateFromRequest($this->context . '.filter.language', 'filter_language', '', 'string'));
            }
        }

        // Special case for the client id.
        if ($app->isClient('site') || $layout === 'modal')
        {
            $this->setState('client_id', 0);
            $clientId = 0;
        }
        else
        {
            $clientId = (int) $this->getUserStateFromRequest($this->context . '.client_id', 'client_id', 0, 'int');
            $clientId = ( ! in_array($clientId, [0, 1])) ? 0 : $clientId;
            $this->setState('client_id', $clientId);
        }

        // Use a different filter file when client is administrator
        if ($clientId == 1)
        {
            $this->filterFormName = 'filter_modulesadmin';
        }

        // Load the parameters.
        $params = ComponentHelper::getParams('com_advancedmodules');
        $this->setState('params', $params);

        // List state information.
        parent::populateState($ordering, $direction);
    }

    /**
     * Translate a list of objects
     *
     * @param array  &$items The array of objects
     *
     * @return  array The array of translated objects
     */
    protected function translate(&$items)
    {
        $lang       = Factory::getApplication()->getLanguage();
        $clientPath = $this->isAdminFilter() ? JPATH_ADMINISTRATOR : JPATH_SITE;

        foreach ($items as $item)
        {
            $extension = $item->module;
            $source    = $clientPath . "/modules/$extension";
            $lang->load("$extension.sys", $clientPath)
            || $lang->load("$extension.sys", $source);
            $item->name = Text::_($item->name);
        }
    }

    private function getFilterClientId()
    {
        return (int) $this->getState('client_id');
    }

    private function isAdminFilter()
    {
        return (bool) $this->getFilterClientId();
    }

    private function setAccessFilter(&$query)
    {
        $access = $this->getState('filter.access');

        if ( ! $access)
        {
            return;
        }

        $this->isAdminFilter()
            ? $this->setAccessFilterAdmin($query, $access)
            : $this->setAccessFilterSite($query, $access);
    }

    private function setAccessFilterAdmin(&$query, $access)
    {
        $db = RL_DB::get();

        $access = (int) $access;
        $query->where($db->quoteName('a.access') . ' = :access')
            ->bind(':access', $access, ParameterType::INTEGER);
    }

    private function setAccessFilterSite(&$query, $access)
    {
    }

    private function setFiltersOnQuery(&$query)
    {
        $db       = RL_DB::get();
        $clientId = $this->getFilterClientId();

        // Filter by client.
        $query->where($db->quoteName('a.client_id') . ' = :aclientid')
            ->where($db->quoteName('e.client_id') . ' = :eclientid')
            ->bind(':aclientid', $clientId, ParameterType::INTEGER)
            ->bind(':eclientid', $clientId, ParameterType::INTEGER);

        // Filter by current user access level.
        $user = Factory::getUser();

        // Get the current user for authorisation checks
        if ($user->authorise('core.admin') !== true)
        {
            $groups = $user->getAuthorisedViewLevels();
            $query->whereIn($db->quoteName('a.access'), $groups);
        }

        // Filter by published state.
        $state = $this->getState('filter.state');

        if (is_numeric($state))
        {
            $state = (int) $state;
            $query->where($db->quoteName('a.published') . ' = :state')
                ->bind(':state', $state, ParameterType::INTEGER);
        }
        elseif ($state === '')
        {
            $query->whereIn($db->quoteName('a.published'), [0, 1]);
        }

        // Filter by position.
        $position = $this->getState('filter.position');

        if ($position)
        {
            $position = ($position === 'none') ? '' : $position;
            $query->where($db->quoteName('a.position') . ' = :position')
                ->bind(':position', $position);
        }

        // Filter by module.
        $module = $this->getState('filter.module');

        if ($module)
        {
            $query->where($db->quoteName('a.module') . ' = :module')
                ->bind(':module', $module);
        }

        // Filter by category.
        $category = $this->getState('filter.category');

        if ($category)
        {
            $category = ($category === 'none') ? '' : $category;
            $query->where($db->quoteName('amm.category') . ' = :category')
                ->bind(':category', $category);
        }

        $this->setSearchFilter($query);
        $this->setLanguageFilter($query);
        $this->setAccessFilter($query);
    }

    private function setLanguageFilter(&$query)
    {
        $language = $this->getState('filter.language');

        if ( ! $language || $language === '*')
        {
            return;
        }

        $this->isAdminFilter()
            ? $this->setLanguageFilterAdmin($query, $language)
            : $this->setLanguageFilterSite($query, $language);
    }

    private function setLanguageFilterAdmin(&$query, $language)
    {
        $db = RL_DB::get();

        if ($language === 'current')
        {
            $language = [Factory::getApplication()->getLanguage()->getTag(), '*'];
            $query->whereIn($db->quoteName('a.language'), $language, ParameterType::STRING);

            return;
        }

        $query->where($db->quoteName('a.language') . ' = :language')
            ->bind(':language', $language);
    }

    private function setLanguageFilterSite(&$query, $language)
    {
    }

    private function setSearchFilter(&$query)
    {
        $db = RL_DB::get();

        $search = $this->getState('filter.search');

        if (empty($search))
        {
            return;
        }

        if (stripos($search, 'id:') === 0)
        {
            $ids = (int) substr($search, 3);
            $query->where($db->quoteName('a.id') . ' = :id')
                ->bind(':id', $ids, ParameterType::INTEGER);

            return;
        }

        if (preg_match('#^\#[0-9a-z]{3,6}$#i', $search))
        {
            $query->where($db->quoteName('amm.color') . ' = :color')
                ->bind(':color', $search);

            return;
        }

        $search = '%' . StringHelper::strtolower($search) . '%';
        $query->extendWhere(
            'AND',
            [
                'LOWER(' . $db->quoteName('a.title') . ') LIKE :title',
                'LOWER(' . $db->quoteName('a.note') . ') LIKE :note',
            ],
            'OR'
        )
            ->bind(':title', $search)
            ->bind(':note', $search);
    }
}

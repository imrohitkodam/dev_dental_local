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

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\MVC\Model\ListModel;
use RegularLabs\Library\DB as RL_DB;
use RegularLabs\Library\Input as RL_Input;
use RegularLabs\Library\Parameters as RL_Parameters;

defined('_JEXEC') or die;

class ItemsModel extends ListModel
{
    protected $config;

    /**
     * @var     string    The prefix to use with controller messages.
     */
    protected $text_prefix = 'RL';

    /**
     * Constructor.
     *
     * @param array    An optional associative array of configuration settings.
     *
     * @see        JController
     */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = [
                'alias', 'a.alias',
                'category', 'a.category',
                'color', 'a.color',
                'description', 'a.description',
                'id', 'a.id',
                'name', 'a.name',
                'published', 'a.published',
            ];
        }

        parent::__construct($config);

        $is_modal = RL_Input::getString('layout') === 'modal';

        if ($is_modal)
        {
            $this->context .= '_modal';
        }

        $this->config = RL_Parameters::getComponent('conditions');
    }

    /**
     * Duplicate Method
     * Duplicate all items specified by array id
     */
    public function duplicate($ids, $model)
    {
        foreach ($ids as $id)
        {
            $model->duplicate($id);
        }

        $msg = JText::sprintf('%d items duplicated', count($ids));
        JFactory::getApplication()->enqueueMessage($msg);
    }

    public function getItems($getall = false, $aliases = [])
    {
        $db = $this->getDatabase();

        // Load the list items.
        if ($getall)
        {
            $query = RL_DB::getQuery()
                // Select the required fields from the table.
                ->select('a.*')
                ->from(RL_DB::quoteName('#__conditions', 'a'));
        }
        else
        {
            $query = $this->_getListQuery();
        }

        $sub_query = RL_DB::getQuery()
            ->select('count(*)')
            ->from('#__conditions_map as m')
            ->where('m.condition_id = a.id');
        $query->select('(' . ((string) $sub_query) . ') as nr_of_uses');

        if ($getall)
        {
            $db->setQuery($query);
            $items = $db->loadObjectList('alias');
        }
        else
        {
            $items = $this->_getList($query, $this->getStart(), $this->getState('list.limit'));
        }

        return $items;
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return    JDatabaseQuery
     */
    protected function getListQuery()
    {
        $db = RL_DB::get();

        $query = RL_DB::getQuery()
            // Select the required fields from the table.
            ->select(
                $this->getState(
                    'list.select',
                    'a.*'
                )
            )
            ->from(RL_DB::quoteName('#__conditions', 'a'));

        $state = $this->getState('filter.state');

        if (is_numeric($state))
        {
            $query->where(RL_DB::quoteName('a.published') . ' = ' . ( int ) $state);
        }
        elseif ($state == '')
        {
            $query->where('( ' . RL_DB::quoteName('a.published') . ' IN ( 0,1,2 ) )');
        }

        $category = $this->getState('filter.category');

        if ($category != '')
        {
            $query->where(RL_DB::quoteName('a.category') . ' = ' . RL_DB::quote($category));
        }

        // Filter the list over the search string if set.
        $search = $this->getState('filter.search');

        if ( ! empty($search))
        {
            if (stripos($search, 'id:') === 0)
            {
                $query->where(RL_DB::quoteName('a.id') . ' = ' . ( int ) substr($search, 3));
            }
            else
            {
                $search = RL_DB::quote('%' . RL_DB::escape($search, true) . '%');
                $query->where(
                    '( ' . RL_DB::quoteName('a.alias') . ' LIKE ' . $search .
                    ' OR ' . RL_DB::quoteName('a.name') . ' LIKE ' . $search .
                    ' OR ' . RL_DB::quoteName('a.description') . ' LIKE ' . $search .
                    ' OR ' . RL_DB::quoteName('a.category') . ' LIKE ' . $search . ' )'
                );
            }
        }

        $query->select(RL_DB::quoteName('uc.name', 'editor'))
            ->join('LEFT', RL_DB::quoteName('#__users', 'uc'), RL_DB::quoteName('uc.id') . ' = ' . RL_DB::quoteName('a.checked_out'));

        // Add the list ordering clause.
        $ordering = $this->getState('list.ordering', 'a.name');

        $query->order(RL_DB::quoteName(RL_DB::escape($ordering)) . ' ' . RL_DB::escape($this->getState('list.direction', 'ASC')));

        return $query;
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param string    A prefix for the store id.
     *
     * @return    string    A store id.
     */
    protected function getStoreId($id = '', $getall = 0, $alias = [])
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.state');
        $id .= ':' . $getall;
        $id .= ':' . json_encode($alias);

        return parent::getStoreId($id);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     */
    protected function populateState($ordering = null, $direction = null)
    {
        // List state information.
        parent::populateState('a.name', 'asc');
    }

    function getHasCategories()
    {
        $db    = RL_DB::get();
        $query = RL_DB::getQuery();

        $query->select('COUNT(*)')
            ->from(RL_DB::quoteName('#__conditions'))
            ->where(RL_DB::quoteName('category') . ' != ' . RL_DB::quote(''))
            ->where(RL_DB::quoteName('category') . ' IS NOT NULL');

        $db->setQuery($query);

        return (bool) $db->loadResult();
    }
}

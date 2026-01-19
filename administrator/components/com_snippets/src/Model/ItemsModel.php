<?php
/**
 * @package         Snippets
 * @version         9.3.8
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

namespace RegularLabs\Component\Snippets\Administrator\Model;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Form\Form as JForm;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\MVC\Model\ListModel;
use RegularLabs\Library\DB as RL_DB;
use RegularLabs\Library\Input as RL_Input;
use RegularLabs\Library\Parameters as RL_Parameters;
use RegularLabs\Library\RegEx as RL_RegEx;
use RegularLabs\Library\StringHelper as RL_String;

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
                'ordering', 'a.ordering',
                'published', 'a.published',
            ];
        }

        parent::__construct($config);

        $this->config = RL_Parameters::getComponent('snippets');
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

    /**
     * Export Method
     * Export the selected items specified by id
     */
    public function export($ids)
    {
        $db    = RL_DB::get();
        $query = RL_DB::getQuery()
            ->select('s.alias')
            ->select('s.name')
            ->select('s.description')
            ->select('s.category')
            ->select('s.color')
            ->select('s.content')
            ->select('s.params')
            ->select('s.published')
            ->select('s.ordering')
            ->from('#__snippets as s')
            ->where('s.id IN ( ' . implode(', ', $ids) . ' )');
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        $format = $this->config->export_format;

        $this->exportDataToFile($rows, $format);
    }

    public function exportDataToFile($rows, $format = 'json')
    {
        $filename = 'Snippets Items';

        if (count($rows) == 1)
        {
            $name = RL_String::strtolower(RL_String::html_entity_decoder($rows[0]->name));
            $name = RL_RegEx::replace('[^a-z0-9_-]', '_', $name);
            $name = trim(RL_RegEx::replace('__+', '_', $name), '_-');

            $filename = 'Snippets Item (' . $name . ')';
        }

        $string = json_encode($rows);

        $this->exportStringToFile($string, $filename);
    }

    public function exportStringToFile($string, $filename)
    {
        // SET DOCUMENT HEADER
        if (RL_RegEx::match('Opera(/| )([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT']))
        {
            $UserBrowser = "Opera";
        }
        elseif (RL_RegEx::match('MSIE ([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT']))
        {
            $UserBrowser = "IE";
        }
        else
        {
            $UserBrowser = '';
        }

        $mime_type = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ? 'application/octetstream' : 'application/octet-stream';
        @ob_end_clean();
        ob_start();

        header('Content-Type: ' . $mime_type);
        header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');

        if ($UserBrowser == 'IE')
        {
            header('Content-Disposition: inline; filename="' . $filename . '.json"');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            echo $string;
            die;
        }

        header('Content-Disposition: attachment; filename="' . $filename . '.json"');
        header('Pragma: no-cache');
        echo $string;
        die;
    }

    public function getAllItems()
    {
        $db = RL_DB::get();

        $query = RL_DB::getQuery()
            ->select('*')
            ->from(RL_DB::quoteName('#__snippets'));

        $db->setQuery($query);
        $items = $db->loadObjectList();

        $this->setItemVariables($items);

        return $items;
    }

    /**
     * @param array   $data     Data for the form.
     * @param boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  JForm   A Form object on success, false on failure
     */
    public function getForm($data = [], $loadData = true)
    {
        return $this->loadForm('com_snippets.items', 'items');
    }

    public function getItems($only_published = false, $aliases = [])
    {
        // Get a storage key.
        $store = $this->getStoreId('', $only_published, $aliases);

        // Try to load the data from internal storage.
        if (isset($this->cache[$store]))
        {
            return $this->cache[$store];
        }

        $query = $this->_getListQuery();

        if ($only_published)
        {
            $query->where('a.published = 1');
        }

        if ( ! empty($aliases))
        {
            $query = RL_DB::getQuery()
                // Select the required fields from the table.
                ->select([
                    'a.*',
                    RL_DB::quoteName('uc.name', 'editor'),
                ])
                ->from(RL_DB::quoteName('#__snippets', 'a'))
                ->join('LEFT', RL_DB::quoteName('#__users', 'uc'), RL_DB::quoteName('uc.id') . ' = ' . RL_DB::quoteName('a.checked_out'))
                ->where('a.published = 1')
                ->where(RL_DB::in('a.alias', $aliases))
                ->order(RL_DB::quoteName(RL_DB::escape('a.ordering')) . ' ASC');

            RL_DB::get()->setQuery($query);

            $items = RL_DB::get()->loadObjectList('alias');
        }
        else
        {
            $query = $this->_getListQuery();

            if ($only_published)
            {
                $query->where('a.published = 1');
            }

            $items = $this->_getList($query, $this->getStart(), $this->getState('list.limit'));
        }

        $this->setItemVariables($items);

        // Add the items to the internal cache.
        $this->cache[$store] = $items;

        return $items;
    }

    public function getItemsFromImportData($data)
    {
        $items = json_decode($data, true);

        if (is_null($items))
        {
            return [];
        }

        return $items;
    }

    public function hasItems()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('count(*)')
            ->from($db->quoteName('#__snippets', 'a'))
            ->where('( ' . $db->quoteName('a.published') . ' IN ( 0,1,2 ) )');

        $db->setQuery($query);

        return $db->loadResult() > 0;
    }

    /**
     * Import Method
     * Import the selected items specified by id
     * and set Redirection to the list of items
     */
    public function import($file, $model)
    {
        $msg = JText::_('SNP_PLEASE_CHOOSE_A_VALID_FILE');

        if (empty($file) || ! is_array($file) || ! isset($file['name']))
        {
            JFactory::getApplication()->enqueueMessage($msg, 'warning');

            return;
        }

        $file_format = pathinfo($file['name'], PATHINFO_EXTENSION);

        if ($file_format !== 'json')
        {
            JFactory::getApplication()->enqueueMessage($msg, 'warning');

            return;
        }

        $publish_all = RL_Input::getInt('publish_all', 0);

        $data = file_get_contents($file['tmp_name']);

        if (empty($data))
        {
            JFactory::getApplication()->enqueueMessage($msg, 'warning');

            return;
        }

        $items = $this->getItemsFromImportData($data);

        if (empty($items))
        {
            JFactory::getApplication()->enqueueMessage($msg, 'warning');

            return;
        }

        foreach ($items as $item)
        {
            $item['id'] = 0;

            if ($publish_all == 0)
            {
                unset($item['published']);
            }
            elseif ($publish_all == 1)
            {
                $item['published'] = 1;
            }

            $saved = $model->save($item);

            if ($saved != 1)
            {
                $error = JText::_('Error Saving Item') . ' ( ' . $saved . ' )';
                JFactory::getApplication()->enqueueMessage($error, 'error');
            }
        }

        JFactory::getApplication()->enqueueMessage(JText::_('Items saved'));
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
            ->from(RL_DB::quoteName('#__snippets', 'a'));

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
        $ordering  = $this->state->get('list.ordering', 'a.ordering');
        $direction = $this->state->get('list.direction', 'ASC');

        if ($ordering == 'a.ordering')
        {
            $query->order('( ' . RL_DB::quoteName('a.category') . ' = ' . RL_DB::quote('') . ' )')
                ->order(RL_DB::quoteName('a.category') . ' ASC');
        }

        $query->order(RL_DB::quoteName(RL_DB::escape($ordering)) . ' ' . RL_DB::escape($direction));

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
    protected function getStoreId($id = '', $only_published = false, $alias = [])
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.state');
        $id .= ':' . $only_published;
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
        parent::populateState('a.ordering', 'asc');
    }

    private function getButtonVariables($variables)
    {
    }

    private function getVariables($variables)
    {
    }

    private function setItemVariables(&$items)
    {
        foreach ($items as $i => &$item)
        {
            $item->params = RL_Parameters::getObjectFromData(
                $item->params,
                JPATH_ADMINISTRATOR . '/components/com_snippets/forms/item.xml'
            );

            foreach ($item->params as $key => $val)
            {
                if ( ! isset($item->{$key}) && ! is_object($val))
                {
                    $items[$i]->{$key} = $val;
                }
            }

        }
    }
}

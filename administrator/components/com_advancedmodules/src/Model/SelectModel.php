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

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\DatabaseQuery;
use Joomla\Database\ParameterType;
use Joomla\Filesystem\Path;
use Joomla\Utilities\ArrayHelper;
use RegularLabs\Library\DB as RL_DB;
use RegularLabs\Library\Language as RL_Language;

/**
 * Module model.
 */
class SelectModel extends ListModel
{
    /**
     * Method to get a list of items.
     *
     * @return  mixed  An array of objects on success, false on failure.
     */
    public function getItems()
    {
        RL_Language::load('com_modules', JPATH_ADMINISTRATOR);

        // Get the list of items from the database.
        $items = parent::getItems();

        $client = ApplicationHelper::getClientInfo($this->getState('client_id', 0));
        $lang   = Factory::getApplication()->getLanguage();

        // Loop through the results to add the XML metadata,
        // and load language support.
        foreach ($items as &$item)
        {
            $path = Path::clean($client->path . '/modules/' . $item->module . '/' . $item->module . '.xml');

            if (file_exists($path))
            {
                $item->xml = simplexml_load_file($path);
            }
            else
            {
                $item->xml = null;
            }

            // 1.5 Format; Core files or language packs then
            // 1.6 3PD Extension Support
            $lang->load($item->module . '.sys', $client->path)
            || $lang->load($item->module . '.sys', $client->path . '/modules/' . $item->module);
            $item->name = Text::_($item->name);

            if (isset($item->xml) && $text = trim($item->xml->description))
            {
                $item->desc = Text::_($text);
            }
            else
            {
                $item->desc = Text::_('COM_MODULES_NODESCRIPTION');
            }
        }

        $items = ArrayHelper::sortObjects($items, 'name', 1, true, true);

        // TODO: Use the cached XML from the extensions table?

        return $items;
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

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.extension_id, a.name, a.element AS module'
            )
        );
        $query->from($db->quoteName('#__extensions', 'a'));

        // Filter by module
        $query->where($db->quoteName('a.type') . ' = ' . $db->quote('module'));

        // Filter by client.
        $clientId = (int) $this->getState('client_id');
        $query->where($db->quoteName('a.client_id') . ' = :clientid')
            ->bind(':clientid', $clientId, ParameterType::INTEGER);

        // Filter by enabled
        $query->where($db->quoteName('a.enabled') . ' = 1');

        // Add the list ordering clause.
        $query->order($db->escape($this->getState('list.ordering', 'a.ordering')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

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
        $id .= ':' . $this->getState('client_id');

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
    protected function populateState($ordering = null, $direction = null)
    {
        $app = Factory::getApplication();

        // Load the filter state.
        $clientId = $app->getUserStateFromRequest('com_advancedmodules.modules.client_id', 'client_id', 0);
        $this->setState('client_id', (int) $clientId);

        // Load the parameters.
        $params = ComponentHelper::getParams('com_advancedmodules');
        $this->setState('params', $params);

        // Manually set limits to get all modules.
        $this->setState('list.limit', 0);
        $this->setState('list.start', 0);
        $this->setState('list.ordering', 'a.name');
        $this->setState('list.direction', 'ASC');
    }
}

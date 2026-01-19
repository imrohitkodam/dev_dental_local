<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  (C) 2005 Open Source Matters, Inc. <https://www.joomla.org>
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace RegularLabs\Component\AdvancedModules\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Table\Table as JTable;
use Joomla\Database\DatabaseDriver as JDatabaseDriver;
use RegularLabs\Library\DB as RL_DB;

/**
 * Module table
 */
class AdvancedModule extends JTable
{
    protected $_autoincrement    = false;
    protected $_supportNullValue = true;

    /**
     * Constructor.
     *
     * @param JDatabaseDriver $db Database driver object.
     */
    public function __construct(JDatabaseDriver $db)
    {
        parent::__construct('#__advancedmodules', 'module_id', $db);

        $this->_autoincrement = false;
        $this->access         = (int) JFactory::getApplication()->get('access');
    }

    /**
     * Method to compute the default name of the asset.
     * The default name is in the form table_name.id
     * where id is the value of the primary key of the table.
     *
     * @return  string
     */
    protected function _getAssetName()
    {
        $k = $this->_tbl_key;

        return 'com_modules.module.' . (int) $this->$k;
    }

    /**
     * Method to get the parent asset id for the record
     *
     * @param JTable  $table A JTable object for the asset parent
     * @param integer $id
     *
     * @return  integer
     */
    protected function _getAssetParentId(?JTable $table = null, $id = null)
    {
        $db = RL_DB::get();

        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__assets')
            ->where('name = ' . $db->quote('com_modules'));
        $db->setQuery($query);
        $assetId = $db->loadResult();

        return $assetId ?: parent::_getAssetParentId($table, $id);
    }

    /**
     * @return  string
     */
    protected function _getAssetTitle()
    {
        if (isset($this->_title))
        {
            return $this->_title;
        }

        $k = (int) $this->_tbl_key;

        if (empty($this->{$k}))
        {
            return parent::_getAssetTitle();
        }

        $db = RL_DB::get();

        $query = $db->getQuery(true)
            ->select('title')
            ->from('#__modules')
            ->where('id = ' . (int) $this->{$k});
        $db->setQuery($query);

        return $db->loadResult();
    }
}

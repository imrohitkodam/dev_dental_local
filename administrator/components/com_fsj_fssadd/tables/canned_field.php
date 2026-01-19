<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport('joomla.database.tableasset');
jimport('joomla.database.table');


require_once (JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjcftype.php');

class JTablefsj_fssaddcanned_field extends JTable{
	public function __construct(&$db)
	{
		parent::__construct('#__fsj_fssadd_canned_field', 'id', $db);
	}

	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_fsj_fssadd.fssadd_canned_field.' . (int) $this->$k;
	}

	protected function _getAssetTitle()
	{
		return $this->title;
	}

	protected function _getAssetParentId(JTable $table = NULL, $id = NULL)
	{
		// Initialise variables.
		$assetId = null;


		// we dont have a parent, so need to look up the asset id from the parent table
		if ($assetId === null && $this->canned_id)
		{
			// Build the query to get the asset id for the parent.
			$query = $this->_db->getQuery(true);
			$query->select($this->_db->quoteName('asset_id'));
			$query->from($this->_db->quoteName('#__fsj_fssadd_canned'));
			$query->where($this->_db->quoteName('id') . ' = ' . (int) $this->canned_id);

			// Get the asset id from the database.
			$this->_db->setQuery($query);
			if ($result = $this->_db->loadResult())
			{
				$assetId = (int) $result;
			}
		}

		// if asset null, set to component
		if ($assetId === null)
		{
			// Build the query to get the asset id for the parent category.
			$query = $this->_db->getQuery(true);
			$query->select($this->_db->quoteName('id'));
			$query->from($this->_db->quoteName('#__assets'));
			$query->where($this->_db->quoteName('name') . ' = ' . $this->_db->quote('com_fsj_fssadd'));

			// Get the asset id from the database.
			$this->_db->setQuery($query);
			if ($result = $this->_db->loadResult())
			{
				$assetId = (int) $result;
			}
		}

		// Return the asset id.
		if ($assetId)
		{
			return $assetId;
		}
		else
		{
			return parent::_getAssetParentId($table, $id);
		}
	}
	/**
	 * Overloaded bind function
	 *
	 * @param   array  $array   Named array
	 * @param   mixed  $ignore  An optional array or space separated list of properties
	 * to ignore while binding.
	 *
	 * @return  mixed  Null if operation was satisfactory, otherwise returns an error string
	 *
	 * @see     JTable::bind
	 * @since   11.1
	 */
	public function bind($array, $ignore = '')
	{
		$this->array = $array;
		
		
		
		
		
		
		


		return parent::bind($array, $ignore);
	}

	/**
	 * Overloaded check function
	 *
	 * @return  boolean  True on success, false on failure
	 *
	 * @see     JTable::check
	 * @since   11.1
	 */
	public function check()
	{
		if (trim($this->title) == '')
		{
			$this->setError(JText::_('COM_CONTENT_WARNING_PROVIDE_VALID_NAME'));
			return false;
		}

		if (trim($this->alias) == '')
		{
			$this->alias = $this->title;
		}

		$this->alias = JApplication::stringURLSafe($this->alias);
		if (trim(str_replace('-', '', $this->alias)) == '')
		{
			$this->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
		}
				$this->alias = str_replace("-","_", $this->alias);
		


		/*if (trim(str_replace('&nbsp;', '', $this->fulltext)) == '')
		{
			$this->fulltext = '';
		}*/

		/*if (trim($this->introtext) == '' && trim($this->fulltext) == '')
		{
			$this->setError(JText::_('JGLOBAL_ARTICLE_MUST_HAVE_TEXT'));
			return false;
		}*/



		if (empty($this->ordering)) {
			// Set ordering to last if ordering was 0
			$this->ordering = self::getNextOrder();
		}


		return true;
	}

	/**
	 * Overrides JTable::store to set modified data and user id.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 */
	public function store($updateNulls = false)
	{
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		if ($this->id)
		{

		}
		else
		{
		}
		
			// Verify that the alias is unique
		$table = JTable::getInstance('canned_field', 'JTablefsj_fssadd');
		$alias = $this->alias;
		$tries = 2; 
				while ($table->load(array('alias' => $this->alias, 'canned_id' => $this->canned_id)) && ($table->id != $this->id || $this->id == 0))
		{
			$this->alias = $alias."-".$tries++;
		}
				$this->alias = str_replace("-","_", $this->alias);
			if (!parent::store($updateNulls))
			return false;
			
	
	JTable::addIncludePath(JPATH_LIBRARIES.DS.'fsj_core'.DS.'tables');
	
	
		$j_parentId = $this->_getAssetParentId();		
		$parsetting = JTable::getInstance('FSJSettings', 'JTable');
		$parsetting->loadByJAsset($j_parentId);
		$s_parentId = $parsetting->id;		


		$name = $this->_getAssetName();
		$title = $this->_getAssetTitle();
		
		$setting = JTable::getInstance('FSJSettings', 'JTable');
		$setting->loadByName($name);

		$setting->name = $name;
		$setting->title = $title;
		$setting->j_asset = $this->asset_id;
	
		// Check for an error.
		if ($error = $setting->getError())
		{
			$this->setError($error);
			return false;
		}

		// Specify how a new or moved node asset is inserted into the tree.
		if (empty($this->asset_id) || $setting->parent_id != $s_parentId)
		{
			$setting->setLocation($s_parentId, 'last-child');
			
			// need to lookup
		}


		$setting->check();
		$setting->store();


		return true;
	}


	public function delete($pk = null, $children = true)
	{
		$item = $this->load($pk);
			
		$db = JFactory::getDBO();
			

																																			
		// need to check for any cascades to delete, and call the correct table to delete them
		

		// delete actual item
		if (parent::delete($pk))
		{	
			$db = JFactory::getDBO();



																																	

			if (isset($this->asset_id))
			{
				FSJ_Settings::Delete($this->asset_id);
			}	

			return true;
		}

		return false;
		
	}
}

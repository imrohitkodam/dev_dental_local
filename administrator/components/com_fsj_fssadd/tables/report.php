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


require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_fssadd'.DS.'models'.DS.'fields'.DS.'fsjreportfield.php');
require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_fssadd'.DS.'models'.DS.'fields'.DS.'fsjreportfilter.php');

class JTablefsj_fssaddreport extends JTable{
	public function __construct(&$db)
	{
		parent::__construct('#__fsj_fssadd_report', 'id', $db);
	}

	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_fsj_fssadd.fssadd_report.' . (int) $this->$k;
	}

	protected function _getAssetTitle()
	{
		return $this->title;
	}

	protected function _getAssetParentId(JTable $table = NULL, $id = NULL)
	{
		// Initialise variables.
		$assetId = null;



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
		
		
      
        // MODIFY ALIAS TO MAKE SURE NO CONFLICTING FILES
        require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_fsj_fssadd'.DS.'helpers'.DS.'generatereport.php');
        $this->alias = FSS_Report_Generate::processAlias($this->alias);
      
    

		/*if (trim(str_replace('&nbsp;', '', $this->fulltext)) == '')
		{
			$this->fulltext = '';
		}*/

		/*if (trim($this->introtext) == '' && trim($this->fulltext) == '')
		{
			$this->setError(JText::_('JGLOBAL_ARTICLE_MUST_HAVE_TEXT'));
			return false;
		}*/





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
		$table = JTable::getInstance('report', 'JTablefsj_fssadd');
		$alias = $this->alias;
		$tries = 2; 
				while ($table->load(array('alias' => $this->alias)) && ($table->id != $this->id || $this->id == 0))
		{
			$this->alias = $alias."-".$tries++;
		}
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

	/**
	 * Method to set the publishing state for a row or list of rows in the database
	 * table. The method respects checked out rows by other users and will attempt
	 * to checkin rows that it can after adjustments are made.
	 *
	 * @param   mixed    $pks     An optional array of primary key values to update.  If not set the instance property value is used.
	 * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published]
	 * @param   integer  $userId  The user id of the user performing the operation.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 */
	public function publish($pks = null, $state = 1, $userId = 0)
	{	
		// Initialise variables.
		$k = $this->_tbl_key;

		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k)
			{
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else
			{
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				return false;
			}
		}

		// Build the WHERE clause for the primary keys.
		$where = $k . '=' . implode(' OR ' . $k . '=', $pks);

		// Determine if there is checkin support for the table.
		if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time'))
		{
			$checkin = '';
		}
		else
		{
			$checkin = ' AND (checked_out = 0 OR checked_out = ' . (int) $userId . ')';
		}

		// Get the JDatabaseQuery object
		$query = $this->_db->getQuery(true);

		// Update the publishing state for rows with the given primary keys.
		$query->update($this->_db->quoteName($this->_tbl));
		$query->set($this->_db->quoteName('state') . ' = ' . (int) $state);
		$query->where('(' . $where . ')' . $checkin);
		$this->_db->setQuery($query);
		$this->_db->execute();

		// Check for a database error.
		if ($this->_db->getErrorNum())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// If checkin is supported and all rows were adjusted, check them in.
		if ($checkin && (count($pks) == $this->_db->getAffectedRows()))
		{
			// Checkin the rows.
			foreach ($pks as $pk)
			{
				$this->checkin($pk);
			}
		}

		// If the JTable instance value is in the list of primary keys that were set, set the instance.
		if (in_array($this->$k, $pks))
		{
			$this->state = $state;
		}

		$this->setError('');

																	
		
      
        if (count($pks) > 0)
        {
            // GENERATE REPORT HERE IF PUBLISHED
            require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_fsj_fssadd'.DS.'helpers'.DS.'generatereport.php');
            foreach ($pks as $pk)
            {
                $report_object = FSS_Report_Generate::makeReportObject($pk);
                if ($state == 1)
                {
                     $report_object->publishReport();
                } else {
                     $report_object->removeReport();
                }    
            }
        }
      
    

		return true;
	}

	public function delete($pk = null, $children = true)
	{
		$item = $this->load($pk);
			
		$db = JFactory::getDBO();
			

																				
		// need to check for any cascades to delete, and call the correct table to delete them
		
		
      
        // DELETE THE REPORT FILE HERE!
        require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_fsj_fssadd'.DS.'helpers'.DS.'generatereport.php');
        $report_object = FSS_Report_Generate::makeReportObject($pk);
        $report_object->removeReport();
      
    
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

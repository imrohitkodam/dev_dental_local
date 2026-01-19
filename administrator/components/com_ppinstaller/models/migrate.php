<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_ppinstaller
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Import library dependencies
jimport('joomla.application.component.model');


/**
 * Extension Manager Install Model
 *
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @since		1.5
 */

if(!class_exists('PpinstallerModelAdapt')) {
	if(interface_exists('JModel')) {
		abstract class PpinstallerModelAdapt extends JModelLegacy {}
	} else {
		class PpinstallerModelAdapt extends JModel {}
	}
}

//XiTODO:: improve Funtion names
class PpinstallerModelMigrate extends PpinstallerModelAdapt
{
	//required joomla var
	protected $option		= 'com_ppinstaller';
	
	// migration variables
	protected $sql_file 	= null;
	protected $migrate_from = null ;
	protected $migrate_file = null ;
	protected $is_success 	= true;
	protected $query		= null;
	protected $start 		= 0;
	protected $limit 		= PPINSTALLER_CRITICAL_LIMIT;
	protected $action		= Array();
	protected $msg			= null;
	
	
	function __construct($config=Array())
	{
		$this->start = JRequest::getVar('start',0);
		$before 	 = JText::_('COM_PPINSTALLER_BEFORE_MIGRATION');
		$after 		 = JText::_('COM_PPINSTALLER_AFTER_MIGRATION');
		$this->action 	= array_merge(array('before'=>$before),$this->action,array('after'=>$after));
		$config['name'] = __CLASS__;
		parent::__construct($config);
		
	}
	
	public function nextMigrateAction($currentAction)
	{
		if(!$this->is_success ){ return 'halt';}
			
		$next_action = null;
		$start_from = 0;
		if(!empty($this->query)){
			$db = JFactory::getDbo();
			$db->setQuery($this->query);
			$total	 = count($db->loadColumn());
			$from 	 = $this->start;  
			$end   	 = $this->start+$this->limit;	
			if($end < $total){
				$start_from = $end;
				$next_action = $currentAction;
			}else{
				$end = $total;
			}
			$this->msg = JText::sprintf('COM_PPINSTALLER_MIGRATION_LIMIT',$from,$end,$total);
		}
				
		if(empty($next_action)){
			//$start_from = 0;
			$keys 	  = array_keys($this->action);
			$position = array_search($currentAction, $keys);
			$position = (false === $position )? 0 : $position+1;
			$next_action = isset($keys[$position]) ? $keys[$position] : false; 
		}
		
		JRequest::setVar('start', $start_from);
		return array($next_action,$start_from);
	}
	
	/**
	 * Load a list of database objects
	 * If <var>key</var> is not empty then the returned array is indexed by the value
	 * the database key.  Returns <var>null</var> if the query fails.
	 * @param $table : table name 
	 * @param $key 
	 * @param $filters : where conditions Array('value'=>'column_name')
	 * @param $operator : OR/AND
	 */
	
	protected function loadData($table,$key = '', $filter = Array(), $operator = 'AND')
	{
		$db = JFactory::getDbo();
		$query = " SELECT * FROM `$table`";
		if(!empty($filter)){
			$query .= ' WHERE ';
			if(is_string($filter)){
				$query .= $filter;				
			}
			else {
				foreach ( $filter as $value => $column_name){
					$query .= (is_string($value))? " `$column_name`='$value'":" `$column_name`=$value";
					$query .= " $operator " ;
				}
				// XiTODO::In case of OR operator it will be misguiding
				$query .= 1;	
			}
		}
		$db->setQuery($query);
		return $db->loadObjectList($key);
	}
	
	public function before() 
	{
		
 		$msg 			  = JText::_('COM_PPINSTALLER_MIGRATION_START');
 		PpinstallerHelperMigrate::setKeyValue('migration_status',PPINSTALLER_MIGRATION_START);		
 		$this->msg 	      = $msg;
		return array();
	}
	
	public function after() 
	{
		PpinstallerHelperMigrate::setKeyValue('migration_status',PPINSTALLER_MIGRATION_SUCCESS);
		
		$returnInfo = Array();
		$this->msg = JText::sprintf('COM_PPINSTALLER_MIGRATION_DONE','PayPlans');
		return $returnInfo;
	}
	
	public function halt()
	{
		//XiTODO:: Stop Migration process
		$returnInfo = Array();
		$returnInfo['migrateAction'] = false;
		$this->msg = 'COM_PPINSTALLER_HALT_MIGRATION';
		return $returnInfo;
		;
	}
}

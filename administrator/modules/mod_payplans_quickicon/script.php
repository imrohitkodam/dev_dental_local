<?php
/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package		PayPlans
* @subpackage	quick-icon
* @contact 		support+payplans@readybytes.in
*/
if(defined('_JEXEC')===false) die();

class mod_payplans_quickiconInstallerScript 
{
    public function postflight($type, $parent)
	{
		$type = strtolower($type);
		if ($type == 'install' || $type == 'update'){
			$result = $this->updatePositionAndPublish();
			
			if ($result){
				$this->updateModulesMenu();
			}
		}
		
		return true;
	}
	
	protected function updatePositionAndPublish()
	{
		$newState	= 1;
		$position 	= 'icon';
		$name		= 'mod_payplans_quickicon';

		$status 	= 1;
		
		$db		= JFactory::getDBO();		        
		$query  = $db->getQuery(true);

		$query->update($db->quoteName('#__modules'))
				->set($db->quoteName('published') . ' = ' . $db->quote($status))
				->set($db->quoteName('position') . ' = ' . $db->quote($position))
				->where($db->quoteName('module') . ' = ' . $db->quote($name));

		$db->setQuery($query);	
		if (!$db->execute()){
			return false;
		}
		
		return true;
	}
	
	protected function updateModulesMenu()
	{
		$name	= 'mod_payplans_quickicon';
		$db		= JFactory::getDBO();		        
		$query  = $db->getQuery(true);
		
		//get the module-id
		$query->select('id')
			  ->from('#__modules')
			  ->where($db->quoteName('module') . ' = ' . $db->quote($name));
			  
		$db->setQuery($query);
		$module_id = $db->loadResult();
		
		if ($module_id){
			//check whether any entry exists for this module-id in modules-menu table 
			$query->clear()
				  ->select('moduleid')
				  ->from('#__modules_menu')
				  ->where($db->quoteName('moduleid') . ' = '. $db->quote($module_id));
				  
			$db->setQuery($query);
			$result = $db->loadResult();
			
			//if no entry exists in modules_menu table then create 
			if (!$result){
				$query->clear()
	 				  ->insert('#__modules_menu')
				      ->columns(array($db->quoteName('moduleid'), $db->quoteName('menuid')))
				      ->values($module_id . ', 0');
				$db->setQuery($query);
				$db->execute();
			}
		}
	}
}

<?php
/**
* @copyright	Copyright (C) 2009 - 2009 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package		PayPlans
* @subpackage	Frontend
* @contact 		shyam@readybytes.in
*/
if(defined('_JEXEC')===false) die();

//Before unintall, payplans system plugin is disabled so need to redefine it.
if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

class Com_payplansInstallerScript
{
	public function preflight($type, $parent)
	{
		if($type == 'install' || $type == 'update'){
			self::_deleteAdminMenu();
		}

		if($type == 'uninstall'){
			require_once dirname(__FILE__).DS.'installer'.DS.'installer.php';
	
			$installer	= new PayplansInstaller();
			return $installer->uninstall();	
		}
	}

	/**
	 * Joomla! 1.6+ bugfix for "Can not build admin menus"
	 */
	function _deleteAdminMenu()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		//get all records
		$query->delete('#__menu')
			  ->where($db->quoteName('type').' = '.$db->quote('component'))
			  ->where($db->quoteName('menutype').' = '.$db->quote('main'))
			  ->where($db->quoteName('link').' LIKE '.$db->quote('index.php?option=com_payplans'));
	
		return $db->setQuery($query)->query();
	}
	
	public function postflight($type, $parent)
	{
		if ($type == 'install' || $type == 'update'){
			require_once dirname(__FILE__).DS.'admin'.DS.'installer'.DS.'installer.php';
			
			$installer	= new PayplansInstaller();
			$installer->installExtensions();
			return true;
		}
	}
}

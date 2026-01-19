<?php
/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package		PayPlans
* @subpackage	offlinepay
* @contact 		support+payplans@readybytes.in
*/
if(defined('_JEXEC')===false) die();

class plgPayplansOfflinepayInstallerScript 
{
    public function postflight($type, $parent)
	{
		if ($type == 'install' || $type == 'update'){
			$name 		= 'offlinepay';
			$folder 	= 'payplans';
			$status 	= 1;
			
			$db		= JFactory::getDBO();		        
			$query  = $db->getQuery(true);

			$query->update($db->quoteName('#__extensions'))
					->set($db->quoteName('enabled') . ' = ' . $db->quote($status))
					->where($db->quoteName('folder') . ' = ' . $db->quote($folder) , 'AND')
					->where($db->quoteName('type') . ' = ' . $db->quote('plugin') , 'AND')
					->where($db->quoteName('element') . ' = ' . $db->quote($name) , 'AND');

			$db->setQuery($query);			
			if(!$db->execute())
				return false;
		}
		
		return true;
	}
}

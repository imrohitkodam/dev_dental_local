<?php
/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package		PayPlans
* @subpackage	adminpay
* @contact 		support+payplans@readybytes.in
*/
if(defined('_JEXEC')===false) die();

class plgPayplansAdminpayInstallerScript 
{
    public function postflight($type, $parent)
	{
		if ($type == 'install' || $type == 'update'){
			$name 		= 'adminpay';
			$folder 	= 'payplans';
			$status 	= 1;
			
			$db		= JFactory::getDBO();		        
			$query  = $db->getQuery(true);

			$query->update($db->quoteName('#__extensions'))
					->set($db->quoteName('enabled') . ' = ' . $db->quote($status))
					->where($db->quoteName('folder') . ' = ' . $db->quote($folder) , 'AND')
					->where($db->quoteName('element') . ' = ' . $db->quote($name) , 'AND')
					->where($db->quoteName('type') . ' = ' . $db->quote('plugin') , 'AND');

			$db->setQuery($query);			

			if(!$db->execute())
				return false;
		
			// check app instance exists or not and if does not exists then create
			$query = $db->getQuery(true)
						->select('app_id')
						->from('#__payplans_app')
						->where(' `type` = "adminpay" ');
					
			$result = $db->setQuery($query)
						 ->loadObjectList();
						
			if (empty($result)){
				$sql = $db->getQuery(true)
						  ->insert($db->quoteName('#__payplans_app'))
						  ->columns($db->quoteName(array('title', 'type', 'description', 'core_params', 'app_params', 'ordering', 'published')))
						  ->values(	$db->quote('Admin Payment') .', '
									. $db->quote('adminpay') .', ' 
									. $db->quote('Through this application Admin can create payment from back-end. There is no either way to create payment from back-end. This application can not be created, changed and deleted. And can not be used for fron-end payment.').', '
									. $db->quote('{"applyAll":"1"}').', ' 
									. $db->quote('').', '
									. '1, 1'
								);

				$db->setQuery($sql);
				$db->execute();
			}
		}

		return true;
	}
}

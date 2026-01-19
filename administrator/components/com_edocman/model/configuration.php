<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Edocman Component Configuration Model
 *
 * @package        Joomla
 * @subpackage     EDocman
 */
class EDocmanModelConfiguration extends OSModel
{
	/**
	 * Store the configuration data
	 *
	 * @param array $data
	 */
	function store($data)
	{		
		$db = $this->getDbo();
		$db->truncateTable('#__edocman_configs');		
		$row = $this->getTable();
		foreach ($data as $key => $value)
		{
			$row->id = 0;
			$row->config_key = $key;
			$row->config_value = $value;
			if (!$row->store())
			{
				return false;
			}				
		}						
		return true;
	}
}
<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die();

class EdocmanControllerLicense extends EDocmanController
{	
	function active_default(){
		$id    = $this->input->getInt('id', 0);
		$state = $this->input->getInt('state',0);
		$db    = JFactory::getDbo();
		if($id > 0){
			$db->setQuery("Update #__edocman_licenses set default_license = '0'");
			$db->query();
			$db->setQuery("Update #__edocman_licenses set default_license = '$state' where id = '$id'");
			$db->query();
		}
		$this->app->enqueueMessage(JText::_('EDOCMAN_DEFAULT_LICENSE_HAS_BEEN_UPDATED'));
		$this->app->redirect('index.php?option=com_edocman&view=licenses');
	}

}
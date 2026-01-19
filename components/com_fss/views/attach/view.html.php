<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @deprecated 299c88a7cd75a201b053616945988036
**/
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view');
require_once (JPATH_SITE.DS.'components'.DS.'com_fss'.DS.'views'.DS.'attach'.DS.'attach_handler.php');

class FssViewAttach extends FSSView
{
	function display($tpl = null)
    {
		$task = JRequest::getVar('task');
		if ($task == "process")
			return $this->process();
    }
	
	function process()
	{
		$upload_handler = new FSS_Attach_Handler();
		exit;
	}
}


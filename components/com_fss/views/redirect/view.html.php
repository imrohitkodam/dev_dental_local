<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @deprecated 98bd8140a751549fe267d948b1f3f249
**/
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view');
require_once (JPATH_SITE.DS.'components'.DS.'com_fss'.DS.'models'.DS.'admin.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_fss'.DS.'helper'.DS.'comments.php');


class fssViewredirect extends FSSView
{
    function display($tpl = null)
    {
		JFactory::getApplication()->redirect(FSSRoute::_("index.php?option=com_fss&view=main", false));
		return;
    }
	
}


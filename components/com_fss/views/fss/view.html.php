<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @deprecated a32638922478298c4359d966a75cc0c5
**/
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view');
jimport('joomla.utilities.date');

class FssViewFss extends FSSView
{
    function display($tpl = null)
    {
        $mainframe = JFactory::getApplication();
		$option = FSS_Input::getCmd('option');
		if ($option == "com_fst")
		{
			$link = FSSRoute::_('index.php?option=com_fss&view=test',false);
		} else if ($option == "com_fsf")
		{
			$link = FSSRoute::_('index.php?option=com_fss&view=faq',false);
		} else {
			$link = FSSRoute::_('index.php?option=com_fss&view=main',false);
		}
		$mainframe->redirect($link);
    }
}


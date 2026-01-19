<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @deprecated 7335bba6110ca7043a38af45e304f7c7
**/
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view');

class FssViewCsstest extends FSSView
{
	function display($tpl = null)
	{
		$type = FSS_Input::getCmd('type');
		
		if ($type)
			return parent::display($type);
		
		parent::display();	
	}
}

<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @deprecated cab25bddaf63c50e0889628e39f259de
**/
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view');
require_once( JPATH_SITE.DS.'components'.DS.'com_fss'.DS.'helper'.DS.'cron.php' );

class FssViewCron extends FSSView
{
	function display($tpl = null)
    {
		FSS_Helper::noBots();
		FSS_Helper::noCache();
		
		FSS_Cron_Helper::runCron(FSS_Input::getInt('test'));
		
		exit;
	}
}

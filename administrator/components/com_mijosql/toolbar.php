<?php
/**
* @package		MijoSQL
* @copyright	2009-2012 Mijosoft LLC, www.mijosoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT.'/helpers/helper.php');

JHTML::_('behavior.switcher');

if (JRequest::getInt('hidemainmenu') != 1) {
	JSubMenuHelper::addEntry(JText::_('COM_MIJOSQL_RUN_QUERY'), 'index.php?option=com_mijosql', MijosqlHelper::isActiveSubMenu('query'));
	JSubMenuHelper::addEntry(JText::_('COM_MIJOSQL_SAVED_QUERIES'), 'index.php?option=com_mijosql&controller=queries', MijosqlHelper::isActiveSubMenu('queries'));
}
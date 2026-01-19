<?php
/**
* @package		MijoSQL
* @copyright	2009-2012 Mijosoft LLC, www.mijosoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

if (version_compare(JVERSION,'1.6.0','ge')) {
	if (!JFactory::getUser()->authorise('core.manage', 'com_mijosql')) {
		return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
	}
}

require_once(JPATH_COMPONENT.'/mvc/model.php');
require_once(JPATH_COMPONENT.'/mvc/view.php');
require_once(JPATH_COMPONENT.'/mvc/controller.php');

require_once(JPATH_COMPONENT.'/toolbar.php');
require_once(JPATH_COMPONENT.'/helpers/helper.php');

JTable::addIncludePath(JPATH_COMPONENT.'/tables');

if ($controller = JRequest::getWord('controller')) {
	$path = JPATH_COMPONENT.'/controllers/'.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	}
	else {
		$controller = '';
	}
}

$classname = 'MijosqlController'.ucfirst($controller);

$controller = new $classname();

$controller->execute(JRequest::getCmd('task'));
$controller->redirect();

echo '<div style="margin: 10px; text-align: center;"><a href="http://www.mijosoft.com/joomla-extensions/mijosql" target="_blank">MijoSQL | Copyright &copy; 2009-2012 Mijosoft LLC</a></div>';

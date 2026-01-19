<?php
/**
* @package      StackIdeas
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* StackIdeas Toolbar is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

// Load toolbar engine
require_once(__DIR__ . '/includes/toolbar.php');

class modStackideasToolbarHelper
{
	public static function actionAjax()
	{
		$input = JFactory::getApplication()->input;
		$type = $input->get('type', '', 'string');
		$currentView = $input->get('view', '', 'string');
		$moduleId = $input->get('moduleId', 0, 'int');

		// We need to get the module manually instead of relying on Joomla because the ajax link is com_ajax and when the menu assignment is selected to a specific component, we can't get the module that we want so we have to get it from the db manually. #174
		$module = self::getModuleById($moduleId);

		if ($module) {
			$params = new JRegistry($module->params);

			FDT::setConfig($params);
		}

		return FDT::themes()->html('ajax.' . $type);
	}

	public static function pollingAjax()
	{
		return FDT::themes()->html('ajax.poll');
	}

	public static function setAllReadAjax()
	{
		return FDT::themes()->html('ajax.setAllReadAjax');
	}

	public static function friendRequestAjax()
	{
		$input = JFactory::getApplication()->input;
		$action = $input->get('action', '');
		$id = $input->get('id', '');
		$namespace = 'ajax.friend' . ucfirst($action);

		return FDT::themes()->html($namespace, $id);
	}

	public static function searchAjax()
	{
		return FDT::themes()->html('ajax.search');
	}

	public static function getModuleById($id)
	{
		$db = JFactory::getDbo();
		$query = 'SELECT * FROM `#__modules` WHERE `id` = ' . $db->quote($id);

		$db->setQuery($query);
		$result = $db->loadObject();

		return $result;
	}

	public static function dialogAjax()
	{
		$input = JFactory::getApplication()->input;
		$moduleId = $input->get('moduleId', 0, 'int');

		// We need to get the module manually instead of relying on Joomla because the ajax link is com_ajax and when the menu assignment is selected to a specific component, we can't get the module that we want so we have to get it from the db manually. #174
		$module = self::getModuleById($moduleId);

		if ($module) {
			$params = new JRegistry($module->params);

			FDT::setConfig($params);
		}

		return FDT::themes()->html('ajax.dialog');
	}
}
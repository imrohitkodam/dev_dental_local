<?php
/**
 * @version    SVN: <svn_id>
 * @package    Plg_System_Tjlms
 * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access.
defined('_JEXEC') or die( 'Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.html.parameter');
jimport('joomla.plugin.plugin');

// Load language file for plugin.
$lang = JFactory::getLanguage();
$lang->load('tjlmsuserlog', JPATH_ADMINISTRATOR);

/**
 * Methods supporting a list of Tjlms action.
 *
 * @since  1.0.0
 */
class PlgUsertjlmsuserlog extends JPlugin
{
	/**
	 * Function used as a trigger after User login
	 *
	 * @param   MIXED  $user     user ID
	 * @param   MIXED  $options  Options available
	 *
	 * @return  boolean true or false
	 *
	 * @since  1.0.0
	 */
	public function onUserLogin($user, $options)
	{
		$db	= JFactory::getDBO();
		$query = "select id from #__users where email = '" . $user['email'] . "'";
		$db->setQuery($query);
		$user_id = $db->loadResult();
		$action = "LOGIN";
		$this->addActivity($user_id, $action);

		return true;
	}

	/**
	 * Function used as a trigger after User Logout
	 *
	 * @param   MIXED  $user     user ID
	 * @param   MIXED  $options  Options available
	 *
	 * @return  boolean true or false
	 *
	 * @since  1.0.0
	 */
	public function onUserLogout($user, $options)
	{
		$db	= JFactory::getDBO();
		$data = new stdClass;
		$user_id = $user['id'];
		$action = "LOGOUT";
		$this->addActivity($user_id, $action);

		return true;
	}

	/**
	 * Function used  add the Login and logout activity in TjLms
	 *
	 * @param   MIXED  $user_id  user ID
	 * @param   MIXED  $action   Action to be logged
	 *
	 * @return  boolean true or false
	 *
	 * @since  1.0.0
	 */
	public function addActivity($user_id, $action)
	{
		$path = JPATH_SITE . '/components/com_tjlms/helpers/main.php';

		if (JFile::exists($path))
		{
			if (!class_exists('comtjlmsHelper'))
			{
				JLoader::register('comtjlmsHelper', $path);
				JLoader::load('comtjlmsHelper');
			}

			$comtjlmsHelperObj	= new comtjlmsHelper;
			$comtjlmsHelperObj->addActivity($user_id, $action);
		}

		return true;
	}
}

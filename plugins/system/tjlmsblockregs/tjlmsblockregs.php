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

jimport('joomla.html.parameter');
jimport('joomla.plugin.plugin');

// Load language file for plugin.
$lang = JFactory::getLanguage();
$lang->load('tjlmsblockregs', JPATH_ADMINISTRATOR);

/**
 * Methods supporting a list of Tjlms action.
 *
 * @since  1.0.0
 */
class PlgSystemtjlmsblockregs extends JPlugin
{
	/**
	 * Function used as a trigger after User login
	 *
	 * @param   MIXED  $oldUser  old user
	 * @param   MIXED  $isnew    isnew
	 * @param   MIXED  $newUser  newUser
	 *
	 * @return  boolean true or false
	 *
	 * @since  1.0.0
	 */
	public function onUserBeforeSave($oldUser, $isnew, $newUser)
	{
		$app    = JFactory::getApplication();

		if ($isnew)
		{
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('count(u.id)');
			$query->from('`#__users` AS u');

			if ($this->params->get('consider_only_published_users', '1', 'INT') == 1)
			{
				$query->where("u.block = 0");
			}

			$db->setQuery($query);
			$usersCnt = $db->loadResult();

			if ($usersCnt >= $this->params->get('allowed_user_registrations', '5000', 'INT'))
			{
				$app->enqueueMessage(JText::_('PLG_SYSTEM_TJLMS_BLCOKREG_REGISTRAION_NOT_ALLOWED_MESSGAE'), 'error');

				return false;
			}
		}
	}
}

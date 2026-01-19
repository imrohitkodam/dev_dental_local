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
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;

// Load language file for plugin.
$lang = Factory::getLanguage();
$lang->load('plg_system_tjlms_dental', JPATH_ADMINISTRATOR);
$lang->load('com_tjlms', JPATH_SITE);

/**
 * Methods supporting a list of Tjlms action.
 *
 * @since  1.0.0
 */
class PlgSystemTjlms_Dental extends CMSPlugin
{
	/**
	 * Constructor - Function used as a contructor
	 *
	 * @param   object  $subject  The object to observe
	 * @param   array   $config   An array that holds the plugin configuration
	 *
	 * @retunr  class object
	 *
	 * @since  1.0.0
	 */
	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 * Function used as a trigger after order deleted
	 *
	 * @param   ARRAY  $orderIds  order IDs
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function onAfterOrderDelete($orderIds)
	{
		$db = Factory::getDbo();

		// Delete items
		$query = $db->getQuery(true);

		// Delete all enrollment as selected
		$conditions = array(
			$db->quoteName('tjlms_order_id') . ' IN ( ' . $orderIds . ' )',
		);

		$query->delete($db->quoteName('#__tjlms_payplanApp'));
		$query->where($conditions);

		$db->setQuery($query);

		if (!$db->execute())
		{
			$this->setError($db->getErrorMsg());

			return false;
		}

		// For now orderIds is accepted
		return true;
	}

	/**
	 * Call function after click on sync button
	 * Sync data payplan & shika
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function onAjaxlaunchEventData()
	{
		$app           = Factory::getApplication();
		$jinput        = $app->getInput();
		$eventID       = $jinput->get('eventID');

		$eventdata     = new stdClass;
		$eventdata->id = $eventID;

		$dispatcher    = $app->getDispatcher();
		PluginHelper::importPlugin('tjevents');

		return $result        = $dispatcher->trigger('updateLessonTrack', array(Factory::getUser()->id, $eventdata));
	}
}

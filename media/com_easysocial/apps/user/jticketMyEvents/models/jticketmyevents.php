<?php
/**
 * @version    SVN: <svn_id>
 * @package    Quick2cart
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */
defined('_JEXEC') or die();
use Joomla\CMS\Factory;
Foundry::import('admin:/includes/model');
/**
 * Model for Events
 *
 * @package     JTicketing
 * @subpackage  component
 * @since       1.0
 */
class JticketMyEventsModel extends EasySocialModel
{
	/**
	 * Get Event list
	 *
	 * @param   integer  $userId       userId id
	 * @param   integer  $limit        limit id
	 * @param   integer  $category_id  category_id
	 *
	 * @return  object event list
	 *
	 * @since   1.0
	 */
	public function getItems($userId, $limit = 0, $category_id = '')
	{
		require_once JPATH_SITE . '/components/com_jticketing/helpers/main.php';
		require_once JPATH_SITE . '/components/com_jticketing/models/events.php';
		$app = Factory::getApplication();
		$app->input->set('filter_creator', $userId);
		$app->input->set('filter_events_cat', $category_id);
		$JticketingModelEvents = new JticketingModelEvents;
		$db                    = $this->getDbo();
		$query                = $db->getQuery(true);
		$query                 = $JticketingModelEvents->getListQuery();

		if ($limit)
		{
			$query .= ' limit ' . $limit;
		}

		$db->setQuery($query);

		return $results = $db->LoadObjectList();
	}

	/**
	 * Get Event count
	 *
	 * @param   integer  $userId       userId id
	 * @param   integer  $category_id  category_id
	 *
	 * @return  int  event total count
	 *
	 * @since   1.0
	 */
	public function getEventsCount($userId, $category_id = '')
	{
		$db = $this->getDbo();
		require_once JPATH_SITE . '/components/com_jticketing/helpers/main.php';
		require_once JPATH_SITE . '/components/com_jticketing/models/events.php';
		$app = Factory::getApplication();
		$app->input->set('filter_events_cat', '');
		$query_total           = $db->getQuery(true);
		$JticketingModelEvents = new JticketingModelEvents;
		$query_total           = $JticketingModelEvents->getListQuery();
		$db->setQuery($query_total);
		$events_total_data = $db->LoadObjectList();

		return $event_count = count($events_total_data);
	}
}

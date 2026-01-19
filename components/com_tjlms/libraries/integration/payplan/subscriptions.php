<?php
/**
 * @package    Com_Tjlms_Payplans_Integration_Library
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (C) 2009 - 2018 Techjoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */


// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Payplans Subscriptions
 *
 * @since  1.0
 */
class ComtjlmsSubscriptions
{
	/**
	 * Get payplans Subscriptions
	 *
	 * @param   INT  $courseId  course id
	 * @param   INT  $userId    user id
	 *
	 * @return  MIX  subscriptionData string for the button
	 *
	 * @since   1.0
	 */
	public static function getSubscription($courseId, $userId)
	{
		$db = JFactory::getDBO();
		$subscriptionData = array();
		$payplansPlan = array();

		$query = $db->getQuery(true);
		$query->select('*, p.title AS plan_title');
		$query->from('`#__payplans_planapp` AS pa');
		$query->join('LEFT', '`#__payplans_plan` AS p ON p.plan_id = pa.plan_id');
		$query->join('LEFT', '`#__payplans_app` AS a ON a.app_id = pa.app_id');
		$query->where('a.type = "tjlms"');
		$query->where('a.published = 1');
		$query->where('p.published = 1');
		$db->setQuery($query);
		$planApps = $db->loadObjectList();

		require_once JPATH_SITE . '/components/com_payplans/helpers/plan.php';
		require_once JPATH_SITE . '/components/com_payplans/helpers/format.php';

		foreach ($planApps as $planAppData)
		{
			$decodeCoursePlans = json_decode($planAppData->app_params);
			$decodePlanDetails = json_decode($planAppData->details);

			foreach ($decodeCoursePlans as $tjlms_plans)
			{
				if (!empty($tjlms_plans))
				{
					$coursePlanIds = implode(", ", $tjlms_plans);

					$query = $db->getQuery(true);
					$query->select('course_id');
					$query->from('`#__tjlms_subscription_plans` AS sp');
					$query->where('sp.id IN (' . $coursePlanIds . ')');
					$db->setQuery($query);
					$courseIds = $db->loadColumn();

					if (in_array($courseId, $courseIds))
					{
						$payplansPlan['planAppData'] = $planAppData;
						$payplansPlan['decodePlanDetails'] = $decodePlanDetails;

						array_push($subscriptionData, $payplansPlan);
					}
				}
			}
		}

		return $subscriptionData;
	}

	/**
	 * Get Payplans Subscriptions details
	 *
	 * @param   INT  $courseId  course id
	 * @param   INT  $userId    user id
	 *
	 * @return  MIX  subscriptiondetails
	 *
	 * @since   1.0
	 */
	public static function getSubscriptionDetails($courseId, $userId)
	{
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->select('*, p.title AS plan_title');
		$query->from('`#__tjlms_payplanApp` AS pa');
		$query->join('LEFT', '`#__payplans_subscription` AS s ON s.subscription_id = pa.pp_subscription_id');
		$query->join('LEFT', '`#__payplans_plan` AS p ON p.plan_id = s.plan_id');
		$query->where('pa.course_id = ' . $courseId);
		$query->where('pa.user_id = ' . $userId);
		$query->where('p.published = 1');
		$query->where('s.status = ' . PayplansStatus::SUBSCRIPTION_ACTIVE);
		$query->order('pa.id DESC');

		$db->setQuery($query);
		$planDetails = $db->loadObject();

		return $planDetails;
	}
}

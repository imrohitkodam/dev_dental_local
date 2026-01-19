
<?php
/**
 * @version    SVN: <svn_id>
 * @package    Payplan_Tjlms
 * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die('Restricted access');


/**
 * Controller
 *
 * @since  1.6
 */
class TjlmsControllerCreateOrder extends tjlmsController
{
	/**
	 * Call function after click on sync button
	 * Sync data payplan & shika
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function onAjaxsyncData()
	{
		$current_date = JHtml::date($input = 'now', 'Y-m-d H:i:s', false);
		$jinput       = JFactory::getApplication()->input;

		$appId   = $jinput->get('appId');

		echo 'Processing app ' . $appId . '<br/>';

		$db           = JFactory::getDBO();
		$query        = $db->getQuery(true);

		$query->select('s.id AS sync_id, s.app_id, p.plan_id as payplan_id, s.old_shika_course_plan, s.new_shika_course_plan');
		$query->from('`#__payplans_app` AS a');
		$query->leftJoin('`#__payplans_planapp` AS pa ON pa.`app_id` = a.`app_id`');
		$query->leftJoin('`#__payplans_plan` AS p ON p.`plan_id` = pa.`plan_id`');
		$query->leftJoin('`#__payplans_tjlms_plan_sync` AS s ON s.`app_id` = a.`app_id`');
		$query->where('s.sync_status = 0');
		$query->where('p.published = 1');
		$query->where('a.published = 1');
		$query->where('a.type = "tjlms"');
		$query->where('pa.app_id =' . (int) $appId);

		$db->setQuery($query);

		$app_data = $db->loadObject();

		if ($app_data)
		{
			$sync_id               = $app_data->sync_id;
			$appId                 = $app_data->app_id;
			$payplan_id            = $app_data->payplan_id;
			$old_shika_course_plan = json_decode($app_data->old_shika_course_plan);
			$new_shika_course_plan = json_decode($app_data->new_shika_course_plan);

			$subscriberIds         = array_unique($this->renderPayplansPlanSubscriber($payplan_id));

			$remove_plan           = array_diff($old_shika_course_plan, $new_shika_course_plan);

			$extra_added_plan      = array_diff($new_shika_course_plan, $old_shika_course_plan);

			if ($subscriberIds)
			{
				$mainArray = array();

				foreach ($subscriberIds as $userid)
				{
					echo 'Processing user id ' . $userid . '<br/>';
					$payplan_details = $this->renderPayplansDetails($payplan_id, $userid);

					if ($payplan_details)
					{
						foreach($payplan_details as $plan_details)
						{
							if ($extra_added_plan)
							{
								foreach($extra_added_plan as $course)
								{
									$xrefpl                     = new stdClass;
									$xrefpl->pp_order_id        = $plan_details->order_id;
									$xrefpl->pp_invoice_id      = $plan_details->invoice_id;
									$xrefpl->pp_subscription_id = $plan_details->subscription_id;
									$xrefpl->user_id            = $plan_details->subscription_user;
									$xrefpl->pp_app_id          = $appId;

									$coursePlans = $this->renderCoursePlanId($course);
									echo 'Processing course ' . $coursePlans[0]->course_id . '<br/>';
									$order = $this->getOrder($coursePlans[0]->course_id, $userid);

									if($order->cdate >= $plan_details->subscription_date && $order->cdate <= $plan_details->expiration_date)
									{
										if($data['order']->status != 'C')
										{
											$this->onCreateOrder($userid, $plan_details->invoicestatus, $plan_details->subscription_date, $coursePlans, $xrefpl);
										}
									}
									else
									{
										$this->onCreateOrder($userid, $plan_details->invoicestatus, $plan_details->subscription_date, $coursePlans, $xrefpl);
									}
								}
							}
						}
					}
				}
			}
		}

	}

	/**
	 * Render Payplans Plan Subscriber id's
	 *
	 * @param   Interger  $planId  payplan plan id.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function renderPayplansPlanSubscriber($planId)
	{
		$query = new XiQuery;
		/*
			0    - No-Status
			1601 - Subscription - Active
			1601 - Subscription - Hold
			1601 - Subscription - Expired
		*/

		$users = $query->select("a.user_id")
					->from('`#__payplans_subscription` as a')
					->where('a.plan_id = ' . $planId)
					->where('a.status = 1601')
					->dbLoadQuery()
					->loadColumn();

		return $users;
	}

	/**
	 * Render Payplans Plan Details
	 *
	 * @param   Interger  $planId  payplan plan id.
	 * @param   Interger  $userid  payplan subscription user id.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function renderPayplansDetails($planId, $userid)
	{
		if ($planId)
		{
			$db           = JFactory::getDBO();
			$query        = $db->getQuery(true);
			$selectList   = 'o.order_id, s.*,';
			$selectList  .= 'i.invoice_id';
			$query->select($selectList);
			$query->from('`#__payplans_subscription` AS s');
			$query->leftJoin('`#__payplans_order` AS o ON o.`order_id` = s.`order_id`');
			$query->leftJoin('`#__payplans_invoice` AS i ON i.`object_id` = s.`order_id`');
			$query->where('s.plan_id = ' . $planId);
			$query->where('s.user_id = ' . $userid);

			$db->setQuery($query);

			return $plan_details = $db->loadObjectList();
		}

		return 0;
	}

	public function getOrder($courseId, $useId)
	{
		$db           = JFactory::getDBO();
		$query        = $db->getQuery(true);

		if($courseId)
		{
			$db           = JFactory::getDBO();
			$query        = $db->getQuery(true);

			$query->select('*');
			$query->from('#__tjlms_orders');
			$query->where('course_id = ' . $courseId);
			$query->where('user_id = ' . $useId);

			$db->setQuery($query);
			return $data = $db->loadObject();
		}
	}

	public function onCreateOrder($userId, $newStatus, $date, $course_Plans, $xrefpl)
	{
		echo 'Processing creating order ' . '<br/>';
		$xrefplArray = $xrefpl;

		//$userId, $coursePlans, $newStatus, $xrefplArray, $current_date
		require_once JPATH_SITE . '/components/com_tjlms/helpers/courses.php';
		$this->TjlmsCoursesHelper = new TjlmsCoursesHelper;

		jimport('joomla.application.component.model');
		require_once JPATH_SITE . '/components/com_tjlms/models/buy.php';
		$this->TjlmsModelbuy = new TjlmsModelbuy;

		require_once JPATH_SITE . '/components/com_tjlms/models/payment.php';
		$this->TjlmsModelpayment = new TjlmsModelpayment;

		$query = new XiQuery;


		$query = new XiQuery;
		$CourseOrderId = $query->select("a.*")
					->from('`#__tjlms_payplanApp` as a')
					->rightJoin('`#__tjlms_orders` AS o ON a.`tjlms_order_id` = o.`id`')
					->where('a.pp_order_id = ' . (int) $xrefplArray->pp_order_id)
					->where('a.course_id = ' . (int) $course_Plans[0]->course_id)
					->where('a.user_id = ' . (int) $userId)
					->where('a.tjlms_order_id != 0')
					->dbLoadQuery()
					->loadAssoc();

		$isNew   = 0;
		$xrefID  = $CourseOrderId['id'];
		$orderId = 0;

		// Get only active status user so, order status is always complet
		//$newCourseStatus = 'C';

		// Check shika order is created or not
		if (!$CourseOrderId['tjlms_order_id'])
		{
			$isNew                          = 1;
			$user                           = JFactory::getUser($userId);
			$res                            = array();

			//$res['user_id']                 = $CourseOrderId['tjlms_order_id'];
			$res['user_id']                 = $userId;
			$res['name']                    = $user->name;
			$res['email']                   = $user->email;
			$res['coupon_code']             = '';
			$res['course_id']               = $course_Plans[0]->course_id;
			$res['coupon_discount']         = '';
			$res['coupon_discount_details'] = '';
			$res['order_tax']               = '';
			$res['order_tax_details']       = '';
			$res['cdate']                   = $date;
			$res['mdate']                   = $date;
			$res['processor']               = 'PayplanApp';
			$res['extra        ']           = '';
			$res['customer_note']           = '';
			$res['status']                  = 'C';
			$res['original_amt']            = $course_Plans[0]->price;
			$res['amount']                  = $course_Plans[0]->price;
			$res['ip_address']              = $_SERVER["REMOTE_ADDR"];


			$orderId                        = $this->TjlmsModelbuy->createMainOrder($res);

			$data                           = array();
			$data['plan_id']                = $course_Plans[0]->id;
			$data['course_id']              = $course_Plans[0]->course_id;
			$data['original_amt']           = $course_Plans[0]->price;

			$this->TjlmsModelbuy->updateOrderItems($data, $orderId);

			$rese  = array();
			$rese['status'] = 'C';
			$rese['transaction_id'] = '';
			$rese['raw_data'] = '';

			$this->TjlmsModelpayment->updateStatus($rese, $orderId);
		}
		else
		{
			$query = new XiQuery;

			$invoiceStatus = $query->select("status")
						->from('`#__payplans_subscription` as a')
						->where('a.order_id = ' . (int) $CourseOrderId['pp_order_id'])
						->where('a.user_id = ' . (int) $userId)
						->dbLoadQuery()
						->loadResult();

			$rese  = array();
			$rese['status'] = 'C';
			$rese['transaction_id'] = '';
			$rese['raw_data'] = '';

			$this->TjlmsModelpayment->updateStatus($rese, $CourseOrderId['tjlms_order_id']);
		}

		/*
		 * Using reference table save course order details and payplan details
		 */
		$xrefplArray->tjlms_order_id = $orderId;
		$xrefplArray->course_id      = $course_Plans[0]->course_id;
		$xrefplArray->course_plan_id = $plan;
		$xrefplArray->user_id = $userId;

		if ($orderId != 0)
		{
			$this->_xrefPayplanLMS($xrefplArray, $isNew, $xrefID);
		}
	}

	/**
	 * Render Course Plan Id
	 *
	 * @param   Interger  $coursePlans  course plan id.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function renderCoursePlanId($coursePlans)
	{
		$query = new XiQuery;
		$course_Plans = $query->select("*")
				->from('`#__tjlms_subscription_plans` as p')
				->where('p.id = ' . $coursePlans)
				->dbLoadQuery()
				->loadobjectlist();

		return $course_Plans;
	}

	/**
	 * Using reference table save course order details and payplan details
	 *
	 * @param   object   $xrefplArray  Subscription, order id with shika course id.
	 * @param   INTEGER  $isNew        Check is it new
	 * @param   INTEGER  $xrefID       PK of table
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function _xrefPayplanLMS($xrefplArray, $isNew = 1, $xrefID = 0)
	{
		$db = JFactory::getDBO();

		try
		{
			if ($isNew == 1)
			{
				$db->insertObject('#__tjlms_payplanApp', $xrefplArray);

				// Update user enrollment date
				$enroll                       = array();
				$enroll['course_id']          = $xrefplArray->course_id;
				$enroll['userid']             = $xrefplArray->user_id;
				$enroll['pp_app_id']          = $xrefplArray->pp_app_id;
				$enroll['pp_subscription_id'] = $xrefplArray->pp_subscription_id;

				// As per old enrollment date update new course enrollment date
				$this->updateEnrollmentDate($enroll);

				return 1;
			}
			else
			{
				$xrefplArray->id = $xrefID;

				return $db->updateObject('#__tjlms_payplanApp', $xrefplArray, 'id');
			}
		}
		catch (Exception $e)
		{
			return $e->getMessage();
		}
	}

	/**
	 * Update user enrollment date
	 *
	 * @param   Array  $enrollDataArray  user enrollment array of data.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function updateEnrollmentDate($enrollDataArray)
	{
		// Get old user enrollment data
		$old_enrollment_data = $this->getOldData($enrollDataArray);

		try
		{
			if ($old_enrollment_data)
			{
				return $this->setEnrollmentDate($enrollDataArray, $old_enrollment_data);
			}
		}
		catch (Exception $e)
		{
			return 0;
		}
	}

	/**
	 * Render old course enrollment details
	 *
	 * @param   Array  $enrollDataArray  user enrollment array of data.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function getOldData($enrollDataArray)
	{
		$query = new XiQuery;
		$oldOrderData = $query->select("*")
				->from('`#__tjlms_payplanApp` as tp')
				->where('tp.tjlms_order_id != 0')
				->where('tp.user_id = ' . $enrollDataArray['userid'])
				->where('tp.pp_app_id = ' . $enrollDataArray['pp_app_id'])
				->where('tp.pp_subscription_id = ' . $enrollDataArray['pp_subscription_id'])
				->order('tp.id ASC')
				->dbLoadQuery()
				->loadobject();

		$query = new XiQuery;
		$oldEnrollID = $query->select("enrollment_id")
				->from('`#__tjlms_orders` as o')
				->where('o.id = ' . $oldOrderData->tjlms_order_id)
				->dbLoadQuery()
				->loadobject();

		if ($oldEnrollID)
		{
			$query = new XiQuery;
			$oldEnrollIdData = $query->select("*")
				->from('`#__tjlms_enrolled_users` as e')
				->where('e.id = ' . $oldEnrollID->enrollment_id)
				->dbLoadQuery()
				->loadobject();

			return $oldEnrollIdData;
		}

		return 0;
	}

	/**
	 * For newly added course user enrollment date update
	 *
	 * @param   Array  $new_enrollDataArray  user enrollment array of data.
	 * @param   Array  $old_enrollment_data  user old enrollment array of data.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function setEnrollmentDate ($new_enrollDataArray, $old_enrollment_data)
	{
		try
		{
			if ($new_enrollDataArray)
			{
				$db = JFactory::getDBO();

				/*
				 * GET updated enrollment id
				 *
				 */
				$query = $db->getQuery(true);
				$query->select('*');
				$query->from('#__tjlms_enrolled_users as c');
				$query->where('c.course_id = ' . $db->quote($new_enrollDataArray['course_id']));
				$query->where('c.user_id = ' . $db->quote($new_enrollDataArray['userid']));

				$db->setQuery($query);
				$enrollmentData = $db->loadObject();

				/*
				 * Update user enrollment start time and end time
				 *
				 */

				$query = $db->getQuery(true);
				$query->select('expiration_date, subscription_date');
				$query->from('#__payplans_subscription as s');
				$query->where('s.subscription_id = ' . $db->quote($new_enrollDataArray['pp_subscription_id']));

				$db->setQuery($query);
				$subscription_data = $db->loadObject();

				$arrOne                   = new stdClass;
				$arrOne->id               = $enrollmentData->id;
				//$arrOne->enrolled_on_time = $old_enrollment_data->enrolled_on_time;
				$arrOne->enrolled_on_time = $subscription_data->subscription_date;
				$arrOne->end_time         = $subscription_data->expiration_date;

				$db->updateObject('#__tjlms_enrolled_users', $arrOne, 'id');

				/*
				 * Update user enrollment history table start time and end time
				 *
				 */

				$arrTwo                = new stdClass;
				$arrTwo->enrollment_id = $enrollmentData->id;
				//$arrTwo->start_date    = $old_enrollment_data->enrolled_on_time;
				$arrTwo->start_date    = $subscription_data->subscription_date;
				$arrTwo->end_date      = $subscription_data->expiration_date;

				$db->updateObject('#__tjlms_enrolled_users_history', $arrTwo, 'enrollment_id');

				return 1;
			}
		}
		catch (Exception $e)
		{
			return 0;
		}
	}
}


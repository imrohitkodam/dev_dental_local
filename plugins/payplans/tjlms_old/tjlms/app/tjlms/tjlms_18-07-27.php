<?php
/**
 * @package     Payplans.Plugin
 * @subpackage  Payplans.app
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Payplans Tjlms Plugin
 *
 * @since  1.6
 */
class PayplansAppTjlms extends PayplansApp
{
	// Inherited properties, this variable always helps PayPlans to get the app file location
	public $_location       = __FILE__;

	/** Let the system know if your app-instance should be triggered for given event and reference object.
	 *
	 * @param   object  $refObject  reference object of any type, check its type on which you want to work.
	 * It is generally a type
	 * Transaction / Invoice / Payment /Plan / Subscription
	 * @param   string  $eventName  a string which starts from onPayplans
	 *
	 * This function ensures your app is triggered for certain plans (as defined by user during app instance creation)
	 * Therefore
	 * 1. Do not override this function until it is essential.
	 * 2. Better to override function _isApplicable
	 *
	 * @return  avoid
	 */
	public function isApplicable($refObject= '', $eventName='')
	{
		$isApplicable = parent::isApplicable($refObject, $eventName);

		return $isApplicable;
	}

	/**
	 * save function
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function save()
	{
		$isSave = parent::save();

		$app_id = $isSave->app_id;
		$app_param = $isSave->app_params;
		$shika_plans = $app_param->get('tjlms_plans');

		$query = new XiQuery;

		$syncData = $query->select("*")
				->from('`#__payplans_tjlms_plan_sync` as s')
				->where('s.app_id = ' . $app_id)
				->order('s.id Desc')
				->dbLoadQuery()
				->loadObject();

		$syncArray          = new stdClass;
		$syncArray->app_id  = $app_id;

		if (!empty($syncData))
		{
			$syncArray->new_shika_course_plan = json_encode($shika_plans);

			$sync_id = $syncData->id;

			if ($syncData->sync_status == 1)
			{
				$newSyncValue = json_decode($syncData->new_shika_course_plan);

				if ($newSyncValue === $shika_plans)
				{
					$syncArray->modified_date = date("Y-m-d H:i:s");

					// Update row
					$this->_syncPayplanLMS($syncArray, 0, $sync_id);
				}
				else
				{
					$syncArray->sync_status           = 0;
					$syncArray->old_shika_course_plan = $syncData->new_shika_course_plan;
					$syncArray->created_date          = date("Y-m-d H:i:s");

					// Insert new row
					$this->_syncPayplanLMS($syncArray, 1, 0);
				}
			}
			else
			{
				$syncArray->modified_date = date("Y-m-d H:i:s");

				// Update row
				$this->_syncPayplanLMS($syncArray, 0, $sync_id);
			}
		}
		else
		{
			$syncArray->old_shika_course_plan = json_encode(array());
			$syncArray->new_shika_course_plan = json_encode($shika_plans);
			$syncArray->created_date          = date("Y-m-d H:i:s");
			$syncArray->sync_status           = 0;

			$this->_syncPayplanLMS($syncArray, 1, 0);
		}

		JFactory::getApplication()->enqueueMessage(JText::_('PLG_PAYPLANS_TJLMS_SYNC_BUTTON_ALERT'), 'Notice');

		return $isSave;
	}

	/**
	 * Insert course order details in tjlms database
	 *
	 * @param   object   $syncArray  Subscription, order id with shika course id.
	 * @param   INTEGER  $isNew      Check is it new
	 * @param   INTEGER  $syncID     PK of table
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function _syncPayplanLMS($syncArray, $isNew = 1, $syncID = 0)
	{
		$db = JFactory::getDBO();

		try
		{
			if ($isNew == 1)
			{
				return $db->insertObject('#__payplans_tjlms_plan_sync', $syncArray);
			}
			else
			{
				$syncArray->id = $syncID;

				return $db->updateObject('#__payplans_tjlms_plan_sync', $syncArray, 'id');
			}
		}
		catch (Exception $e)
		{
			return $e->getMessage();
		}
	}

	/**
	 * After Subscription save call this trigger
	 *
	 * @param   object  $prev  Subscription object with old data.
	 * @param   object  $new   Subscription object with new data
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function onPayplansSubscriptionAfterSave($prev, $new)
	{
		// #die('onPayplansSubscriptionAfterSave');
	}

	/**
	 * After invoice save call this trigger
	 *
	 * @param   object  $prev  Subscription object with old data.
	 * @param   object  $new   Subscription object with new data
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function onPayplansInvoiceAfterSave($prev, $new)
	{
		// #die('onPayplansInvoiceAfterSave');
	}

	/**
	 * After order save call this trigger
	 *
	 * @param   object  $prev  Subscription object with old data.
	 * @param   object  $new   Subscription object with new data
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function onPayplansOrderAfterSave($prev, $new)
	{
		/*if (($new->getStatus() == PayplansStatus::NONE) || ($prev != null && $prev->getStatus() == $new->getStatus()))
		{
			return true;
		}*/

		return $this->_triggerSetOrder($prev, $new);
	}

	/**
	 * Insert course order details in tjlms database
	 *
	 * @param   object  $prev  Subscription object with old data.
	 * @param   object  $new   Subscription object with new data
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function _triggerSetOrder($prev, $new)
	{
		$newStatus          = $new->getStatus();
		$Invoice            = $new->getInvoice();
		$userid 			= $new->getBuyer();
		$subId  			= $new->getId();
		$plan               = $new->getTitle();
		$app_coursePlans    = $this->getAppParam('tjlms_plans');

		// App Id
		$getAppId           = $this->getId();
		$planId             = 0;
		$orderId            = 0;

		if ($new->getId())
		{
			$planId         = $this->renderPayplansPlanId($subId);
			$orderId        = $this->renderPayplansOrderId($subId);
		}

		$pp_invoice_id      = 0;
		$invoice_status     = 0;

		if ($Invoice)
		{
			$pp_invoice_id  = $Invoice->getId();
			$invoice_status = $Invoice->getStatus();
		}

		$appId = 0;

		if ($planId)
		{
			$appId = $this->renderAppId($planId);
		}

		$xrefpl                     = new stdClass;
		$xrefpl->pp_order_id        = $orderId;
		$xrefpl->pp_invoice_id      = $pp_invoice_id;
		$xrefpl->pp_subscription_id = $subId;
		$xrefpl->user_id            = $userid;
		$xrefpl->pp_app_id          = $getAppId;

		return $this->createOrder($userid, $app_coursePlans, $invoice_status, $xrefpl);
	}

	/**
	 * Render Payplans Order Id
	 *
	 * @param   Interger  $subId  Subscription id.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function renderPayplansOrderId($subId)
	{
		$query = new XiQuery;

		return $planId = $query->select("s.order_id")
				->from('`#__payplans_subscription` as s')
				->where('s.subscription_id = ' . $subId)
				->dbLoadQuery()
				->loadResult();
	}

	/**
	 * Render Payplans Plan Id
	 *
	 * @param   Interger  $subId  Subscription id.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function renderPayplansPlanId($subId)
	{
		$query = new XiQuery;

		return $planId = $query->select("s.plan_id")
				->from('`#__payplans_subscription` as s')
				->where('s.subscription_id = ' . $subId)
				->dbLoadQuery()
				->loadResult();
	}

	/**
	 * Render Payplans Plan App Id
	 *
	 * @param   Interger  $planId  payplan id.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function renderAppId($planId)
	{
		$query = new XiQuery;

		return $appId = $query->select("a.app_id")
					->from('`#__payplans_planapp` as a, `#__payplans_app` as b')
					->where('a.app_id = b.app_id')
					->where('a.plan_id = ' . $planId)
					->where('b.type = "tjlms"')
					->dbLoadQuery()
					->loadResult();
	}

	/**
	 * Method to store a order record.
	 * Order is placed using this function.
	 *
	 * @param   INT    $userid       Order user id
	 * @param   ARRAY  $coursePlans  course plans
	 * @param   INT    $newStatus    order new status
	 * @param   ARRAY  $xrefplArray  payplan order, subscripation id, invoice id, app id, shika course id, course plan id
	 *
	 * @return  json  $data
	 *
	 * @since  1.0.0
	 */
	public function createOrder($userid, $coursePlans, $newStatus, $xrefplArray)
	{
		$params = $this->tjlmsGetParams();

		require_once JPATH_SITE . '/components/com_tjlms/helpers/courses.php';
		$this->TjlmsCoursesHelper = new TjlmsCoursesHelper;

		jimport('joomla.application.component.model');
		require_once JPATH_SITE . '/components/com_tjlms/models/buy.php';
		$this->TjlmsModelbuy = new TjlmsModelbuy;

		require_once JPATH_SITE . '/components/com_tjlms/models/payment.php';
		$this->TjlmsModelpayment = new TjlmsModelpayment;

		switch ($newStatus)
		{
			case "0":
			$newCourseStatus = 'P';
			break;

			case "401":
			$newCourseStatus = "P";
			break;

			case "402":
			$newCourseStatus = "C";
			break;

			case "403":
			$newCourseStatus = "RF";
			break;
		}

		$query = new XiQuery;

		foreach ($coursePlans as $plan)
		{
			$course_Plans = $this->renderCoursePlanId($plan);

			$query = new XiQuery;
			$CourseOrderId = $query->select("*")
						->from('`#__tjlms_payplanApp` as a')
						->where('a.pp_order_id = ' . $xrefplArray->pp_order_id)
						->where('a.course_id = ' . $course_Plans[0]->course_id)
						->where('a.user_id = ' . $userid)
						->dbLoadQuery()
						->loadAssoc();

			$isNew = 0;
			$xrefID = $CourseOrderId['id'];

			if (!$CourseOrderId['tjlms_order_id'])
			{
				$isNew                          = 1;
				$user                           = JFactory::getUser($userid);
				$res                            = array();
				$res['user_id']                 = $userid;
				$res['name']                    = $user->name;
				$res['email']                   = $user->email;
				$res['coupon_code']             = '';
				$res['course_id']               = $course_Plans[0]->course_id;
				$res['coupon_discount']         = '';
				$res['coupon_discount_details'] = '';
				$res['order_tax']               = '';
				$res['order_tax_details']       = '';
				$res['cdate']                   = date("Y-m-d H:i:s");
				$res['mdate']                   = date("Y-m-d H:i:s");
				$res['processor']               = 'PayplanApp';
				$res['extra        ']           = '';
				$res['customer_note']           = '';
				$res['status']                  = $newCourseStatus;
				$res['original_amt']            = $course_Plans[0]->price;
				$res['amount']                  = $course_Plans[0]->price;
				$res['ip_address']              = $_SERVER["REMOTE_ADDR"];

				$orderId                        = $this->TjlmsModelbuy->createMainOrder($res);

				$data                           = array();
				$data['plan_id']                = $course_Plans[0]->id;
				$data['course_id']              = $course_Plans[0]->course_id;
				$data['original_amt']           = $course_Plans[0]->price;

				$this->TjlmsModelbuy->updateOrderItems($data, $orderId);
			}
			else
			{
				$query = new XiQuery;
				/*$invoiceStatus = $query->select("status")
							->from('`#__payplans_invoice` as a')
							->where('a.invoice_id = ' . $CourseOrderId['pp_invoice_id'])
							->where('a.user_id = ' . $userid)
							->dbLoadQuery()
							->loadResult();

				switch ($invoiceStatus)
				{
					case "0":
					$newCourseStatus = 'P';
					break;

					case "401":
					$newCourseStatus = "P";
					break;

					case "402":
					$newCourseStatus = "C";
					break;

					case "403":
					$newCourseStatus = "RF";
					break;
				}*/

				$invoiceStatus = $query->select("status")
							->from('`#__payplans_subscription` as a')
							->where('a.order_id = ' . $CourseOrderId['pp_order_id'])
							->where('a.user_id = ' . $userid)
							->dbLoadQuery()
							->loadResult();

				switch ($invoiceStatus)
				{
					case "0":
					$newCourseStatus = 'P';
					break;

					case "1602":
					$newCourseStatus = "P";
					break;

					case "1601":
					$newCourseStatus = "C";
					break;

					case "1603":
					$newCourseStatus = "D";
					break;
				}

				$rese  = array();
				$rese['status'] = $newCourseStatus;

				$this->TjlmsModelpayment->updateStatus($rese, $CourseOrderId['tjlms_order_id']);
			}

			$xrefplArray->tjlms_order_id = $orderId;
			$xrefplArray->course_id      = $course_Plans[0]->course_id;
			$xrefplArray->course_plan_id = $plan;

			$this->_xrefPayplanLMS($xrefplArray, $isNew, $xrefID);
		}

		return;
	}

	/**
	 * Insert course order details in tjlms database
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
				return $db->insertObject('#__tjlms_payplanApp', $xrefplArray);
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
		if(empty($coursePlans))
		{
			return false;
		}

		$query = new XiQuery;
		$course_Plans = $query->select("*")
				->from('`#__tjlms_subscription_plans` as p')
				->where('p.id = ' . $coursePlans)
				->dbLoadQuery()
				->loadobjectlist();

		return $course_Plans;
	}

	/**
	 * Function used to plugin params
	 *
	 * @return  $socialIntegration
	 *
	 * @since  1.0.0
	 */
	public function tjlmsGetParams()
	{
		$app = JFactory::getApplication();

		jimport('joomla.registry.registry');
		$registry = new JRegistry;
		$registry->set('isMailSend', '1');

		// Merge plugin params plugin params override jlike component params
		$component_params = JComponentHelper::getParams('com_tjlms');

		$component_params->merge($registry);

		return $component_params;
	}
}

/**
 * Payplans Tjlms Plugin for log
 *
 * @since  1.6
 */
class PayplansAppTjlmsFormatter extends PayplansAppFormatter
{
	// View log
	public $template	= 'view_log';

	/**
	 * Get Ignore data
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function getIgnoredata()
	{
		$ignore = array('_trigger', '_tplVars', '_mailer', '_location', '_errors', '_component');

		return $ignore;
	}

	/**
	 * Get rules
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function getVarFormatter()
	{
		$rules = array('_appplans' => array('formatter' => 'PayplansAppFormatter',
						'function' => 'getAppPlans'),
						'app_params' => array('formatter' => 'PayplansAppTjlmsFormatter',
						'function' => 'getFormattedContent'));

		return $rules;
	}

	/**
	 * Format email app content,status, expiration time
	 *
	 * @param   Interger  $key    Order details
	 * @param   Interger  $value  Order details
	 * @param   ARRAY     $data   Order details
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function getFormattedContent($key, $value, $data)
	{
		$this->template = 'view';
	}
}

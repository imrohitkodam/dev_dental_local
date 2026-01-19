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

// Check if Payplans installed or not
jimport('joomla.filesystem.file');

if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}

// Load particular autoloading required
$app      = JFactory::getApplication();
$basepath = $app->isAdmin() ? JPATH_ADMINISTRATOR : JPATH_SITE;

$fileName = $basepath . '/components/com_payplans/includes/includes.php';

if (!JFile::exists($fileName))
{
	return true;
}
else
{
	$option	= JRequest::getVar('option');

	// Do not load payplans when component is com_installer
	if ($option == 'com_installer')
	{
		return true;
	}

	require_once $fileName;

	/**
	 * Payplans Tjlms Plugin
	 *
	 * @since  1.6
	 */
	class PlgSystemPayplans_Tjlms extends XiPlugin
	{
		public $_app = null;

		/**
		 * Constructor.
		 *
		 * @param   array  &$subject  Subject.
		 * @param   array  $config    An optional associative array of configuration settings.
		 *
		 * @since   1.0.0
		 */
		public function __construct(&$subject, $config = array())
		{
			parent::__construct($subject, $config);
			$this->_app = JFactory::getApplication();
			$this->path = JPATH_SITE . '/components/com_tjlms/helpers/main.php';

			if (JFile::exists($this->path))
			{
				if (!class_exists('comtjlmsHelper'))
				{
					JLoader::register('comtjlmsHelper', $this->path);
					JLoader::load('comtjlmsHelper');
				}

				$this->comtjlmsHelperObj = new comtjlmsHelper;
			}

			jimport('joomla.application.component.model');
			require_once JPATH_ADMINISTRATOR . '/components/com_tjlms/models/orders.php';
			$this->TjlmsModelOrders = new TjlmsModelOrders;

			$this->logparams = array();
			$this->logparams['filepath'] = JPATH_PLUGINS . '/system/payplans_tjlms/payplans_tjlms';
			$this->logparams['filename'] = 'log.php';
			$this->logparams['component'] = 'com_tjlms';
			$this->logparams['userid'] = JFactory::getUser()->id;
			$this->logparams['logEntryTitle'] = "Payplans and Shika integration";
			$this->logparams['desc'] = '';
			$this->logparams['logType'] = JLog::INFO;
		}

		/**
		 * Delete shika course order against paypal subscription
		 *
		 * @param   Interger  $subscriptionId  Payplan subscription id.
		 *
		 * @return  boolean
		 *
		 * @since   1.6
		 */
		public function onPayplansSubscriptionAfterDelete($subscriptionId)
		{
			if ($subscriptionId)
			{
				$query = new XiQuery;

				$CourseOrderIds = $query->select("a.tjlms_order_id")
								->from('`#__tjlms_payplanApp` as a')
								->where('a.pp_subscription_id = ' . $subscriptionId)
								->where('a.tjlms_order_id != 0')
								->dbLoadQuery()
								->loadRowList();

				if (!empty($CourseOrderIds))
				{
					foreach ($CourseOrderIds as $orderId)
					{
						$this->deleteOrdersFromLMS($orderId);
					}
				}
			}
		}

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
			$limitstart   = $jinput->get('limit');
			$appId   = $jinput->get('app_id');

			// Set plugin parameter config to it
			$limit        = $this->params->get('sync_limit');
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
			$db->setQuery($query, $limitstart, $limit);

			$app_data = $db->loadObject();
	//		$appIds = array_filter($appIds);

			if ($app_data)
			{
				// foreach ($appIds as $app_data)
				// {
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
							$payplan_details = $this->renderPayplansDetails($payplan_id, $userid);

							if ($payplan_details)
							{
								$planData = array();

								foreach ($payplan_details AS $plan_details)
								{
									if ($extra_added_plan)
									{
										$xrefpl                     = new stdClass;
										$xrefpl->pp_order_id        = $plan_details->order_id;
										$xrefpl->pp_invoice_id      = $plan_details->invoice_id;
										$xrefpl->pp_subscription_id = $plan_details->subscription_id;
										$xrefpl->user_id            = $plan_details->subscription_user;
										$xrefpl->pp_app_id          = $appId;

										$invoiceStatus = 0;

										if ($plan_details->invoice_status)
										{
											$invoiceStatus = $plan_details->invoice_status;
										}
										$planData[] = array(
											'userid' => $userid,
											'extra_added_plan' => (object) $extra_added_plan,
											'invoiceStatus' => $invoiceStatus,
											'xrefpl' => $xrefpl,
											'current_date' => $current_date
										);

										//$this->createOrder($userid, $extra_added_plan, $invoiceStatus, $xrefpl, $current_date);
									}
								}
							}

							$mainArray[] = $planData;

							if ($remove_plan)
							{
								$this->deleteShikaOrder($remove_plan, $userid);
							}
						}
					}

					// Update sync status
					$this->updateSyncStatus($sync_id, $current_date);

				$current_running_app_count = $limit + $limitstart;
			}
			else
			{
				$current_running_app_count = 0;
			}

			$appCount = $this->getAppCount();
			//$response = array('appCount' => $appCount, 'current_running_app_count' => $current_running_app_count);
			$response = array('appData' => $mainArray);

			return $response;
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

			$plan_id = $query->select("a.user_id")
						->from('`#__payplans_subscription` as a')
						->where('a.plan_id = ' . $planId)
						->dbLoadQuery()
						->loadColumn();

			return $plan_id;
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
				$selectList   = 's.user_id AS subscription_user, o.order_id, ';
				$selectList  .= 'i.invoice_id, s.subscription_id, s.plan_id, i.status AS invoice_status, t.tjlms_order_id';
				$query->select($selectList);
				$query->from('`#__payplans_subscription` AS s');
				$query->leftJoin('`#__payplans_order` AS o ON o.`order_id` = s.`order_id`');
				$query->leftJoin('`#__payplans_invoice` AS i ON i.`object_id` = s.`order_id`');
				$query->leftJoin('`#__tjlms_payplanApp` AS t ON t.`pp_subscription_id` = s.`subscription_id`');
				$query->where('s.plan_id = ' . $planId);
				$query->where('t.tjlms_order_id != 0');
				$query->where('s.user_id = ' . $userid);
				$db->setQuery($query);

				return $plan_details = $db->loadObjectList();
			}

			return 0;
		}

		/**
		 * Method to store a order record.
		 * Order is placed using this function.
		 *
		 * @param   INT       $userid        Order user id
		 * @param   ARRAY     $coursePlans   course plans
		 * @param   INT       $newStatus     order new status
		 * @param   ARRAY     $xrefplArray   payplan order, subscripation id, invoice id, app id, shika course id, course plan id
		 * @param   datetime  $current_date  current date
		 *
		 * @return  json  $data
		 *
		 * @since  1.0.0
		 */
		public function onAjaxCreateOrder()
		{
			$dataArray = json_decode($_POST['data']);

			foreach ($dataArray as $data)
			{
				$userid = $data->userid;
				$newStatus = $data->invoiceStatus;
				$current_date = $data->current_date;
				$coursePlans = $data->extra_added_plan;
				$xrefplArray = $data->xrefpl;

				//$userid, $coursePlans, $newStatus, $xrefplArray, $current_date
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
								->where('a.tjlms_order_id != 0')
								->dbLoadQuery()
								->loadAssoc();

					$isNew   = 0;
					$xrefID  = $CourseOrderId['id'];
					$orderId = 0;

					// Check shika order is created or not

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
						$res['cdate']                   = $current_date;
						$res['mdate']                   = $current_date;
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

						$rese  = array();
						$rese['status'] = $newCourseStatus;
						$rese['transaction_id'] = '';
						$rese['raw_data'] = '';

						$this->TjlmsModelpayment->updateStatus($rese, $orderId);
					}
					else
					{
						$query = new XiQuery;

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

					if ($orderId != 0)
					{
						$this->_xrefPayplanLMS($xrefplArray, $isNew, $xrefID);
					}
				}
			}

			return;
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

					$arrOne                   = new stdClass;
					$arrOne->id               = $enrollmentData->id;
					$arrOne->enrolled_on_time = $old_enrollment_data->enrolled_on_time;
					$arrOne->end_time         = $old_enrollment_data->end_time;

					$db->updateObject('#__tjlms_enrolled_users', $arrOne, 'id');

					/*
					 * Update user enrollment history table start time and end time
					 *
					 */

					$arrTwo                = new stdClass;
					$arrTwo->enrollment_id = $enrollmentData->id;
					$arrTwo->start_date    = $old_enrollment_data->enrolled_on_time;
					$arrTwo->end_date      = $old_enrollment_data->end_time;

					$db->updateObject('#__tjlms_enrolled_users_history', $arrTwo, 'enrollment_id');

					return 1;
				}
			}
			catch (Exception $e)
			{
				return 0;
			}
		}

		/**
		 * Delete shika order
		 *
		 * @param   Interger  $coursePlanIds  course plan id.
		 * @param   Interger  $userid         User id.
		 *
		 * @return  boolean
		 *
		 * @since   1.6
		 */
		public function deleteShikaOrder($coursePlanIds, $userid)
		{
			$db    = JFactory::getDBO();
			$query = $db->getQuery(true);

			foreach ($coursePlanIds as $deletePlanIds)
			{
				$query = new XiQuery;
				$pp_subscription_id = $query->select("tjlms_order_id")
								->from('`#__tjlms_payplanApp` as a')
								->where('a.course_plan_id = ' . $deletePlanIds)
								->where('a.user_id = ' . $userid)
								->where('a.tjlms_order_id != 0')
								->dbLoadQuery()
								->loadObjectList();

				foreach ($pp_subscription_id as $subscription_id)
				{
					if (!$this->deleteOrdersFromLMS($subscription_id->tjlms_order_id, 'sync'))
					{
						return 0;
					}
				}
			}

			return 1;
		}

		/**
		 * Using shika order module call delete fuction, delete order
		 *
		 * @param   Interger  $orderId  Shika course order id.
		 * @param   String    $from     This function call from where (this flag is $from).
		 *
		 * @return  boolean
		 *
		 * @since   1.6
		 */
		public function deleteOrdersFromLMS($orderId, $from = '')
		{
			try
			{
				if ($orderId)
				{
					if ($from == 'sync')
					{
						$orderId = array($orderId);
					}

					if ($this->TjlmsModelOrders->delete($orderId))
					{
						$order_id = implode(',', $orderId);

						if ($from == 'sync')
						{
							$this->logparams['desc'] = JText::sprintf(
														'PLG_PAYPLANS_TJLMS_SYNC_LOGS',
														$order_id
													);
							$this->logparams['filename'] = 'sync_log.php';
							$this->logparams['logEntryTitle'] = "Payplans App remove course plans";
						}
						else
						{
							$this->logparams['desc'] = JText::sprintf(
														'PLG_PAYPLANS_TJLMS_LOGS',
														$subscriptionId,
														$order_id
													);
						}

						$this->logparams['logType'] = JLog::INFO;
						$this->comtjlmsHelperObj->techjoomlaLog($this->logparams['filename'], $this->logparams['filepath'], $this->logparams);
					}
				}

				return 1;
			}
			catch (Exception $e)
			{
				return 0;
			}
		}

		/**
		 * Update sync status
		 *
		 * @param   Interger  $sync_id       sync id.
		 * @param   datetime  $current_date  current date
		 *
		 * @return  boolean
		 *
		 * @since   1.6
		 */
		public function updateSyncStatus($sync_id, $current_date)
		{
			try
			{
				if ($sync_id)
				{
					$db = JFactory::getDBO();

					// Update Sync status
					$query = $db->getQuery(true);

					// Fields to update.
					$fields = array(
						$db->quoteName('sync_date') . ' = ' . $db->quote($current_date),
						$db->quoteName('sync_status') . ' = 1'
					);

					// Conditions for which records should be updated.
					$conditions = array(
						$db->quoteName('id') . ' = ' . $db->quote($sync_id),
						$db->quoteName('sync_status') . ' = 0'
					);

					$query->update($db->quoteName('#__payplans_tjlms_plan_sync'))->set($fields)->where($conditions);

					$db->setQuery($query);

					$result = $db->execute();
				}

				return 1;
			}
			catch (Exception $e)
			{
				return 0;
			}
		}

		/**
		 * Get Total app count
		 *
		 * @return  boolean
		 *
		 * @since   1.6
		 */
		public function getAppCount()
		{
			$db    = JFactory::getDBO();
			$query = $db->getQuery(true);

			$query->select('count(*)');
			$query->from('`#__payplans_app` AS a');
			$query->leftJoin('`#__payplans_planapp` AS pa ON pa.`app_id` = a.`app_id`');
			$query->leftJoin('`#__payplans_plan` AS p ON p.`plan_id` = pa.`plan_id`');
			$query->leftJoin('`#__payplans_tjlms_plan_sync` AS s ON s.`app_id` = a.`app_id`');
			$query->where('s.sync_status = 0');
			$query->where('p.published = 1');
			$query->where('a.published = 1');
			$query->where('a.type = "tjlms"');
			$db->setQuery($query);

			return $appCount = $db->loadResult();
		}
	}
}

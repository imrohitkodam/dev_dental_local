<?php
/**
 * @version    SVN: <svn_id>
 * @package    Com_Tjlms
 * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');
jimport('joomla.database.table.user');
jimport('techjoomla.common');

/**
 * Methods supporting buy process
 *
 * @since  1.0.0
 */
class TjlmsModelbuy extends JModelLegacy
{
	public $total = null;

	public $pagination = null;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		$TjGeoHelper = JPATH_ROOT . DS . 'components/com_tjfields/helpers/geo.php';

		if (!class_exists('TjGeoHelper'))
		{
			JLoader::register('TjGeoHelper', $TjGeoHelper);
			JLoader::load('TjGeoHelper');
		}

		$this->TjGeoHelper = new TjGeoHelper;
		$this->techjoomlacommon = new TechjoomlaCommon;
	}

	/**
	 * To Fetch country list from Db
	 *
	 * @return  list of countries
	 *
	 * @since  1.0.0
	 */
	public function getCountry()
	{
		return $this->TjGeoHelper->getCountryList();
	}

	/**
	 * To Fetch state list from Db
	 *
	 * @param   INT  $country  Country ID
	 *
	 * @return  list of state
	 *
	 * @since  1.0.0
	 */
	public function getuserState($country)
	{
		return $this->TjGeoHelper->getRegionListFromCountryID($country);
	}

	/**
	 * Get user data that would be prefill during billing info.
	 *
	 * @param   INT  $orderid  Order ID
	 *
	 * @return  ARRAY  User data
	 *
	 * @since  1.0.0
	 */
	public function getuserdata($orderid = 0)
	{
		$db = JFactory::getDBO();

		$params   = JComponentHelper::getParams('com_tjlms');
		$user     = JFactory::getUser();
		$userdata = array();

		if ($orderid and !($user->id))
		{
			$query = "SELECT u.*
						FROM #__tjlms_users as u
						WHERE  u.order_id=" . $orderid . " order by u.id DESC LIMIT 0 , 1";
		}
		else
		{
			$query = "SELECT u.*
			FROM #__tjlms_users as u
			 WHERE  u.user_id=" . $user->id . " order by u.id DESC LIMIT 0 , 1";
		}

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!empty($result))
		{
			if ($result[0]->address_type == 'BT')
			{
				$userdata['BT'] = $result[0];
			}
			elseif ($result[1]->address_type == 'BT')
			{
				$userdata['BT'] = $result[1];
			}
		}
		else
		{
			$row             = new stdClass;
			$row->user_email = $user->email;
			$userdata['BT']  = $row;
			$userdata['ST']  = $row;
		}

		return $userdata;
	}

	/**
	 * during checkout...used for recalculating amount...return the price of the plan
	 *
	 * @param   INT  $selected_plan  Selected plan by the user
	 *
	 * @return  STRING  Amount for the plan
	 *
	 * @since  1.0.0
	 */
	public function getoriginalAmt($selected_plan = '')
	{
		$originalamt = 0;
		$query = "SELECT price FROM #__tjlms_subscription_plans WHERE id=" . (int) $selected_plan;
		$this->_db->setQuery($query);
		$originalamt = $this->_db->loadresult();

		return $originalamt;
	}

	/**
	 * Apply coupon to the actual value. during checkout.
	 *
	 * @param   INT     $originalamount  Original amount for ckeckout
	 * @param   STRING  $coupon_code     Coupon applied if any
	 * @param   STRING  $course_id       Couse against which coupon is to apply
	 *
	 * @return  ARRAY  $vars  Data
	 *
	 * @since  1.0.0
	 */
	public function applycoupon($originalamount, $coupon_code = '', $course_id = '')
	{
		$coupon_code     = trim($coupon_code);
		$val             = 0;
		$coupon_discount = $this->getcoupon($coupon_code, $course_id);

		if ($coupon_discount)
		{
			if ($coupon_discount->data[0]->val_type == 1)
			{
				$val = ($coupon_discount->data[0]->value / 100) * ($originalamount);
			}
			else
			{
				$val                             = $coupon_discount->data[0]->value;
				$vars['coupon_discount_details'] = json_encode($coupon_discount);
			}
		}

		$amt                     = $originalamount - $val;
		$vars['original_amt']    = $originalamount;
		$vars['amt']             = $amt;
		$vars['coupon_discount'] = $val;

		return $vars;
	}

	/**
	 * Method to store a order record.
	 * Order is placed using this function.
	 *
	 * @param   ARRAY   $orderdata  Order details
	 * @param   STRING  $step       Checkout step
	 *
	 * @return  json  $data
	 *
	 * @since  1.0.0
	 */
	public function createOrder($orderdata, $step = '')
	{
		if (!$orderdata['user_id'])
		{
			echo JText::_('COM_TJLMS_SESSION_EXIRED');

			return false;
		}

		if ($step == 'step_select_subsplan')
		{
			return $this->createSubscriptionOrder($orderdata);
		}

		if ($step == 'save_step_billinginfo')
		{
			return $this->saveBillingInfo($orderdata);
		}
	}

	/**
	 * returns User data
	 *
	 * @param   INT    $uid              User Id
	 * @param   ARRAY  $billingarr       user billing address
	 * @param   INT    $insert_order_id  order ID
	 *
	 * @return  STRING  Amount for the plan
	 *
	 * @since  1.0.0
	 */
	public function billingaddr($uid, $billingarr, $insert_order_id)
	{
		$this->_db->setQuery('SELECT order_id FROM #__tjlms_users WHERE order_id=' . $insert_order_id);
		$order_id = (string) $this->_db->loadResult();

		require_once JPATH_SITE . '/components/com_tjlms/helpers/main.php';
		$comtjlmsHelper = new comtjlmsHelper;

		$com_params = JComponentHelper::getParams('com_tjlms');

		if ($order_id)
		{
			$query = "DELETE FROM #__tjlms_users    WHERE order_id=" . $insert_order_id;
			$this->_db->setQuery($query);

			if (!$this->_db->execute())
			{
				echo $this->_db->stderr();

				return false;
			}
		}

		$row               = new stdClass;
		$row->user_id      = $uid;
		$row->user_email   = $billingarr['email1'];
		$row->address_type = 'BT';
		$row->firstname    = $billingarr['fnam'];
		$row->lastname     = $billingarr['lnam'];
		$row->country_code = $billingarr['country'];

		if (!empty($billingarr['vat_num']))
		{
			$row->vat_number = $billingarr['vat_num'];
		}

		$row->address    = $billingarr['addr'];
		$row->city       = $billingarr['city'];
		$row->state_code = $billingarr['state'];
		$row->zipcode    = $billingarr['zip'];
		$row->phone      = $billingarr['phon'];
		$row->approved   = '1';
		$row->order_id   = $insert_order_id;

		if (!$this->_db->insertObject('#__tjlms_users', $row, 'id'))
		{
			echo $this->_db->stderr();

			return false;
		}

		$params = JComponentHelper::getParams('com_tjlms');

		// Save customer note in order table
		$order = new stdClass;

		if ($insert_order_id)
		{
			$order->id            = $insert_order_id;
			$order->customer_note = $billingarr['comment'];

			if ($uid)
			{
				$order->name  = JFactory::getUser($uid)->name;
				$order->email = JFactory::getUser($uid)->email;
			}
			else
			{
				$order->name  = $billingarr['fnam'] . " " . $billingarr['lnam'];
				$order->email = $billingarr['email1'];
			}

			if (!$this->_db->updateObject('#__tjlms_orders', $order, 'id'))
			{
				echo $this->_db->stderr();

				return false;
			}
		}

		// Send mail on new order placed
		$sendMailOnOrderPlaced = $com_params->get('mail_on_new_order', '', 'INT');
		$session    = JFactory::getSession();
		$sendUpdateMail = $session->get('sendUpdateMail');

		if ($sendMailOnOrderPlaced == 1 && $sendUpdateMail == 1)
		{
			$comtjlmsHelper->sendInvoiceEmail($row->order_id);
		}

		$session->set('sendUpdateMail', '0');

		return $row->user_id;
	}

	/**
	 * Update order details.
	 *
	 * @param   INT    $orderid    Order ID
	 * @param   ARRAY  $orderInfo  Order data
	 *
	 * @return  boolean  true or false
	 *
	 * @since  1.0.0
	 */
	public function updateOrderDetails($orderid, $orderInfo = array())
	{
		$obj = new stdClass;

		$obj->id = $orderid;

		if (isset($orderInfo['user_id']))
		{
			$obj->user_id = $orderInfo['user_id'];
		}

		if (isset($orderInfo['email']))
		{
			$obj->email = $orderInfo['email'];
		}

		if (isset($orderInfo['enrollment_id']))
		{
			$obj->enrollment_id = $orderInfo['enrollment_id'];
		}

		// Update order entry.
		if (!$this->_db->updateObject('#__tjlms_orders', $obj, 'id'))
		{
			echo $this->_db->stderr();

			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Function to create oder during checkout
	 *
	 * @param   INT  $orderdata  Orderdata
	 *
	 * @return  INT  Order ID
	 *
	 * @since  1.0.0
	 */
	public function createMainOrder($orderdata)
	{
		$res = $this->createOrderObject($orderdata);

		// Update order if orde_id present
		if (isset($orderdata['order_id']))
		{
			$res->id = $orderdata['order_id'];
			$this->_db->updateObject('#__tjlms_orders', $res, 'id');
			$insert_order_id = $orderdata['order_id'];
		}
		else
		{
			// Store Order to tjlms Table
			$lang      = JFactory::getLanguage();
			$extension = 'com_tjlms';

			$base_dir = JPATH_ROOT;
			$lang->load($extension, $base_dir);

			$com_params     = JComponentHelper::getParams('com_tjlms');
			$integration    = $com_params->get('integration');
			$currency       = $com_params->get('currency');
			$order_prefix   = $com_params->get('order_prefix');
			$separator      = $com_params->get('separator');
			$random_orderid = $com_params->get('random_orderid');
			$padding_count  = $com_params->get('padding_count');

			// Lets make a random char for this order
			// Take order prefix set by admin
			$order_prefix = (string) $order_prefix;

			// String length should not be more than 5
			$order_prefix = substr($order_prefix, 0, 5);

			// Take separator set by admin
			$separator     = (string) $separator;
			$res->order_id = $order_prefix . $separator;

			// Check if we have to add random number to order id
			$use_random_orderid = (int) $random_orderid;

			if ($use_random_orderid)
			{
				$random_numer = $this->_random(5);
				$res->order_id .= $random_numer . $separator;

				/* this length shud be such that it matches the column lenth of primary key
				// it is used to add pading
				// order_id_column_field_length - prefix_length - no_of_underscores - length_of_random number */
				$len = (23 - 5 - 2 - 5);
			}
			else
			{
				/* This length shud be such that it matches the column lenth of primary key
				// It is used to add pading
				// Order_id_column_field_length - prefix_length - no_of_underscores */
				$len = (23 - 5 - 2);
			}

			if (!$this->_db->insertObject('#__tjlms_orders', $res, 'id'))
			{
				echo $this->_db->stderr();

				return false;
			}

			$insert_order_id = $orders_key = $this->_db->insertid();

			$this->_db->setQuery('SELECT order_id FROM #__tjlms_orders WHERE id=' . $orders_key);
			$order_id      = (string) $this->_db->loadResult();
			$maxlen        = 23 - strlen($order_id) - strlen($orders_key);
			$padding_count = (int) $padding_count;

			// Use padding length set by admin only if it is les than allowed(calculate) length
			if ($padding_count > $maxlen)
			{
				$padding_count = $maxlen;
			}

			if (strlen((string) $orders_key) <= $len)
			{
				$append = '';

				for ($z = 0; $z < $padding_count; $z++)
				{
					$append .= '0';
				}

				$append = $append . $orders_key;
			}

			$resd     = new stdClass;
			$resd->id = $orders_key;
			$order_id = $resd->order_id = $order_id . $append;

			if (!$this->_db->updateObject('#__tjlms_orders', $resd, 'id'))
			{
				echo $this->_db->stderr();

				return false;
			}
		}

		return $insert_order_id;
	}

	/**
	 * Function to create the object order
	 *
	 * @param   ARRAY  $data  order data
	 *
	 * @return  obj
	 *
	 * @since  1.0.0
	 */
	public function createOrderObject($data)
	{
		$res = new StdClass;
		$comtjlmsHelper = new comtjlmsHelper;

		if (isset($data['name']))
		{
			$res->name = $data['name'];
		}

		if (isset($data['email']))
		{
			$res->email = $data['email'];
		}

		if (isset($data['user_id']))
		{
			$res->user_id = $data['user_id'];
		}

		$res->coupon_code = $data['coupon_code'];

		$res->course_id               = $data['course_id'];
		$res->coupon_discount         = $data['coupon_discount'];
		$res->coupon_discount_details = $data['coupon_discount_details'];
		$res->order_tax               = $data['order_tax'];
		$res->order_tax_details       = $data['order_tax_details'];

		$res->cdate = $this->techjoomlacommon->getDateInUtc(JHtml::date('now', 'Y-m-d H:i:s', true));
		$res->mdate = $this->techjoomlacommon->getDateInUtc(JHtml::date('now', 'Y-m-d H:i:s', true));

		if (isset($data['processor']))
		{
			$res->processor = $data['processor'];
		}

		if (isset($data['customer_note']))
		{
			$res->customer_note = $data['customer_note'];
		}

		$res->status          = 'I';

		if (isset($data['status']))
		{
			$res->status = $data['status'];
		}

		// This is calculated amount
		$res->original_amount = $data['original_amt'];
		$res->amount          = $data['amount'];
		$res->ip_address      = $_SERVER["REMOTE_ADDR"];

		return $res;
	}

	/**
	 * Function used to recalculate amount
	 *
	 * @param   ARRAY  $amountdata      Amount data
	 * @param   ARRAY  $allow_taxation  Param
	 *
	 * @return  ARRAY  vars
	 *
	 * @since  1.0.0
	 */
	public function recalculatetotalamount($amountdata, $allow_taxation = 0)
	{
		$com_params = JComponentHelper::getParams('com_tjlms');

		$originalamt = 0;

		// Calculate original Amt to pay Based on Subs plan Price.
		$originalamt = $amountdata['original_amount'];

		if (!empty($amountdata['coupon_code']))
		{
			// Apply coupon if applied. Vars returns us original_amt, amt as amount after discount, coupon_discount;
			$vars = $this->applycoupon($originalamt, $amountdata['coupon_code'], $amountdata['course_id']);
		}
		else
		{
			$vars['original_amt']    = $originalamt;
			$vars['amt']             = $originalamt;
			$vars['coupon_code']     = $amountdata['coupon_code'];
			$vars['coupon_discount'] = 0;
		}

		// If taxation is applied
		if ($allow_taxation)
		{
			$tax_amt = $this->applytax($vars);

			if (isset($tax_amt->taxvalue) and $tax_amt->taxvalue > 0)
			{
				$vars['order_tax']         = $tax_amt->taxvalue;
				$vars['amt']               = $vars['net_amt_after_tax'] = $vars['amt'] + $tax_amt->taxvalue;
				$vars['order_tax_details'] = json_encode($tax_amt);
			}
		}
		// @TODO Sagar to do check for 0 value condition
		return $vars;
	}

	/*
	public function registerUser($regdata1)
	{
	$regdata['fnam'] = $regdata1['fnam'];
	$regdata['user_name'] =$regdata1['email1'];
	$regdata['user_email']=$regdata1['email1'];

	require_once(JPATH_SITE.DS."components".DS."com_tjlms".DS."models".DS."registration.php");
	$tjlmsModelregistration=new tjlmsModelregistration();

	if(!$tjlmsModelregistration->store($regdata))
	return false;
	$user =JFactory::getUser();
	return $userid=$user->id;

	}
	*/
	/**
	 * Function used to apply tax
	 *
	 * @param   ARRAY  $vars  tax vars
	 *
	 * @return  ARRAY
	 *
	 * @since  1.0.0
	 */
	public function applytax($vars)
	{
		// Set Required Sessions
		$dispatcher = JDispatcher::getInstance();

		// @TODO:need to check plugim type..
		JPluginHelper::importPlugin('lmstax');

		// Call the plugin and get the result
		$taxresults = $dispatcher->trigger('addTax', array(
															$vars['amt']
														)
											);

		if (isset($taxresults[0]) and $taxresults['0']->taxvalue > 0)
		{
			return $taxresults['0'];
		}
		else
		{
			return 0;
		}
	}

	/**
	 * function
	 *
	 * @param   INT  $length  dont know
	 *
	 * @return  STRING
	 *
	 * @since  1.0.0
	 */
	public function _random($length = 5)
	{
		$salt   = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$len    = strlen($salt);
		$random = '';

		$stat = @stat(__FILE__);

		if (empty($stat) || !is_array($stat))
		{
			$stat = array(
				php_uname()
			);
		}

		mt_srand(crc32(microtime() . implode('|', $stat)));

		for ($i = 0; $i < $length; $i++)
		{
			$random .= $salt[mt_rand(0, $len - 1)];
		}

		return $random;
	}

	/**
	 * Update order items table
	 *
	 * @param   ARRAY  $data      Order item data
	 * @param   INT    $order_id  Order ID
	 *
	 * @return  boolean  true or false
	 *
	 * @since  1.0.0
	 */
	public function updateOrderItems($data, $order_id)
	{
		$session = JFactory::getSession();
		$db      = JFactory::getDBO();

		// Get old plan Id
		JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_tjlms/tables');
		$db = JFactory::getDbo();
		$table = JTable::getInstance('Orderitems', 'TjlmsTable', array('dbo', $db));
		$table->load(array('order_id' => $order_id));

		if (isset($table->plan_id) && $table->plan_id)
		{
			if ($table->plan_id != $data['plan_id'])
			{
				$session->set('sendUpdateMail', 1);
			}
		}
		else
		{
			$session->set('sendUpdateMail', 1);
		}

		$lms_selected_plan = $data['plan_id'];
		$lms_course_id     = $data['course_id'];

		$table->order_id = $order_id;
		$table->course_id = $lms_course_id;
		$table->plan_id   = $lms_selected_plan;
		$table->amount    = $data['original_amt'];
		$table->store();

		return true;
	}

	/**
	 * Clear session
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function clearSession()
	{
		$session = JFactory::getSession();
		$session->set('subplanid', '');
		$session->set('lms_orderid', '');
		$session->set('order_id', '');
	}

	/**
	 * Function
	 *
	 * @return  STRING  html
	 *
	 * @since  1.0.0
	 */
	public function buildLayout()
	{
		// Load the layout & push variables
		ob_start();
		$layout = $this->buildLayoutPath($layout);
		include $layout;
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	/**
	 * Function used to get coupon if avaiable
	 *
	 * @param   STRING  $c_code     Coupon code
	 *
	 * @param   STRING  $course_id  Course id
	 *
	 * @return  obj    Coupon details
	 *
	 * @since  1.0.0
	 */
	public function getcoupon($c_code, $course_id)
	{
		$user  = JFactory::getUser();
		$db    = JFactory::getDBO();

		$result = new stdclass;
		$result->status = 'none';

		if ($course_id)
		{
			$this->tjlmsCoursesHelper = new tjlmsCoursesHelper;
			$course_creator = $this->tjlmsCoursesHelper->getCourseColumn($course_id, 'created_by');
		}

		$query = $db->getQuery(true);
		$query->select("course_id,value,val_type,exp_date, privacy, created_by");
		$query->from(" #__tjlms_coupons");
		$query->where("code = " . $db->quote($c_code));

		$db->setQuery($query);
		$coupon_obj = $db->loadObject();

		if (empty($coupon_obj))
		{
			$result->status = 'invalid';
		}
		elseif ($coupon_obj->exp_date && $coupon_obj->exp_date != '0000-00-00 00:00:00' &&
		strtotime($coupon_obj->exp_date) <= strtotime(JFactory::getDate('now', 'UTC')))
		{
			$result->status = 'expired';
		}
		else
		{
			if ($coupon_obj->course_id)
			{
				$coupon_course_id = explode(",", $coupon_obj->course_id);

				if (in_array($course_id, $coupon_course_id))
				{
					$result->status = 'ok';
				}
			}
			else
			{
				if ($coupon_obj->privacy == 1)
				{
					$result->status = 'ok';
				}
				else
				{
					if ($course_id && $coupon_obj->created_by == $course_creator->created_by)
					{
							$result->status = 'ok';
					}
					else
					{
						$result->status = 'invalid';
					}
				}
			}

			if ($result->status == 'ok')
			{
				$result->status = 'exceed';

				$subquery1 = $db->getQuery(true);
				$subquery1->select('COUNT( api1.coupon_code )');
				$subquery1->from("#__tjlms_orders AS api1");
				$subquery1->where("api1.coupon_code = " . $db->quote($db->escape($c_code)));
				$subquery1->where("(api1.status = 'C' OR (api1.status = 'P' AND ( (api1.processor = 'bycheck') OR (api1.processor = 'byorder')) ))");

				$subquery2 = $db->getQuery(true);
				$subquery2->select('COUNT( api.coupon_code )');
				$subquery2->from("#__tjlms_orders AS api");
				$subquery2->where("api.coupon_code = " . $db->quote($db->escape($c_code)));
				$subquery2->where("api.user_id =" . $user->id);

				$query = $db->getQuery(true);
				$query->select("value, val_type");
				$query->from(" #__tjlms_coupons");
				$query->where("(from_date <= " . $db->quote(JFactory::getDate('now', 'UTC', true)) . " OR from_date = '0000-00-00 00:00:00')");
				$query->where("(exp_date >= " . $db->quote(JFactory::getDate('now', 'UTC', true)) . "   OR exp_date = '0000-00-00 00:00:00')");
				$query->where("(max_use > (" . $subquery1 . ") OR max_use =0) AND (max_per_user > (" . $subquery2 . ") OR max_per_user =0)");
				$query->where("state =1 AND code=" . $db->quote($db->escape($c_code)));

				$db->setQuery($query);
				$count = $db->loadObjectList();

				if (!empty ($count))
				{
					$result->data  = $count;
					$result->status = 'ok';
				}
			}
		}

		return $result;
	}

	/**
	 * Function used to get Tax if avaiable
	 *
	 * @param   INT  $dis_totalamt  Total amount
	 *
	 * @return  INT  tax value
	 *
	 * @since  1.0.0
	 */
	public function afterTaxPrice($dis_totalamt)
	{
		$dispatcher = JDispatcher::getInstance();

		// @TODO:need to check plugim type..
		JPluginHelper::importPlugin('tjlmstax');
		$taxresults = $dispatcher->trigger('addTax', array(
															$dis_totalamt
														)
											);

		return $taxresults;
	}

	/**
	 * Function used to get price after discount
	 *
	 * @param   INT     $totalamt  Amount
	 * @param   STRING  $c_code    Coupon code
	 *
	 * @return  INT  Amount for the plan
	 *
	 * @since  1.0.0
	 */
	public function afterDiscountPrice($totalamt, $c_code)
	{
		$coupon       = $this->getcoupon($c_code);
		$coupon       = $coupon ? $coupon : array();
		$dis_totalamt = $totalamt;

		// If user entered code is matched with dDb coupon code
		if (isset($coupon) && $coupon)
		{
			if ($coupon[0]->val_type == 1)
			{
				$cval = ($coupon[0]->value / 100) * $totalamt;
			}
			else
			{
				$cval = $coupon[0]->value;
			}

			$camt = $totalamt - $cval;

			if ($camt <= 0)
			{
				$camt = 0;
			}

			$dis_totalamt = ($camt) ? $camt : $totalamt;
		}

		return $dis_totalamt;
	}

	/**
	 * Get subscription plan for the course.
	 *
	 * @return  obj  Subscription Info
	 *
	 * @since  1.0.0
	 */
	public function getSubsplan()
	{
		$db        = JFactory::getDBO();
		$input     = JFactory::getApplication()->input;
		$course_id = $input->get('course_id', '', 'INT');

		$query = "SELECT * FROM #__tjlms_subscription_plans WHERE course_id=" . $course_id;
		$db->setQuery($query);
		$subsplan = $db->Loadobjectlist('id');

		return $subsplan;
	}

	/**
	 * Get subscription plan for the course.
	 *
	 * @param   INT  $course_id  Course ID
	 *
	 * @return  ARRAY  Course Info
	 *
	 * @since  1.0.0
	 */
	public function getcourseinfo($course_id)
	{
		$db = JFactory::getDBO();

		$query = "SELECT * FROM #__tjlms_courses WHERE id=" . $course_id;
		$db->setQuery($query);
		$course_info = $db->loadobjectlist();

		$access_l = $course_info[0]->access;

		$user_access = JFactory::getUser()->getAuthorisedViewLevels();

		$course_info[0]->authorized = 0;

		if (in_array($access_l, $user_access))
		{
			$course_info[0]->authorized = 1;
		}

		$course_info[0]->creator_name = new stdClass;
		$userDetail = JFactory::getUser($course_info[0]->created_by);
		$course_info[0]->creator_name->name = $userDetail->name;
		$course_info[0]->creator_name->username = $userDetail->username;

		return $course_info;
	}

	/**
	 * Check if article exists
	 *
	 * @param   INT  $articleId  Article ID
	 *
	 * @return  INT  Article ID
	 *
	 * @since  1.0.0
	 */
	public function doesArticleExists($articleId)
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select('art.id');
		$query->from($db->quoteName('#__content', 'art'));
		$query->join('INNER', $db->quoteName('#__categories', 'cat') . 'ON(' . $db->quoteName('art.catid') . '=' . $db->quoteName('cat.id') . ') ');
		$query->where($db->quoteName('art.state') . '= 1');
		$query->where($db->quoteName('cat.published') . '= 1');
		$query->where($db->quoteName('art.id') . '=' . $articleId);

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$result = $db->loadResult();

		if (!$result)
		{
			return 0;
		}

		return 1;
	}

	/**
	 * Function used to check if the user has a suncription
	 *
	 * @param   INT  $userId    User ID
	 * @param   INT  $courseId  Course ID
	 *
	 * @return  boolean
	 *
	 * @since  1.0.0
	 */
	public function checkIfUserHasSubscription($userId, $courseId)
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName(array('id', 'end_time', 'state')));
		$query->from($db->quoteName('#__tjlms_enrolled_users'));
		$query->where($db->quoteName('user_id') . '=' . $userId);
		$query->where($db->quoteName('course_id') . '=' . $courseId);

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$result = $db->loadobject();

		$currentDate = JFactory::getDate();

		if (!empty($result))
		{
			// When enrollment is 1 and subcription is active  || enrolment status is -1 i.e order is pending. Cases when to redirect to course page.
			if (($result->state == 1 && $result->end_time >= $currentDate) || $result->state == 0)
			{
				return 1;
			}
		}
		else
		{
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('o.status', 'o.processor')));
			$query->from($db->quoteName('#__tjlms_orders') . 'as o');
			$query->join('INNER', $db->quoteName('#__tjlms_order_items') . 'as oi ON oi.order_id=o.id');
			$query->where('o.user_id=' . $userId);
			$query->where('o.course_id=' . $courseId);
			$query->order('o.id DESC');

			// Reset the query using our newly populated query object.
			$db->setQuery($query);

			// Load the results as a list of stdClass objects (see later for more options on retrieving data).
			$result = $db->loadobject();

			if (!$result)
			{
				return -1;
			}
			elseif ($result->status == 'C' || ($result->status == 'P' && $result->processor != ''))
			{
				return 1;
			}
		}

		return -1;
	}

	/**
	 * Method create an order with selected subscription plan
	 *
	 * @param   ARRAY  $orderdata  Order details
	 *
	 * @return  json  $data
	 *
	 * @since  1.1.4
	 */
	public function createSubscriptionOrder($orderdata)
	{
		$session    = JFactory::getSession();
		$com_params = JComponentHelper::getParams('com_tjlms');
		$user  = JFactory::getUser($orderdata['user_id']);

		if ($orderdata)
		{
			$orderdata['name']    = $user->name;
			$orderdata['email']   = $user->email;

			// PlanDate is used to store order item.
			$planData['course_id']       = $orderdata['course_id'];
			$planData['plan_id']         = $orderdata['plan_id'];
			$planData['original_amount'] = $this->getoriginalAmt($orderdata['plan_id']);
			$planData['coupon_code']     = $orderdata['coupon_code'];

			$allow_taxation = $com_params->get('allow_taxation');

			$amountData = $this->recalculatetotalamount($planData, $allow_taxation);
			$planData['original_amt']  = $amountData['original_amt'];
			$orderdata['original_amt'] = $amountData['original_amt'];

			if ($amountData['amt'] < 0)
			{
				$amountData['amt'] = 0;
				$amountData['fee'] = 0;
			}

			$orderdata['amount']          = $amountData['amt'];
			$orderdata['coupon_discount'] = $amountData['coupon_discount'];

			if (isset($amountData['order_tax']))
			{
				$orderdata['order_tax'] = $amountData['order_tax'];
			}
			else
			{
				$orderdata['order_tax'] = 0;
			}

			if (isset($amountData['order_tax']))
			{
				$orderdata['order_tax_details'] = $amountData['order_tax_details'];
			}
			else
			{
				$orderdata['order_tax_details'] = 0;
			}

			$orderdata['coupon_discount_details'] = $orderdata['coupon_code'];
			$orderdata['coupon_code']             = $orderdata['coupon_code'];
		}

		$lms_orderid = $session->get('lms_orderid');

		if (isset($lms_orderid))
		{
			require_once JPATH_SITE . '/components/com_tjlms/helpers/main.php';
			$comtjlmsHelper = new comtjlmsHelper;

			// @To do.
			$orderinfo = $comtjlmsHelper->getorderinfo($lms_orderid, 'step_select_subsplan');

			// Check if orderid is of this plan only
			if ($orderinfo['order_info']['0']->course_id == $orderdata['course_id'] and $orderinfo['order_info']['0']->status == 'I')
			{
				// Check if order is of this plan and is pending
				$orderdata['order_id'] = $lms_orderid;
			}
		}

		// Create Main order
		$order_id = $this->createMainOrder($orderdata);

		if ($order_id)
		{
			$session->set('lms_orderid', $order_id);
			$data['success']  = 1;
			$data['order_id'] = $order_id;
			$data['message']  = "Order Created Successfully";
		}
		else
		{
			$data['failure']  = 1;
			$data['message']  = "Something went wrong, order is not created";
		}

		// Create Order Items for this order
		$this->updateOrderItems($planData, $order_id);

		return $data;
	}

	/**
	 * Function save user billing info.
	 *
	 * @param   ARRAY  $orderdata  Order details
	 *
	 * @return  json  $data
	 *
	 * @since  1.1.4
	 */
	public function saveBillingInfo($orderdata)
	{
		$user  = JFactory::getUser($orderdata['user_id']);
		$session    = JFactory::getSession();
		$order_id                = $session->get('lms_orderid');
		$com_params = JComponentHelper::getParams('com_tjlms');
		$billing_data            = $orderdata['bill'];
		$billing_data['comment'] = $orderdata['comment'];

		if (!$user->id)
		{
			return false;
		}
		else
		{
			if ($order_id)
			{
				$orderInfo = array();

				$orderInfo['user_id'] = $user->id;

				// Update the order details.
				$this->updateOrderDetails($order_id, $orderInfo);

				/* On After order update trigger */
				$dispatcher = JDispatcher::getInstance();
				JPluginHelper::importPlugin('system', 'discount', true, null);
				$discountedprice = $dispatcher->trigger('onAfterOrderUpdate', array($order_id, $billing_data['country']));
				/* END*/
			}
		}

		// If order id present
		if ($order_id)
		{
			require_once JPATH_SITE . '/components/com_tjlms/helpers/main.php';
			$comtjlmsHelper = new comtjlmsHelper;

			$this->billingaddr($user->id, $billing_data, $order_id);
			$order = $comtjlmsHelper->getorderinfo($order_id);

			// If free plan(In case of coupon applied) then confirm automatically and redirect to Invoice View.
			if ($order['order_info']['0']->amount == 0)
			{
				$confirm_order                   = array();
				$confirm_order['buyer_email']    = '';
				$confirm_order['status']         = 'C';
				$confirm_order['processor']      = "Free_plan";
				$confirm_order['transaction_id'] = "";
				$confirm_order['raw_data']       = "";
				$confirm_order['order_id']       = $order_id;

				$paymenthelper = JPATH_ROOT . '/components/com_tjlms/models/payment.php';

				if (!class_exists('tjlmsModelpayment'))
				{
					JLoader::register('tjlmsModelpayment', $paymenthelper);
					JLoader::load('tjlmsModelpayment');
				}

				$tjlmsModelpayment = new tjlmsModelpayment;
				$tjlmsModelpayment->updateStatus($confirm_order, $order_id);

				// Add Table Path
				JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_tjlms/tables');

				$order_table = JTable::getInstance('Orders', 'TjlmsTable', array('dbo', $this->_db));
				$order_table->load(array('id' => $order_id));

				// After order table status update, count the used_count Check if coupon code is used
				if ($order_table->coupon_code)
				{
					// Load jlike reminders model to call api to send the reminders
					require_once JPATH_ADMINISTRATOR . '/components/com_tjlms/models/coupons.php';

					// Call the actual cron code which will send the reminders
					$model         = JModelLegacy::getInstance('Coupons', 'TjlmsModel');
					$model->updateCouponUsedcount($order_table->coupon_code);
				}

				$order_id_with_prefix = $order['order_info']['0']->orderid_with_prefix;
				$orderUrl = 'index.php?option=com_tjlms&view=orders&orderid=';
				$data['redirect_invoice_view'] = $comtjlmsHelper->tjlmsRoute($orderUrl . $order_id_with_prefix . '&processor=Free_plan', false);
			}
			else
			{
				$billpath = $comtjlmsHelper->getViewpath('com_tjlms', 'buy', 'default_payment');
				ob_start();
				include $billpath;
				$html = ob_get_contents();
				ob_end_clean();
				$data['payment_html'] = $html;
			}
		}

		$selected_gateways = $com_params->get('gateways');

		if (count($selected_gateways) == 1)
		{
			$data['single_gateway'] = $selected_gateways[0];
		}

		if ($order_id)
		{
			$data['success']  = 1;
			$data['order_id'] = $order_id;
			$data['message']  = "Billing Data saved succefully";
		}
		else
		{
			$data['failure']  = 1;
			$data['message']  = "Something went wrong, billing Data is not getting saved";
		}

		@ob_end_clean();

		return $data;
	}
}

<?php
/**
 * @version    SVN: <svn_id>
 * @package    JTicketing
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2016 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die(';)');
jimport('joomla.application.component.model');
jimport('joomla.database.table.user');

require_once JPATH_ADMINISTRATOR . '/components/com_tjvendors/models/vendors.php';

/**
 * Model for buy for creating order and other
 *
 * @package     JTicketing
 * @subpackage  component
 * @since       1.0
 */
class JticketingModelbuy extends JModelLegacy
{
	/**
	 * Constructor
	 *
	 * @since   1.0
	 */
	public function __construct()
	{
		parent::__construct();
		$this->jticketingmainhelper     = new jticketingmainhelper;
		$this->jticketingfrontendhelper = new jticketingfrontendhelper;

		$TjGeoHelper = JPATH_ROOT . DS . 'components/com_tjfields/helpers/geo.php';

		if (!class_exists('TjGeoHelper'))
		{
			JLoader::register('TjGeoHelper', $TjGeoHelper);
			JLoader::load('TjGeoHelper');
		}

		$this->TjGeoHelper = new TjGeoHelper;
		$this->_db         = JFactory::getDBO();
	}

	/**
	 * Retrieve details for a country
	 *
	 * @return  object  $country  Details
	 *
	 * @since   1.0
	 */
	public function getCountry()
	{
		return $this->TjGeoHelper->getCountryList();
	}

	/**
	 * Get Contry list from tjfields
	 *
	 * @param   integer  $country  country id
	 *
	 * @return  object state list
	 *
	 * @since   1.0
	 */
	public function getuserState($country)
	{
		return $this->TjGeoHelper->getRegionListFromCountryID($country);
	}

	/**
	 * Get billing data
	 *
	 * @param   integer  $orderid  country id
	 *
	 * @return  array state list
	 *
	 * @since   1.0
	 */
	public function getuserdata($orderid = 0)
	{
		$this->_db = JFactory::getDBO();

		$params   = JComponentHelper::getParams('com_jticketing');
		$user     = JFactory::getUser();
		$userdata = array();

		if ($orderid and !($user->id))
		{
			$query = "SELECT u.* FROM #__jticketing_users as u
			WHERE  u.order_id=" . $orderid . " order by u.id DESC LIMIT 0 , 1";
		}
		else
		{
			$query = "SELECT u.*
			FROM #__jticketing_users as u
			WHERE  u.user_id=" . $user->id . " order by u.id DESC LIMIT 0 , 1";
		}

		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();

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
			$userdata['ST']  = $row;
		}

		return $userdata;
	}

	/**
	 * Get Event data
	 *
	 * @return  array event list
	 *
	 * @since   1.0
	 */
	public function getEventdata()
	{
		$com_params             = JComponentHelper::getParams('com_jticketing');
		$integration            = $com_params->get('integration');
		$siteadmin_comm_per     = $com_params->get('siteadmin_comm_per');
		$guest_reg_id           = $com_params->get('guest_reg_id');
		$auto_fix_seats         = $com_params->get('auto_fix_seats');
		$currency               = $com_params->get('currency');
		$affect_js_native_seats = $com_params->get('affect_js_native_seats');
		$session                = JFactory::getSession();
		$input                  = JFactory::getApplication()->input;
		$post                   = $input->post;

		$eventid = $post->get('eventid');

		if (empty($eventid))
		{
			$eventid = $session->get('JT_eventid');
		}
		else
		{
			$eventid = $eventid;
		}

		if (empty($eventid))
		{
			$eventid = $input->get('eventid', '', 'INT');
		}

		if ($integration == 1)
		{
			$query = "SELECT * FROM #__community_events
		WHERE id = {$eventid}";
		}
		elseif ($integration == 2)
		{
			$query = "SELECT * FROM #__jticketing_events
		WHERE id={$eventid}";
		}
		elseif ($integration == 3)
		{
			$query = "SELECT event.*,DATE(FROM_UNIXTIME(event.dtstart)) as startdate,
			DATE(FROM_UNIXTIME(event.dtend)) as enddate FROM #__jevents_vevdetail as event
			WHERE evdet_id={$eventid}";
		}
		elseif ($integration == 4)
		{
			$query = "SELECT event.*,event_det.start as start,
			event_det.end as enddate
			FROM #__social_clusters as event
			JOIN #__social_events_meta as event_det
			ON event_det.cluster_id=event.id
			WHERE event.id={$eventid}";
		}

		$this->_db->setQuery($query);
		$result = $this->_db->loadobject();

		return $result;
	}

	/**
	 * Get Ticket Types
	 *
	 * @param   integer  $typeid  typeid
	 *
	 * @return  array state list
	 *
	 * @since   1.0
	 */
	public function getTicketTypes($typeid)
	{
		$query = "SELECT  price FROM  #__jticketing_types WHERE id=" . $typeid;
		$this->_db->setQuery($query);

		return $result = $this->_db->loadResult();
	}

	/**
	 * Apply coupon
	 *
	 * @param   integer  $originalamount  originalamount
	 * @param   integer  $coupon_code     coupon_code
	 *
	 * @return  array coupon amount
	 *
	 * @since   1.0
	 */
	public function applycoupon($originalamount, $coupon_code = '')
	{
		$coupon_code     = trim($coupon_code);
		$val             = 0;
		$coupon_discount = $this->getcoupon($coupon_code);

		if ($coupon_discount)
		{
			if ($coupon_discount[0]->val_type == 1)
			{
				$val = ($coupon_discount[0]->value / 100) * ($originalamount);
			}
			else
			{
				$val = $coupon_discount[0]->value;
			}

			$vars['coupon_discount_details'] = json_encode($coupon_discount);
		}

		$amt = $originalamount - $val;
		$vars['original_amt']    = $originalamount;
		$vars['amt']             = $amt;
		$vars['coupon_discount'] = $val;

		return $vars;
	}

	/**
	 * Create order
	 *
	 * @param   integer  $orderdata  orderdata for successed
	 * @param   integer  $step       step no
	 *
	 * @return  array coupon amount
	 *
	 * @since   1.0
	 */
	public function createOrder($orderdata = '', $step = '')
	{
		$input         = JFactory::getApplication()->input;
		$post          = $input->post;
		$user          = JFactory::getUser();
		$com_params    = JComponentHelper::getParams('com_jticketing');
		$email_options = $com_params->get('email_options');
		$session       = JFactory::getSession();
		$socialintegration    = $com_params->get('integrate_with', 'none');
		$integration          = $com_params->get('integration');
		$streamBuyTicket      = $com_params->get('streamBuyTicket', 0);
		$jteventHelper        = new jteventHelper;
		$eventid              = $post->get('eventid', '', 'INT');

		// For step 1
		if ($step == 'step_selectTicket')
		{
			require_once JPATH_SITE . "/components/com_jticketing/helpers/main.php";
			$jticketingmainhelper = new jticketingmainhelper;
			$eventdata = $jticketingmainhelper->getAllEventDetails($eventid);

			$orderdata = array();

			if (!$orderdata)
			{
				$orderdata['user_id']           = $user->id;
				$orderdata['name']              = $user->name;
				$orderdata['email']             = $user->email;
				$orderdata['parent_order_id']   = 0;
				$ticketcount                    = 0;
				$ticketdata['type_ticketcount'] = $post->get('type_ticketcount', '', 'ARRAY');
				$ticketdata['type_id']      = $post->get('type_id', '', 'ARRAY');

				if ($integration == '2')
				{
					if ($eventdata->online_events == '0')
					{
						foreach ($ticketdata['type_ticketcount'] as $key => $count)
						{
							$ticketcount += $count;
						}
					}
					else
					{
						$ticketdata['type_ticketcount'] = array($ticketdata['type_id'][0] => '1');
						$ticketcount = '1';
					}
				}
				else
				{
					foreach ($ticketdata['type_ticketcount'] as $key => $count)
					{
						$ticketcount += $count;
					}
				}

				$orderdata['no_of_tickets'] = $ticketcount;
				$ticketdata['coupon_code']  = $post->get('coupon_code', '', 'STRING');
				$allow_taxation             = $com_params->get('allow_taxation');
				$event_data['eventid']      = $eventid;

				// RecalculateAmount Based on TIcket Type and Ticket count
				$amountData                = $this->recalculatetotalamount($ticketdata, $allow_taxation, $event_data);
				$orderdata['original_amt'] = $amountData['original_amt'];

				if ($amountData['amt'] < 0)
				{
					$amountData['amt'] = 0;
					$amountData['fee'] = 0;
				}

				$orderdata['amount']          = $amountData['amt'];
				$orderdata['fee']             = $amountData['fee'];
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

				$orderdata['coupon_discount_details'] = $post->get('coupon_code', '', 'STRING');
				$orderdata['coupon_code']             = $post->get('coupon_code', '', 'STRING');
				$event_integration_id                 = $orderdata['integraton_id'] = $post->get('event_integraton_id', '', 'INT');
			}

			$JT_orderid              = $session->get('JT_orderid');
			$current_session_tickets = $session->get('type_ticketcount_current');

			if (empty($current_session_tickets))
			{
				$session->set('type_ticketcount_current', $ticketdata['type_ticketcount']);
				$session->set('type_ticketcount_old', $ticketdata['type_ticketcount']);
			}
			else
			{
				$session->set('type_ticketcount_old', $session->get('type_ticketcount_current'));
				$session->set('type_ticketcount_current', $ticketdata['type_ticketcount']);
			}

			$current_session_tickets = $session->get('type_ticketcount_current');
			$old_session_tickets     = $session->get('type_ticketcount_old');
			$removed_ticket_types = array();
			$added_ticket_types   = array();

			foreach ($current_session_tickets AS $key => $value)
			{
				foreach ($old_session_tickets AS $oldkey => $oldval)
				{
					if ($key == $oldkey and $value < $oldval)
					{
						$removed_ticket_types[$key] = $oldval - $value;
					}

					if ($key == $oldkey and $value > $oldval)
					{
						$added_ticket_types[$key] = $value - $oldval;
					}
				}
			}

			if (!empty($removed_ticket_types))
			{
				$ticketdata['removed_ticket_types'] = $removed_ticket_types;
			}

			if (isset($JT_orderid))
			{
				$orderinfo = $this->jticketingmainhelper->getorderinfo($JT_orderid);

				if (!empty($orderinfo))
				{
					// Check if orderid is of this event only
					if ($orderinfo['order_info']['0']->event_integration_id == $event_integration_id and $orderinfo['order_info']['0']->status == 'P')
					{
						// Check if order is of this event and is pending
						$orderdata['order_id'] = $JT_orderid;
					}
				}
			}

			// Create Main order
			$order_id = $this->jticketingfrontendhelper->createMainOrder($orderdata);

			if ($order_id)
			{
				$JT_orderdata[$event_integration_id] = $order_id;
				$session->set('JT_orderdata', $JT_orderdata);
				$session->set('JT_orderid', $order_id);
				$data['success']  = 1;
				$data['order_id'] = $order_id;
				$data['message']  = "Order Created Successfully";
			}

			// Create Order Items for this order
			$this->updateOrderItems($ticketdata, $order_id);
			$this->fields              = $this->jticketingfrontendhelper->getAllfields($eventid);
			$userentryfields_orderitem = $this->orderitems = $this->jticketingfrontendhelper->getOrderItems($order_id);

			if (!empty($userentryfields_orderitem))
			{
				$room_fee_selected = 0;

				foreach ($userentryfields_orderitem as $key => $orderitem)
				{
					$orderitems_id = $orderitem->id;

					if ($user->id)
					{
						$paramstopass['user_id'] = $user->id;
					}

					$attendee_id                           = $paramstopass['attendee_id'] = $orderitem->attendee_id;
					$paramstopass_ticket['order_items_id'] = $orderitems_id;

					// Get core and event specific field values  for this Attendee.
					$orderitem_fieldvalues = $this->jticketingfrontendhelper->getUserEntryField($paramstopass);

					if (!empty($orderitem_fieldvalues))
					{
						foreach ($orderitem_fieldvalues AS $key => $orderitem_fieldvalue)
						{
							foreach ($orderitem_fieldvalue as $value)
							{
								$final_order_items_value[$orderitem->attendee_id][$value->name] = $value->field_value;
							}
						}
					}

					// GetUniversal Field values for this Attendee.
					$orderitem_fieldvalues_universal = $this->jticketingfrontendhelper->getUniversalUserEntryField($paramstopass);

					if (!empty($orderitem_fieldvalues_universal))
					{
						foreach ($orderitem_fieldvalues_universal AS $key => $orderitem_fieldvalue_universal)
						{
							foreach ($orderitem_fieldvalue_universal as $value_universal)
							{
								$final_order_items_value[$orderitem->attendee_id][$value_universal->name] = $value_universal->field_value;
							}
						}
					}
				}
			}

			$billpath = $this->jticketingmainhelper->getViewpath('buy', 'default_attendee_data');
			ob_start();
			include $billpath;
			$html = ob_get_contents();
			ob_end_clean();

			$data['attendee_html'] = $html;
			echo json_encode($data);
			jexit();
		}

		// For step 2
		if ($step == 'save_step_selectAttendee')
		{
			$attendee_field = $post->get('attendee_field', '', 'ARRAY');

			foreach ($attendee_field as $attkey => $fields)
			{
				$res           = new StdClass;
				$res->id       = '';
				$res->owner_id = JFactory::getUser()->id;

				if (!empty($fields['attendee_id']))
				{
					$attendee_id = $res->id = $fields['attendee_id'];
				}
				else
				{
					if (!$this->_db->insertObject('#__jticketing_attendees', $res, 'id'))
					{
						echo $this->_db->stderr();

						return false;
					}

					// Firstly create User Entry Field
					$attendee_id = $this->_db->insertid();
				}

				$res              = new StdClass;
				$res->id          = '';
				$res->attendee_id = $attendee_id;

				// If order items id present update it
				if ($fields['order_items_id'])
				{
					$current_order_items[] = $fields['order_items_id'];
					$res->id               = $fields['order_items_id'];
					$insert_order_items_id = $fields['order_items_id'];
					$this->_db->updateObject('#__jticketing_order_items', $res, 'id');
				}
				else
				{
					// Insert new items
					if (!$this->_db->insertObject('#__jticketing_order_items', $res, 'id'))
					{
						echo $this->_db->stderr();

						return false;
					}

					$insert_order_items_id = $this->_db->insertid();
				}

				// Save Custom user Entry Fields
				foreach ($fields as $key => $field)
				{
					// Using id for Event specific custom fields
					$this->_db->setQuery('SELECT id FROM `#__jticketing_attendee_fields` WHERE id LIKE  "' . $key . '"');
					$field_id = $this->_db->loadResult();

					if ($field_id)
					{
						$field_source = "com_jticketing";
					}
					else
					{
						// Using name for Universal custom fields
						$this->_db->setQuery('SELECT id FROM `#__tjfields_fields` WHERE name LIKE  "' . $key . '"');
						$field_id     = $this->_db->loadResult();
						$field_source = "com_tjfields.com_jticketing.ticket";
					}

					if ($field_id)
					{
						$row             = new stdClass;
						$row->id         = '';
						$field_id_exists = 0;

						// Changed this for phpcs error
						$query = 'SELECT id FROM `#__jticketing_attendee_field_values` WHERE attendee_id="' . $attendee_id . '"';
						$query .= 'AND field_id=' . $field_id . ' AND field_source="' . $field_source . '"';

						// Important to use field source in query
						$this->_db->setQuery($query);
						$field_id_exists   = $this->_db->loadResult();
						$row->field_source = $field_source;
						$row->field_id     = $field_id;
						$row->attendee_id  = $attendee_id;

						if (is_array($field))
						{
							$field = implode('|', $field);
						}

						$row->field_value = $field;

						if ($field_id_exists)
						{
							$row->id = $field_id_exists;

							if (!$this->_db->updateObject('#__jticketing_attendee_field_values', $row, 'id'))
							{
							}
						}
						else
						{
							if (!$this->_db->insertObject('#__jticketing_attendee_field_values', $row, 'id'))
							{
							}
						}
					}
				}
			}
		}

		// For step 3 save billing information
		if ($step == 'save_step_billinginfo')
		{
			$order_id                = $session->get('JT_orderid');
			$billing_data            = $post->get('bill', '', 'ARRAY');
			$billing_data['comment'] = $post->get('jt_comment', '', 'STRING');

			// Save customer note
			$odata                   = new StdClass;
			$odata->id               = $order_id;
			$odata->customer_note    = $billing_data['comment'];

			if (!$this->_db->updateObject('#__jticketing_order', $odata, 'id'))
			{
			}

			// Handle guest checkout and on-the-fly registration.
			$checkout_method = $post->get('account_jt', '', 'STRING');

			// Guest chckout.
			if ($checkout_method == 'guest')
			{
				if ($order_id)
				{
					$orderInfo = array();

					$orderInfo['email'] = $billing_data['email1'];

					// Update the order details.
					$this->updateOrderDetails($order_id, $orderInfo);

					// Get order items for this order.
					$orderItems = $this->getOrderItems($order_id);

					// Update attendees with owner_email
					if (count($orderItems))
					{
						foreach ($orderItems as $oi)
						{
							if (!empty($com_params->collect_attendee_info_checkout))
							{
								$this->updateAttendeeOwner($oi->attendee_id, $owner_id = 0, $owner_email = $billing_data['email1']);
							}
						}
					}
				}
			}

			// Register new account.
			elseif ($checkout_method == 'register')
			{
				$userid = 0;
				$userid = $this->registerUser($billing_data);

				if (!$userid)
				{
					return false;
				}
				else
				{
					if ($order_id)
					{
						$orderInfo = array();

						$orderInfo['user_id'] = JFactory::getUser()->id;

						// Update the order details.
						$this->updateOrderDetails($order_id, $orderInfo);

						// Get order items for this order.
						$orderItems = $this->getOrderItems($order_id);

						// Update attendees with owner_email
						if (count($orderItems))
						{
							foreach ($orderItems as $oi)
							{
								if (!empty($com_params->collect_attendee_info_checkout))
								{
									$this->updateAttendeeOwner($oi->attendee_id, $owner_id = JFactory::getUser()->id, $owner_email = '');
								}
							}
						}
					}
				}
			}

			if ($order_id)
			{
				$this->billingaddr($user->id, $billing_data, $order_id);
				$order = $this->jticketingmainhelper->getorderinfo($order_id);

				// If free ticket then confirm automatically and redirect to Invoice View.
				if ($order['order_info']['0']->amount == 0)
				{
					$confirm_order                   = array();
					$confirm_order['buyer_email']    = '';
					$confirm_order['status']         = 'C';
					$confirm_order['processor']      = "Free_ticket";
					$confirm_order['transaction_id'] = "";
					$confirm_order['raw_data']       = "";
					$paymenthelper = JPATH_ROOT . DS . 'components' . DS . 'com_jticketing' . DS . 'models' . DS . 'payment.php';

					if (!class_exists('jticketingModelpayment'))
					{
						JLoader::register('jticketingModelpayment', $paymenthelper);
						JLoader::load('jticketingModelpayment');
					}

					$guest_email            = '';
					$jticketingModelpayment = new jticketingModelpayment;
					$jticketingModelpayment->updatesales($confirm_order, $order_id);

					// Updated for Adding entries in jomsocial confirmed members
					$member_id   = $jticketingModelpayment->getEventMemberid($order_id, 'C');
					$eventupdate = $jticketingModelpayment->eventupdate($order_id, $member_id);

					// For Guest user attach email
					if (!$order['order_info']['0']->user_id)
					{
						$guest_email = "&email=" . md5($order['order_info']['0']->user_email);
					}

					$Itemid     = $input->get('Itemid');
					$Itemid_str = "";

					if ($Itemid)
					{
						$Itemid_str = "&Itemid=" . $Itemid;
					}

					// Add entries to Reminder queue to send reminder for Event
					$reminder_data              = $this->jticketingmainhelper->getticketDetails($order['eventinfo']->id, $order['items']['0']->order_items_id);
					$reminder_data->ticketprice = $order['order_info']['0']->amount;
					$reminder_data->nofotickets = 1;
					$reminder_data->totalprice  = $order['order_info']['0']->amount;
					$reminder_data->eid         = $order['eventinfo']->id;

					// Added by Snehal - Send Ticket Email for free tickets
					$order_id                   = $order['order_info']['0']->id;

					if (in_array('ticket_email', $email_options))
					{
						$email = $this->jticketingmainhelper->sendmailnotify($order_id, 'afterordermail');
					}

					$eventupdate                = $jticketingModelpayment->addtoReminderQueue($reminder_data, $order);
					$order_id_with_prefix          = $order['order_info']['0']->orderid_with_prefix;
					require_once JPATH_SITE . "/components/com_jticketing/helpers/route.php";
					$JTRouteHelper = new JTRouteHelper;
					$red_url   = "index.php?option=com_jticketing&view=orders&sendmail=1&layout=order&orderid=";
					$red_url .= $order_id_with_prefix . "&processor=Free_ticket" . $Itemid_str . $guest_email;
					$data['redirect_invoice_view'] = $JTRouteHelper->JTRoute($red_url);

					$redirectEventsUrl   = "index.php?option=com_jticketing&view=events&layout=default";
					$data['redirect_events_view'] = $JTRouteHelper->JTRoute($redirectEventsUrl);

					if ($socialintegration != 'none')
					{
						// Add in activity.
						if ($streamBuyTicket == 1 and !empty($user->id))
						{
							$libclass    = $jteventHelper->getJticketSocialLibObj();
							$action      = 'streamBuyTicket';
							$eventLink   = '<a class="" href="' . $order['eventinfo']->event_url . '">' . $order['eventinfo']->summary . '</a>';
							$originalMsg = JText::sprintf('COM_JTICKETING_PURCHASED_TICKET', $eventLink);
							$libclass->pushActivity($user->id, $act_type = '', $act_subtype = '', $originalMsg, $act_link = '', $title = '', $act_access = 0);
						}
					}

					if ($integration == 2)
					{
						// If online event create user on adobe site and register for this event
						if ($reminder_data->online_events == 1)
						{
							$jtFrontendHelper = new Jticketingfrontendhelper;
							$venueDetail = $jtFrontendHelper->getVenue($reminder_data->venue);

							$eventParams = json_decode($reminder_data->jt_params, true);
							$venueParams = json_decode($venueDetail->params, true);
							$jt_params = new stdClass;

							$enroll_user = JFactory::getUser($user->id);
							$jt_params->user_id  = $user->id;
							$jt_params->name     = $enroll_user->name;
							$jt_params->email    = $enroll_user->email;
							$jt_params->password = $this->jticketingmainhelper->rand_str(8);
							$jt_params->meeting_url = $eventParams['event_url'];
							$jt_params->api_username = $venueParams['api_username'];
							$jt_params->api_password = $venueParams['api_password'];
							$jt_params->host_url = $venueParams['host_url'];
							$jt_params->sco_id = $eventParams['event_sco_id'];
							$jt_params->sco_url = $venueParams['sco_url'];

							// TRIGGER After create event
							$dispatcher = JDispatcher::getInstance();
							JPluginHelper::importPlugin('tjevents');
							$result = $dispatcher->trigger('tj_inviteUsers', array($jt_params));
						}
					}
				}
				else
				{
					$billpath = $this->jticketingmainhelper->getViewpath('buy', 'default_payment');
					ob_start();
					include $billpath;
					$html = ob_get_contents();
					ob_end_clean();
					$data['payment_html'] = $html;
				}
			}

			$data['success']  = 1;
			$data['order_id'] = $order_id;
			$data['message']  = "Billing Data saved succefully";
			echo json_encode($data);
			jexit();
		}
	}

	/**
	 * Update order Items in ajax calls in steps
	 *
	 * @param   integer  $ticketdata  orderdata for successed
	 * @param   integer  $orderid     order id
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function updateOrderItems($ticketdata, $orderid)
	{
		$session = JFactory::getSession();
		$this->_db->setQuery('SELECT id,order_id,attendee_id FROM #__jticketing_order_items WHERE order_id=' . $orderid);
		$orderitems = $this->_db->loadObjectlist();

		// Firstly Delete ticket types in order items that are removed
		if (!empty($orderitems))
		{
			if (!empty($ticketdata['removed_ticket_types']))
			{
				foreach ($ticketdata['removed_ticket_types'] as $key => $count)
				{
					if ($count > 0)
					{
						$query = "DELETE FROM #__jticketing_order_items	WHERE order_id=" . $orderid . " AND type_id=" . $key . "  LIMIT " . $count;
						$this->_db->setQuery($query);

						if (!$this->_db->execute())
						{
						}
					}
				}
			}
		}

		// $type_ids = $ticketdata['type_id'];

		foreach ($ticketdata['type_ticketcount'] as $key => $multipletickets)
		{
			$this->_db->setQuery('SELECT id FROM #__jticketing_order_items WHERE order_id=' . $orderid . " AND type_id=" . $key);
			$orderitem_idArr            = $this->_db->loadAssoclist();
			$resdetails                 = new stdClass;
			$resdetails->id             = '';
			$resdetails->order_id       = $orderid;
			$resdetails->ticketcount    = 1;
			$resdetails->type_id        = $key;
			$resdetails->payment_status = 'P';
			$resdetails->ticket_price   = $this->getTicketTypes($resdetails->type_id);

			// @TODO For Deposit Change This to deposit Fee.
			$resdetails->amount_paid    = $resdetails->ticket_price;
			$total_updated_count = 0;

			// Now update order items that already present
			if (!empty($orderitem_idArr))
			{
				foreach ($orderitem_idArr AS $key => $value)
				{
					$resdetails->id = $value['id'];

					if (!$this->_db->updateObject('#__jticketing_order_items', $resdetails, 'id'))
					{
						echo $this->_db->stderr();
					}

					$total_updated_count++;
				}
			}

			if ($total_updated_count)
			{
				$multipletickets = $multipletickets - $total_updated_count;
			}

			// Insert Newly Created order items
			for ($i = 0; $i < $multipletickets; $i++)
			{
				$resdetails->id = '';

				if (!$this->_db->insertObject('#__jticketing_order_items', $resdetails, 'id'))
				{
					echo $this->_db->stderr();
				}
			}
		}
	}

	/**
	 * Get Event xref id
	 *
	 * @param   integer  $eventid  eventid for com_community,easysocial
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function getEventrefid($eventid)
	{
		$com_params  = JComponentHelper::getParams('com_jticketing');
		$integration = $com_params->get('integration');

		if (!empty($eventid))
		{
			if ($integration == 1)
			{
				$query = "SELECT id FROM #__jticketing_integration_xref WHERE eventid = {$eventid} AND source='com_community'";
			}
			elseif ($integration == 2)
			{
				$query = "SELECT id FROM #__jticketing_integration_xref WHERE eventid = {$eventid} AND source='com_jticketing'";
			}
			elseif ($integration == 3)
			{
				$query = "SELECT id FROM #__jticketing_integration_xref WHERE eventid = {$eventid} AND source='com_jevents'";
			}

			$this->_db->setQuery($query);

			return $evxref_id = $this->_db->loadResult();
		}

		return;
	}

	/**
	 * Verify amount data for coupon code calculation and calculate final amount of order
	 *
	 * @param   ARRAY  $amountdata      amountdata
	 * @param   ARRAY  $allow_taxation  1 or 0
	 * @param   ARRAY  $event_data      Data
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function recalculatetotalamount($amountdata, $allow_taxation = 0, $event_data = '')
	{
		$eventid = $event_data['eventid'];

		// Get user specific commission data.
		$user_specific_comm = $this->getUserSpecificCommision($eventid);
		$com_params          = JComponentHelper::getParams('com_jticketing');

		$siteadmin_comm_per = isset($user_specific_comm->percent_commission)?$user_specific_comm->percent_commission:$com_params->get('siteadmin_comm_per');

		$siteadmin_comm_flat = isset($user_specific_comm->flat_commission) ? $user_specific_comm->flat_commission :$com_params->get('siteadmin_comm_flat');

		$siteadmin_comm_cap = $com_params->get('siteadmin_comm_cap');

		/*$siteadmin_comm_per  = $com_params->get('siteadmin_comm_per');
		$siteadmin_comm_flat = $com_params->get('siteadmin_comm_flat');*/

		$originalamt       = 0;
		$type_ticketcounts = $amountdata['type_ticketcount'];
		$typeids           = $amountdata['type_id'];

		// Calculate original Amt to pay Based on ticket Types And Price.
		foreach ($type_ticketcounts AS $key => $multipletickets)
		{
			$resdetails             = new stdClass;
			$resdetails->type_id    = $key;
			$resdetails->type_price = 0;
			$resdetails->type_price = $this->getTicketTypes($resdetails->type_id);

			// @TODO For Deposit Change This to deposit Fee.
			$originalamt += $resdetails->type_price * $multipletickets;
		}

		if (!empty($amountdata['coupon_code']))
		{
			$vars = $this->applycoupon($originalamt, $amountdata['coupon_code']);
		}
		else
		{
			$vars['original_amt']    = $originalamt;
			$vars['amt']             = $originalamt;
			$vars['coupon_code']     = $amountdata['coupon_code'];
			$vars['coupon_discount'] = 0;
		}

		// Calculated as 0.1+1  1
		if ($siteadmin_comm_cap < $vars['amt'] and $siteadmin_comm_cap > 0 )
		{
			$vars['fee'] = $siteadmin_comm_cap * $siteadmin_comm_per / 100;
		}
		else
		{
			$vars['fee'] = $vars['amt'] * $siteadmin_comm_per / 100;
		}

		if (isset($siteadmin_comm_flat) and $siteadmin_comm_flat > 0)
		{
			$fee = $vars['fee'] + $siteadmin_comm_flat;

			// If fee is 1.1 And amt to pay is 1 in that case apply only percentage commission
			if ($fee <= $vars['amt'])
			{
				$vars['fee'] = $fee;
			}
		}

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

		/* @TODO Sagar to do check for 0 value condition
		/ if (($res->processor=='paypal' || $res->processor=='adaptive_paypal') and $com_params->get('handle_transactions')==1)
		$vars['fee'] 	=0;
		else
		{*/

		return $vars;
	}

	/**
	 * Register new user while creating order
	 *
	 * @param   ARRAY  $regdata1  regdata1 contains user entered email
	 *
	 * @return  int    $userid
	 *
	 * @since   1.0
	 */
	public function registerUser($regdata1)
	{
		$regdata['fnam']       = $regdata1['fnam'];
		$regdata['user_name']  = $regdata1['email1'];
		$regdata['user_email'] = $regdata1['email1'];
		$registrationhelper = JPATH_ROOT . DS . 'components' . DS . 'com_jticketing' . DS . 'models' . DS . 'registration.php';

		if (!class_exists('jticketingModelregistration'))
		{
			JLoader::register('jticketingModelregistration', $registrationhelper);
			JLoader::load('jticketingModelregistration');
		}

		$jticketingModelregistration = new jticketingModelregistration;

		if (!$jticketingModelregistration->store($regdata))
		{
			return false;
		}

		$user = JFactory::getUser();

		return $userid = $user->id;
	}

	/**
	 * Calculate tax from and apply from taxation plugin
	 *
	 * @param   ARRAY  $vars  vars contains array for amt to apply tax
	 *
	 * @return  int    amount
	 *
	 * @since   1.0
	 */
	public function applytax($vars)
	{
		// Set Required Sessions
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('jticketingtax');
		$taxresults = $dispatcher->trigger('addTax', array($vars['amt']));

		// Call the plugin and get the result
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
	 * Update billing address while creating order
	 *
	 * @param   ARRAY  $uid              userid of order creator
	 * @param   ARRAY  $billingarr       billing data
	 * @param   ARRAY  $insert_order_id  order id to update billing data
	 *
	 * @return  int    $userid
	 *
	 * @since   1.0
	 */
	public function billingaddr($uid, $billingarr, $insert_order_id)
	{
		$this->_db->setQuery('SELECT order_id FROM #__jticketing_users WHERE order_id=' . $insert_order_id);
		$order_id = (string) $this->_db->loadResult();

		if ($order_id)
		{
			$query = "DELETE FROM #__jticketing_users	WHERE order_id=" . $insert_order_id;
			$this->_db->setQuery($query);

			if (!$this->_db->execute())
			{
			}
		}

		$row          = new stdClass;
		$row->user_id = $uid;

		if ($billingarr['email1'])
		{
			$row->user_email = $billingarr['email1'];
		}

		$row->address_type = 'BT';

		if ($billingarr['fnam'])
		{
			$row->firstname = $billingarr['fnam'];
		}

		$row->lastname = empty($billingarr['lnam']) ? '' : $billingarr['lnam'];

		if (!empty($billingarr['country']))
		{
			$row->country_code = $billingarr['country'];
		}

		if (!empty($billingarr['vat_num']))
		{
			$row->vat_number = $billingarr['vat_num'];
		}

		if (!empty($billingarr['addr']))
		{
			$row->address = $billingarr['addr'];
		}

		if (!empty($billingarr['city']))
		{
			$row->city = $billingarr['city'];
		}

		if (!empty($billingarr['state']))
		{
			$row->state_code = $billingarr['state'];
		}

		if (!empty($billingarr['zip']))
		{
			$row->zipcode = $billingarr['zip'];
		}

		if (!empty($billingarr['country_mobile_code']))
		{
			$row->country_mobile_code = $billingarr['country_mobile_code'];
		}

		if (!empty($billingarr['phon']))
		{
			$row->phone = $billingarr['phon'];
		}

		$row->approved = '1';
		$row->order_id = $insert_order_id;

		if (!$this->_db->insertObject('#__jticketing_users', $row, 'id'))
		{
			echo $this->_db->stderr();
		}

		$params = JComponentHelper::getParams('com_jticketing');

		// Save customer note in order table
		$order = new stdClass;

		if ($insert_order_id)
		{
			$order->id            = $insert_order_id;
			$order->customer_note = empty($billingarr['comment']) ? '' : $billingarr['comment'];

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

			if (!$this->_db->updateObject('#__jticketing_order', $order, 'id'))
			{
				echo $this->_db->stderr();
			}
		}

		// TRIGGER After Billing data save
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('system');
		$result = $dispatcher->trigger('jt_OnAfterBillingsaveData', array($billingarr, $_POST, $row->order_id, $uid));

		return $row->user_id;
	}

	/**
	 * Generate random no
	 *
	 * @param   INT  $length  length of random no
	 *
	 * @return  int  $random  random no
	 *
	 * @since   1.0
	 */

	public function _random($length = 5)
	{
		$salt   = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$len    = strlen($salt);
		$random = '';
		$stat = @stat(__FILE__);

		if (empty($stat) || !is_array($stat))
		{
			$stat = array(php_uname());
		}

		mt_srand(crc32(microtime() . implode('|', $stat)));

		for ($i = 0; $i < $length; $i++)
		{
			$random .= $salt[mt_rand(0, $len - 1)];
		}

		return $random;
	}

	/**
	 * Create session
	 *
	 * @param   ARRAY  $sessiondata  sessiondata contains
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function setSession($sessiondata)
	{
		$session = JFactory::getSession();

		foreach ($sessiondata AS $key => $value)
		{
			$session->set($key, $value);
		}
	}

	/**
	 * Clear session
	 *
	 * @param   ARRAY  $sessiondata  sessiondata contains
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function clearSession($sessiondata)
	{
		$session = JFactory::getSession();

		foreach ($sessiondata AS $key => $value)
		{
			$session->set($key, '');
		}
	}

	/**
	 * Set eventid in session
	 *
	 * @param   ARRAY  $post  post data for order
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function setSessionEventid($post)
	{
		$session = JFactory::getSession();
		$session->set('JT_eventid', 0);
		$this->clearSession();
		$input   = JFactory::getApplication()->input;
		$post    = $input->post;
		$eventid = $post->get('eventid');

		if (empty($eventid))
		{
			$eventid = $input->get('eventid', '', 'GET');
		}

		$session->set('JT_eventid', $eventid);
	}

	/**
	 * Build layout called mainly in ajax for order
	 *
	 * @return  void
	 *
	 * @since   1.0
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
	 * Validate coupon for date and other condition
	 *
	 * @param   INT  $c_code  coupon code
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function getcoupon($c_code)
	{
		$user = JFactory::getUser();
		$user_id = $user->id;

		if (empty($user_id))
		{
			$user_id = 0;
		}

		$where_user = $where_user_id = "";

		// If user is login then only check max per user conditiotn
		if ($user_id)
		{
			$where_user_cond = " AND  api.user_id=" . $user_id;
			$where_user      = " AND (max_per_user > (SELECT COUNT(api.coupon_code)
			FROM #__jticketing_order as api WHERE coupon_discount>0
			AND api.status LIKE 'C' AND api.coupon_code = " . $this->_db->quote($this->_db->escape($c_code)) . " " . $where_user_cond . ")
			OR max_per_user=0) ";
		}

		$query = "SELECT value,val_type
				FROM #__jticketing_coupon as cop
				WHERE
				state = 1
				AND code=" . $this->_db->quote($this->_db->escape($c_code)) . "
				AND	 (  ( NOW() BETWEEN from_date AND exp_date)
				OR from_date = '0000-00-00 00:00:00' )
				AND (max_use  > (SELECT COUNT(api.coupon_code)
				FROM #__jticketing_order as api WHERE coupon_discount>0
				AND api.status LIKE 'C' AND api.coupon_code =" . $this->_db->quote($this->_db->escape($c_code)) . ") OR max_use=0)
				" . $where_user;
		$this->_db->setQuery($query);
		$count = $this->_db->loadObjectList();

		return $count;
	}

	/**
	 * Check if joomla user exists
	 *
	 * @param   INT  $email  email of joomla user
	 *
	 * @return  Boolean true or false
	 *
	 * @since   1.0
	 */
	public function checkuserExistJoomla($email)
	{
		$user  = JFactory::getUser();
		$query = "SELECT id FROM #__users WHERE email LIKE '" . $email . "'";
		$this->_db->setQuery($query);
		$id = $this->_db->loadResult();

		if ($id)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Check if joomla user exists
	 *
	 * @param   INT  $dis_totalamt  dis_totalamt
	 *
	 * @return  INT  returns tax price
	 *
	 * @since   1.0
	 */
	public function afterTaxPrice($dis_totalamt)
	{
		$dispatcher = JDispatcher::getInstance();

		// Call the plugin and get the result
		JPluginHelper::importPlugin('jticketingtax');
		$taxresults = $dispatcher->trigger('addTax', array($dis_totalamt));

		return $taxresults;
	}

	/**
	 * Check if joomla user exists
	 *
	 * @param   INT  $totalamt  totalamt
	 * @param   INT  $c_code    coupon code
	 *
	 * @return  Boolean true or false
	 *
	 * @since   1.0
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
	 * Check if joomla user exists
	 *
	 * @param   INT  $order_id  order_id
	 *
	 * @return  Boolean true or false
	 *
	 * @since   1.0
	 */
	public function getEventownerEmail($order_id)
	{
		// Retrieve eventid
		$sql = "SELECT event_details_id FROM #__jticketing_order  WHERE id=" . $order_id;
		$this->_db->setQuery($sql);
		$eventid = $this->_db->LoadResult();

		// Retrieve paypalemail
		$sql = "SELECT paypalemail FROM #__jticketing_integration_xref WHERE id=" . $eventid;
		$this->_db->setQuery($sql);
		$email = $this->_db->LoadResult();

		return $email;
	}

	/**
	 * Check if joomla user exists
	 *
	 * @return  Boolean true or false
	 *
	 * @since   1.0
	 */
	public function getUpdatedBillingInfo()
	{
		// First update the current orderid with new logged in user id.
		$session  = JFactory::getSession();
		$order_id = $session->get('JT_orderid');

		if ($order_id)
		{
			$orderInfo = array();
			$orderInfo['user_id'] = JFactory::getUser()->id;

			// Update the order details.
			$this->updateOrderDetails($order_id, $orderInfo);

			// Get order items for this order.
			$orderItems = $this->getOrderItems($order_id);

			// Update attendees.
			if (count($orderItems))
			{
				foreach ($orderItems as $oi)
				{
					$this->updateAttendeeOwner($oi->attendee_id, $owner_id = JFactory::getUser()->id);
				}
			}
		}

		// Let's figure out and collect all data needed to return view layout HTML.
		$billpath = $this->jticketingmainhelper->getViewpath('buy', 'default_billing');
		$this->user     = JFactory::getUser();
		$this->country  = $this->getCountry();
		$this->userdata = $this->getuserdata();
		$this->params   = JComponentHelper::getParams('com_jticketing');
		$this->enable_bill_vat = $this->params->get('enable_bill_vat');
		$this->default_country = $this->params->get('default_country');
		$profile_import        = $this->params->get('profile_import');
		$this->tnc             = $this->params->get('tnc');
		$this->article         = $this->params->get('article');
		$JTicketingIntegrationsHelper = new JTicketingIntegrationsHelper;
		$cdata              = '';

		if ($profile_import)
		{
			$cdata = $JTicketingIntegrationsHelper->profileImport();
		}

		// Get user details for autofill.
		// $this->userbill = (isset($this->userdata['BT'])) ? $this->userdata['BT'] : $cdata['userbill'];

		// + Added by manoj
		$this->userbill = array();

		if (isset($this->userdata['BT']))
		{
			$this->userbill = $this->userdata['BT'];
		}
		elseif (is_array($cdata))
		{
			$this->userbill = $cdata['userbill'];
		}
		// ^ Changed by manoj END

		// Get HTML.
		ob_start();
		include $billpath;
		$html = ob_get_contents();
		ob_end_clean();

		$data                 = array();
		$data['billing_html'] = $html;

		echo json_encode($data);
		jexit();
	}

	/**
	 * Check if joomla user exists
	 *
	 * @param   INT    $orderid    orderid
	 * @param   ARRAY  $orderInfo  orderinfo array
	 *
	 * @return  Boolean true or false
	 *
	 * @since   1.0
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

		// Update order entry.
		if (!$this->_db->updateObject('#__jticketing_order', $obj, 'id'))
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
	 * Get order items
	 *
	 * @param   INT  $orderid  orderid of jticketing
	 *
	 * @return  orderItems  order item object
	 *
	 * @since   1.0
	 */
	public function getOrderItems($orderid)
	{
		$orderItems = '';
		$this->_db->setQuery('SELECT id, order_id, attendee_id FROM #__jticketing_order_items WHERE order_id=' . $orderid);
		$orderItems = $this->_db->loadObjectList();

		return $orderItems;
	}

	/**
	 * Update attendee owner
	 *
	 * @param   INT     $attendeeId  email of joomla user
	 * @param   INT     $ownerId     order creator ID
	 * @param   STRING  $ownerEmail  order creator Email
	 *
	 * @return  orderItems order item object
	 *
	 * @since   1.0
	 */
	public function updateAttendeeOwner($attendeeId, $ownerId = 0, $ownerEmail = '')
	{
		$obj = new stdClass;

		$obj->id = $attendeeId;

		if ($ownerId)
		{
			$obj->owner_id = $ownerId;
		}

		if ($ownerEmail)
		{
			$obj->owner_email = $ownerEmail;
		}

		if ($ownerId || $ownerEmail)
		{
			// Update order entry.
			if (!$this->_db->updateObject('#__jticketing_attendees', $obj, 'id'))
			{
				echo $this->_db->stderr();

				return false;
			}
			else
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Get User specific % commission and flat commission
	 *
	 * @param   Int  $eventid  Event ID
	 *
	 * @return Array of data
	 */
	public function getUserSpecificCommision($eventid)
	{
		$db      = JFactory::getDBO();

		if (!class_exists('Jticketingmainhelper'))
		{
			JLoader::register('Jticketingmainhelper', JPATH_ADMINISTRATOR . '/components/com_jgive/helpers/main.php');
			JLoader::load('Jticketingmainhelper');
		}

		$jticketingmainhelper = new jticketingmainhelper;

		$event_creator = $jticketingmainhelper->getEventCreator($eventid);

		// Get user specific % commission and flat commission set by Admin
		$query = $db->getQuery(true)
				->select('*')
				->from($db->qn('#__tj_vendors'))
				->where($db->qn('user_id') . ' = ' . $db->q($event_creator));
			$db->setQuery($query);
		$user_specific_data = $db->loadObject();

		return $user_specific_data;
	}
}

<?php
/**
 * @version     1.0.0
 * @package     com_hierarchy
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Parth Lawate <contact@techjoomla.com> - http://techjoomla.com
 */


// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Reschedules list controller class.
 */
class HierarchyControllerReschedules extends HierarchyController
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Reschedules', $prefix = 'HierarchyModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	function getTicketData()
    {
		$params = JComponentHelper::getParams('com_hierarchy');
		$reschedule_perfix = $params->get('reschedule_perfix', '');
		$jinput = JFactory::getApplication()->input;
		$ticketid_full = $jinput->get('ticketid');
		$language = JFactory::getLanguage();
		$language->load('com_jticketing');

		if (strpos($ticketid_full, JText::_('TICKET_PREFIX')) === false) {
			echo 2;jexit();
		}
		else
		{
			if (count(explode('-',$ticketid_full)) != 3)
			{
				echo 2;jexit();
			}
		}

		$ticketid = explode('-',$ticketid_full);
		$ticketid = $ticketid[1];
		$expected_ticket_id = JText::_('TICKET_PREFIX').$ticketid .'-'.$ticketid;

		if ($expected_ticket_id != $ticketid_full)
		{
			echo 1;jexit();
		}
		require_once JPATH_SITE . '/components/com_jticketing/helpers/main.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_jticketing/models/orders.php';
		$orderobj = new jticketingModelorders;
		$status    = $orderobj->get_order_status($ticketid);

		if ($status == 'D')
		{
			echo 3;jexit();
			// if already reschedule status is done.
		}
		
		$path = JPATH_ROOT . '/components/com_jticketing/helpers/main.php';
		require_once JPATH_COMPONENT . '/helpers/hierarchy.php';

		if (!class_exists('jticketingmainhelper'))
		{
			JLoader::register('jticketingmainhelper', $path);
			JLoader::load('jticketingmainhelper');
		}
		
		$obj = new jticketingmainhelper();
		$t_data = $obj->getorderinfo($ticketid);

		if (empty($t_data))
		{
			echo 1;jexit();
		}
		else
		{
			$ticketid = explode('-',$ticketid_full);
			$check_ticket_record_exits = HierarchyFrontendHelper::checkTicketExit($ticketid_full);
			$ticketid = $ticketid[1];

			$path = JPATH_ROOT . '/components/com_jticketing/helpers/main.php';
			require_once JPATH_COMPONENT . '/helpers/hierarchy.php';

			if (!class_exists('jticketingmainhelper'))
			{
				JLoader::register('jticketingmainhelper', $path);
				JLoader::load('jticketingmainhelper');
			}
			
			$obj = new jticketingmainhelper();
			$t_data = $obj->getorderinfo($ticketid);

			$profileData =  HierarchyFrontendHelper::getUserProfileData($t_data['order_info'][0]->user_id);

			$user = JFactory::getUser(HierarchyFrontendHelper::getUserManager($t_data['order_info'][0]->user_id));

			$ticketDiv['already'] = '';
			$show_reschedule_btn = 1;

			if ($t_data)
			{
				if ($check_ticket_record_exits)
				{
					$ticketDiv['already'] = '1';
					$t_history = HierarchyFrontendHelper::getTicketRescheduleData($t_data['order_info'][0]->user_id, $ticketid_full);

					if ($t_history)
					{
						$hustory = '';
						foreach ($t_history as $his)
						{
							$hustory .= '<tr>';
							$hustory .= '<td>'.$ticketid_full.'</td>';
							$hustory .= '<td>'.$t_data['eventinfo']->title.'</td>';
							$hustory .= '<td>'.JFactory::getDate($t_data['eventinfo']->startdate)->Format('d-m-Y').JText::_('COM_HIERARCHY_RESCHEDULES_DATE_TO').'<br> '.JFactory::getDate($t_data['eventinfo']->enddate)->Format('d-m-Y').'</td>';
							$hustory .= '<td>'.$t_data['eventinfo']->location.'</td>';

							if ($his->user == 1)
							{
								$userconf = 'Confirmed';
							}
							else
							{
								$userconf = 'Not Confirmed'; 
								$show_reschedule_btn = 0;
							}

							if ($his->bh == 2)
							{
								$bhconf = 'Approved'; 
							}
							else if ($his->bh == 1)
							{
								$bhconf = 'Declined'; 
							}
							else 
							{
								$bhconf = 'Not Responded'; 
								$show_reschedule_btn = 0;
							}

							if ($his->hr == 2)
							{
								$hrconf = 'Approved'; 
							}
							else if ($his->hr == 1)
							{
								$hrconf = 'Declined'; 
							}
							else
							{
								$hrconf = 'Not Responded'; 
								$show_reschedule_btn = 0;
							}
							$hustory .= '<td>'.$userconf.'</td>';
							$hustory .= '<td>'.$bhconf.'</td>';
							$hustory .= '<td>'.$hrconf.'</td>';
							$hustory .= '<td>'.JFactory::getDate($his->modified_on)->Format('d-m-Y').'</td>';
							$hustory .= '<td>'.$reschedule_perfix.$his->id.'</td>';
							$hustory .= '</tr>';
						}
						$ticketDiv['tr'] = $hustory;
					}
					else
					{
						$ticketDiv['tr'] = 0;
					}
					//echo json_encode($ticketDiv);
					//jexit();
				}
				$ticketDiv['ticket_id'] = JRequest::getVar('ticketid');
				$ticketDiv['uid'] = $t_data['order_info'][0]->user_id;
				$ticketDiv['name'] = $t_data['order_info'][0]->firstname.' '.$t_data['order_info'][0]->lastname;
				$ticketDiv['email'] = $t_data['order_info'][0]->user_email;
				$ticketDiv['bname'] = $t_data['eventinfo']->title;
				$ticketDiv['bcode'] = $t_data['eventinfo']->short_description;
				$ticketDiv['location'] = $t_data['eventinfo']->location;
				$ticketDiv['bdate'] = JFactory::getDate($t_data['eventinfo']->startdate)->Format('d-m-Y') ;
				$ticketDiv['edate'] = JFactory::getDate($t_data['eventinfo']->enddate)->Format('d-m-Y') ;
				$ticketDiv['manager'] = $user->name;
				$ticketDiv['manager_email'] = $user->email;
				$ticketDiv['nomination'] = $user->name;
				$ticketDiv['department'] = $profileData->department;
				$ticketDiv['sub_department'] = $profileData->sub_department;
				$ticketDiv['show_reschedule_btn'] = $show_reschedule_btn;
				$ticketDiv['cost'] = $t_data['items'][0]->price;



				// BH Email Id Fetach
				$user_profile_data = HierarchyFrontendHelper::getUserProfileData($t_data['order_info'][0]->user_id);

				$bh_user_id = $user_profile_data->business_head;
				if ($bh_user_id)
				{
					$bh_user_data = JFactory::getUser($bh_user_id);
					$bh_name = $bh_user_data->name;
					$bh_email = $bh_user_data->email;
					$ticketDiv['bh'] = $bh_email;
				}
				else
				{
					$ticketDiv['bh'] = '';
				}
				
				$future = strtotime(date("Y-m-d", strtotime($t_data['eventinfo']->startdate)));
				$timefromdb =strtotime(date('Y-m-d')); //source time
				$timeleft = $future-$timefromdb;
				$daysleft = round((($timeleft/24)/60)/60); 

				
				$ticketDiv['daysleft'] = $daysleft;

				echo json_encode($ticketDiv);
				jexit();
			}
			else
			{
				echo 1;
				jexit();
			}
		}
	}
	function save()
	{
		$app   = JFactory::getApplication();
		$formfield = $app->input->post;
		
		require_once JPATH_COMPONENT . '/helpers/hierarchy.php';
		$check_ticket_exits = HierarchyFrontendHelper::getUserId($formfield->get('ticket_id'));
		if ($check_ticket_exits)
		{
			//JFactory::getApplication()->enqueueMessage(JText::_('COM_HIERARCHY_RESCHEDULES_ALREDY_DONE'), 'error');
			$this->setRedirect('index.php?option=com_hierarchy&view=reschedules&layout=form');
		}

		$model = $this->getModel();
		if (!$model->store($formfield))
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_HIERARCHY_RESCHEDULES_SAVE_ERROR'), 'error');
			$this->setRedirect('index.php?option=com_hierarchy&view=reschedules&layout=form');
		}
		else
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_HIERARCHY_RESCHEDULES_SAVE'), 'message');
			$this->setRedirect('index.php?option=com_hierarchy&view=reschedules&layout=form');
		}
	}
	function setDeclineReason()
	{
		$send_email = 0;
		$params = JComponentHelper::getParams('com_hierarchy');
		$reschedule_perfix = $params->get('reschedule_perfix', '');
		$hr_email = $params->get('hrhead', '');
		$mainframe  = JFactory::getApplication();
		$mailfrom = $mainframe->getCfg('mailfrom');
		$fromname = $mainframe->getCfg('fromname');

		$db = JFactory::getDbo();

		$jinput = JFactory::getApplication()->input;
		$reschedule_id = $jinput->get('rid');
		$status = $jinput->get('status');
		$ticketidcoded = $jinput->get('ticketidcoded');
		$reason = $jinput->get('reason','','string');
		$user = $jinput->get('user');
		
		$insertObj             = new stdClass;
		$insertObj->id         = $reschedule_id;
		if ($user == 'bh')
		{
			$insertObj->bh         = 1;
			$insertObj->decline_reason_bh = $reason;
		}
		else if ($user == 'hr')
		{
			$insertObj->hr         = 1;
			$insertObj->decline_reason_hr = $reason;
		}
		else
		{
			$insertObj->hr         = 1;
			$insertObj->bh         = 1;
			$insertObj->decline_reason_hr = $reason;
			$insertObj->decline_reason_bh = $reason;
		}
		
		$insertObj->modified_on = JHtml::date($input = 'now', 'Y-m-d h:i:s', false);
		
		
		
		if($db->updateObject('#__hierarchy_reschedule', $insertObj, 'id'))
		{	
			$params = JComponentHelper::getParams('com_hierarchy');
			$reschedule_perfix = $params->get('reschedule_perfix', '');
			$hr_email = $params->get('hrhead', '');
			require_once JPATH_COMPONENT_SITE . '/helpers/hierarchy.php';
			$ticketData = HierarchyFrontendHelper::getTicketData($ticketidcoded,$reschedule_id);
			$bh_status = $ticketData->bh;
			
			// check hr already update status or not
			if ($user == 'hr')
			{
				if ($bh_status != 0)
				{
					$send_email = 1;
					//~ if ($bh_status == 2 && $status == 1)
					//~ {
						//~ // decline mail
						//~ $status = 'decline';
					//~ }
					//~ else
					//~ {
						//~ // approved mail
						//~ $status = 'approved';
					//~ }
					// send mail to user
					//echo 'send mail';
					$status = 'decline';
				}
				if ($status == 1)
				{
					$user_id = $ticketData->user_id;
					$user_data = JFactory::getUser($user_id);
					$emailSubject =  JText::_('COM_HIERARCHY_RESCHEDULES_USER_STATUS_EMAIL_SUBJECT');

					$manager_data = JFactory::getUser(HierarchyFrontendHelper::getUserManager($user_id));


					$rowBody = '<p>Hi {user_name},<br />&nbsp;<br />Below is the status for your rescheduling request with ID {reschedule_no}.</p>
	<p>Business Head: {status_bh}<br />Chief of HR: {status_hr}<br />Reason (if Declined): {reason_hr}</p>
	<p>Kindly ensure that all the approvals are provided at least 3 days before the start date of your batch.</p>
	<p>Regards,<br />L&amp;D</p>';

					$replaceArr                    = new stdClass;
					$replaceArr->user_name         = $user_data->name;
					$replaceArr->ticket_id         = '' ;
					$replaceArr->confirmation_link = '';
					$replaceArr->user_email = '' ;
					$replaceArr->batch_name = '' ;
					$replaceArr->cost = '' ;
					$replaceArr->reason = '' ;
					$replaceArr->bh_head_name = '' ;
					$replaceArr->hr_head_name = '' ;
					$replaceArr->status = '' ;
					$replaceArr->manager_name = '';
					$replaceArr->department = '';
					$replaceArr->sub_department = '';
					$replaceArr->batch_code = '';
					$replaceArr->batch_date_time = '';
					$replaceArr->batch_location = '';
					$replaceArr->reschedule_no = $reschedule_perfix.$ticketData->id;
					$replaceArr->status_bh = 'Not Responded';
					$replaceArr->reason_bh = '';
					$replaceArr->status_hr = 'Declined';
					$replaceArr->reason_hr = $ticketData->decline_reason_hr;

					$emailBody = HierarchyFrontendHelper::EmailBody($replaceArr, $rowBody);
					
					$cc[] = $params->get('reschedule_manager_email');
					$cc[] = $manager_data->email;

					$email = HierarchyFrontendHelper::RescheduleEmail($mailfrom, $fromname, $user_data->email, $emailSubject, $emailBody, $cc);
				}
				if ($status == 'decline')
				{
					$user_id = $ticketData->user_id;
					$user_data = JFactory::getUser($user_id);
					$emailSubject =  JText::_('COM_HIERARCHY_RESCHEDULES_USER_STATUS_EMAIL_SUBJECT');
					
					$manager_data = JFactory::getUser(HierarchyFrontendHelper::getUserManager($user_id));

					

					$replaceArr                    = new stdClass;
					$replaceArr->user_name         = $user_data->name;
					$replaceArr->ticket_id         = '' ;
					$replaceArr->confirmation_link = '';
					$replaceArr->user_email = '' ;
					$replaceArr->batch_name = '' ;
					$replaceArr->cost = '' ;
					$replaceArr->reason = '' ;
					$replaceArr->bh_head_name = '' ;
					$replaceArr->hr_head_name = '' ;
					$replaceArr->status = '' ;
					$replaceArr->manager_name = '';
					$replaceArr->department = '';
					$replaceArr->sub_department = '';
					$replaceArr->batch_code = '';
					$replaceArr->batch_date_time = '';
					$replaceArr->batch_location = '';
					$replaceArr->reschedule_no = $reschedule_perfix.$ticketData->id;
					if ($bh_status == 1)
					{
						$replaceArr->status_bh = 'Declined';
						$replaceArr->reason_bh = $ticketData->decline_reason_bh;
						$rowBody = '<p>Hi {user_name},<br />&nbsp;<br />Below is the status for your rescheduling request with ID {reschedule_no}.</p>
	<p>Business Head: {status_bh}<br />Reason (if Delined): {reason_bh}<br />Chief of HR: {status_hr}<br />Reason (if Declined): {reason_hr}</p>
	<p>Kindly ensure that all the approvals are provided at least 3 days before the start date of your batch.</p>
	<p>Regards,<br />L&amp;D</p>';
					}
					else
					{
						$replaceArr->status_bh = 'Approved';
						$replaceArr->reason_bh = '';
						$rowBody = '<p>Hi {user_name},<br />&nbsp;<br />Below is the status for your rescheduling request with ID {reschedule_no}.</p>
	<p>Business Head: {status_bh}<br />Chief of HR: {status_hr}<br />Reason (if Declined): {reason_hr}</p>
	<p>Kindly ensure that all the approvals are provided at least 3 days before the start date of your batch.</p>
	<p>Regards,<br />L&amp;D</p>';
					}
					$replaceArr->status_hr = 'Declined';
					$replaceArr->reason_hr = $ticketData->decline_reason_hr;
					
					$manager_data = JFactory::getUser(HierarchyFrontendHelper::getUserManager($user_id));
					$cc[] = $params->get('reschedule_manager_email');
					$cc[] = $manager_data->email;

					$emailBody = HierarchyFrontendHelper::EmailBody($replaceArr, $rowBody);

					$email = HierarchyFrontendHelper::RescheduleEmail($mailfrom, $fromname, $user_data->email, $emailSubject, $emailBody, $cc);
				}
				
			}
			else if ($user == 'bh')
			{
					// check hr already update status or not
					$hr_status = $ticketData->hr;
					if ($hr_status != 0)
					{
						$send_email = 1;
						$status = 'decline';
						
					}
					if ($status == 1)
					{
						$user_id = $ticketData->user_id;
						$user_data = JFactory::getUser($user_id);
						$emailSubject =  JText::_('COM_HIERARCHY_RESCHEDULES_USER_STATUS_EMAIL_SUBJECT');

						$rowBody = '<p>Hi {user_name},<br />&nbsp;<br />Below is the status for your rescheduling request with ID {reschedule_no}.</p>
		<p>Business Head: {status_bh}<br />Reason (if Declined): {reason_bh}<br />Chief of HR: {status_hr}</p>
		<p>Kindly ensure that all the approvals are provided at least 3 days before the start date of your batch.</p>
		<p>Regards,<br />L&amp;D</p>';

						$replaceArr                    = new stdClass;
						$replaceArr->user_name         = $user_data->name;
						$replaceArr->ticket_id         = '' ;
						$replaceArr->confirmation_link = '';
						$replaceArr->user_email = '' ;
						$replaceArr->batch_name = '' ;
						$replaceArr->cost = '' ;
						$replaceArr->reason = '' ;
						$replaceArr->bh_head_name = '' ;
						$replaceArr->hr_head_name = '' ;
						$replaceArr->status = '' ;
						$replaceArr->manager_name = '';
						$replaceArr->department = '';
						$replaceArr->sub_department = '';
						$replaceArr->batch_code = '';
						$replaceArr->batch_date_time = '';
						$replaceArr->batch_location = '';
						$replaceArr->reschedule_no = $reschedule_perfix.$ticketData->id;
						$replaceArr->status_bh = 'Declined';
						$replaceArr->reason_bh = $ticketData->decline_reason_bh;
						$replaceArr->status_hr = 'Not Responded';
						$replaceArr->reason_hr = '';
						
						$cc[] = $params->get('reschedule_manager_email');
						$cc[] = $manager_data->email;

						$emailBody = HierarchyFrontendHelper::EmailBody($replaceArr, $rowBody);
						
						$manager_data = JFactory::getUser(HierarchyFrontendHelper::getUserManager($user_id));
						$cc[] = $params->get('reschedule_manager_email');
						$cc[] = $manager_data->email;

						$email = HierarchyFrontendHelper::RescheduleEmail($mailfrom, $fromname, $user_data->email, $emailSubject, $emailBody, $cc);
					}
				
					if ($status == 'decline')
					{
						$user_id = $ticketData->user_id;
						$user_data = JFactory::getUser($user_id);
						$emailSubject =  JText::_('COM_HIERARCHY_RESCHEDULES_USER_STATUS_EMAIL_SUBJECT');

						

						$replaceArr                    = new stdClass;
						$replaceArr->user_name         = $user_data->name;
						$replaceArr->ticket_id         = '' ;
						$replaceArr->confirmation_link = '';
						$replaceArr->user_email = '' ;
						$replaceArr->batch_name = '' ;
						$replaceArr->cost = '' ;
						$replaceArr->reason = '' ;
						$replaceArr->bh_head_name = '' ;
						$replaceArr->hr_head_name = '' ;
						$replaceArr->status = '' ;
						$replaceArr->manager_name = '';
						$replaceArr->department = '';
						$replaceArr->sub_department = '';
						$replaceArr->batch_code = '';
						$replaceArr->batch_date_time = '';
						$replaceArr->batch_location = '';
						$replaceArr->reschedule_no = $reschedule_perfix.$ticketData->id;
						$replaceArr->status_bh = 'Declined';
						$replaceArr->reason_bh = $ticketData->decline_reason_bh;
						//~ $replaceArr->status_hr = 'Declined';
						//~ $replaceArr->reason_hr = $ticketData->decline_reason_hr;
						if ($hr_status == 1)
						{
							$replaceArr->status_hr = 'Declined';
							$replaceArr->reason_hr = $ticketData->decline_reason_hr;
							$rowBody = '<p>Hi {user_name},<br />&nbsp;<br />Below is the status for your rescheduling request with ID {reschedule_no}.</p>
		<p>Business Head: {status_bh}<br />Reason (if Delined): {reason_bh}<br />Chief of HR: {status_hr}<br />Reason (if Declined): {reason_hr}</p>
		<p>Kindly ensure that all the approvals are provided at least 3 days before the start date of your batch.</p>
		<p>Regards,<br />L&amp;D</p>';
						}
						else
						{
							$replaceArr->status_hr = 'Approved';
							$replaceArr->reason_hr = '';
							$rowBody = '<p>Hi {user_name},<br />&nbsp;<br />Below is the status for your rescheduling request with ID {reschedule_no}.</p>
		<p>Business Head: {status_bh}<br />Reason (if Delined): {reason_bh}<br />Chief of HR: {status_hr}</p>
		<p>Kindly ensure that all the approvals are provided at least 3 days before the start date of your batch.</p>
		<p>Regards,<br />L&amp;D</p>';
						}

						$emailBody = HierarchyFrontendHelper::EmailBody($replaceArr, $rowBody);
						
						$manager_data = JFactory::getUser(HierarchyFrontendHelper::getUserManager($user_id));
						$cc[] = $params->get('reschedule_manager_email');
						$cc[] = $manager_data->email;

						$email = HierarchyFrontendHelper::RescheduleEmail($mailfrom, $fromname, $user_data->email, $emailSubject, $emailBody, $cc);
					}
			}
			//~ else
			//~ {
				//~ $user_id = $ticketData->user_id;
				//~ $user_data = JFactory::getUser($user_id);
				//~ $emailSubject =  JText::_('COM_HIERARCHY_RESCHEDULES_USER_STATUS_EMAIL_SUBJECT');
//~ 
				//~ $rowBody = '<p>Hi {user_name},<br />&nbsp;<br />Below is the status for your rescheduling request with ID {reschedule_no}.</p>
//~ <p>Business Head: {status_bh}<br />Reason (if Delined): {reason_bh}<br />Chief of HR: {status_hr}<br />Reason (if Declined): {reason_hr}</p>
//~ <p>Kindly ensure that all the approvals are provided at least 3 days before the start date of your batch.</p>
//~ <p>Regards,<br />L&amp;D</p>';
//~ 
				//~ $replaceArr                    = new stdClass;
				//~ $replaceArr->user_name         = $user_data->name;
				//~ $replaceArr->ticket_id         = '' ;
				//~ $replaceArr->confirmation_link = '';
				//~ $replaceArr->user_email = '' ;
				//~ $replaceArr->batch_name = '' ;
				//~ $replaceArr->cost = '' ;
				//~ $replaceArr->reason = '' ;
				//~ $replaceArr->bh_head_name = '' ;
				//~ $replaceArr->hr_head_name = '' ;
				//~ $replaceArr->status = '' ;
				//~ $replaceArr->manager_name = '';
				//~ $replaceArr->department = '';
				//~ $replaceArr->sub_department = '';
				//~ $replaceArr->batch_code = '';
				//~ $replaceArr->batch_date_time = '';
				//~ $replaceArr->batch_location = '';
				//~ $replaceArr->reschedule_no = $reschedule_perfix.$ticketData->id;
				//~ $replaceArr->status_bh = 'Declined';
				//~ $replaceArr->reason_bh = $ticketData->decline_reason_bh;
				//~ $replaceArr->status_hr = 'Declined';
				//~ $replaceArr->reason_hr = $ticketData->decline_reason_hr;
//~ 
				//~ $emailBody = HierarchyFrontendHelper::EmailBody($replaceArr, $rowBody);
				//~ 
				//~ $manager_data = JFactory::getUser(HierarchyFrontendHelper::getUserManager($user_id));
					//~ $cc[] = $params->get('reschedule_manager_email');
					//~ $cc[] = $manager_data->email;
//~ 
				//~ $email = HierarchyFrontendHelper::RescheduleEmail($mailfrom, $fromname, $user_data->email, $emailSubject, $emailBody, $cc);
				//~ $send_email = 1;
			//~ }
			
			if ($send_email == 1)
			{
				
			$user_id = $ticketData->user_id;
			$user_data = JFactory::getUser($user_id);
			$manager = JFactory::getUser(HierarchyFrontendHelper::getUserManager($user_id));

			$mainframe  = JFactory::getApplication();
			$mailfrom = $mainframe->getCfg('mailfrom');
			$fromname = $mainframe->getCfg('fromname');

			$emailSubject =  JText::_('COM_HIERARCHY_RESCHEDULES_USER_STATUS_EMAIL_SUBJECT');

	$url = 
	JURI::root().'index.php?option=com_hierarchy&view=hierarchys&rid='.$ticketData->id.'&tid='.base64_encode($reschedule_perfix.$ticketData->id).'&tmpl=component';
	$rowBody_approve = 
	JText::sprintf("COM_HIERARCHY_RESCHEDULES_USER_APPROVE_FINAL_EMAIL",$url);
	
			$rowBody_decline ='<p style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">Hi {user_name}</span></p><br>
			
			<p style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">Your reschedule request with ID {reschedule_no} has been rejected.</span></p>
			<p style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><span>&nbsp;</span></p>
			<p style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">Kindly attend the Learning Lab program as previously scheduled. For further clarification, mail us at </span><a href="mailto:khushboo.khosla@bajajfinserv.in"><span style="font-size: 12px; font-family: "Open Sans"; color: #1155cc; text-decoration: underline; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">khushboo.khosla@bajajfinserv.in</span></a><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">.</span></p>
			<p style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><span>&nbsp;</span></p>
			<p style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">Regards,</span></p>
			<p style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><span id="docs-internal-guid-0ff25d6f-ba6a-5f8d-2c4d-05aa9e57c45b"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">L&amp;D</span></span></p>';

			$groupid = $params->get('groupid', '','INT');

			$replaceArr                    = new stdClass;
			$replaceArr->user_name         = $user_data->name ;
			$replaceArr->ticket_id         = $ticketidcoded ;
			$replaceArr->confirmation_link = '' ;
			$replaceArr->user_email = $user_data->email;
			$replaceArr->batch_name = '' ;
			$replaceArr->cost = '' ;
			$replaceArr->reason = '' ;
			$replaceArr->bh_head_name = '' ;
			$replaceArr->hr_head_name = '' ;
			$replaceArr->status = $status ;
			$replaceArr->manager_name = $manager->name;
			$replaceArr->department = '';
			$replaceArr->sub_department = '';
			$replaceArr->batch_code = '';
			$replaceArr->batch_date_time = '';
			$replaceArr->batch_location = '';
			$replaceArr->reschedule_no = $reschedule_perfix.$ticketData->id;
			$replaceArr->status_bh = '';
			$replaceArr->reason_bh = '';
			$replaceArr->status_hr = '';
			$replaceArr->reason_hr = '';

			$emailBody = HierarchyFrontendHelper::EmailBody($replaceArr, $rowBody_decline);

			// Email to reschedule user
			//$cc[] = $manager->email;

			// Email to Training Admin

			//~ $training_admins = HierarchyFrontendHelper::getTrainingAdmin($groupid);
//~ 
			//~ if ($training_admins)
			//~ {
				//~ $i = 0;
				//~ foreach ($training_admins as $adminIds)
				//~ {
					//~ $admin_data = JFactory::getUser($adminIds);
					//~ $cc[] = $admin_data->email;
				//~ }
			//~ }
			//~ 
			//~ $email = HierarchyFrontendHelper::RescheduleEmail($mailfrom, $fromname, $manager->email, $emailSubject, $emailBody, $cc);
			
			$cc[] = $params->get('reschedule_manager_email');
			$cc[] = $manager->email;
			
			$emailBody = HierarchyFrontendHelper::EmailBody($replaceArr, $rowBody_decline);
			//print_r($emailBody);
			//print_r($cc);die;
			$emailSubject =  JText::_('COM_HIERARCHY_RESCHEDULES_CONFIRM_EMAIL_USER_SUBJECT_REJECTION');
			
			$email = HierarchyFrontendHelper::RescheduleEmail($mailfrom, $fromname, $user_data->email, $emailSubject, $emailBody, $cc);


		}
			echo 1;
			jexit();
		}
		else
		{
			echo 0;
			jexit();
		}
	}
    
}

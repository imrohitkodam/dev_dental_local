<?php
/**
 * @version     1.0.0
 * @package     com_hierarchy
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Parth Lawate <contact@techjoomla.com> - http://techjoomla.com
 */
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
require_once JPATH_COMPONENT_SITE . '/helpers/hierarchy.php';
require_once JPATH_COMPONENT_SITE . '/helpers/confirmation.php';
JHtml::script(JURI::root().'media/jui/js/jquery.min.js');
JHtml::script(JURI::root().'components/com_hierarchy/assets/js/confirmation.js');

$document = JFactory::getDocument();
$document->addScriptDeclaration('
var pathroot = "'.JURI::root().'"
');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
$jinput = JFactory::getApplication()->input;

$reschedule_id = $jinput->get('ticketid', '', 'RAW');
$rid = $jinput->get('rid', '', 'RAW');

$params = JComponentHelper::getParams('com_hierarchy');
$reschedule_perfix = $params->get('reschedule_perfix', '');
$hr_email = $params->get('hrhead', '');
$mainframe  = JFactory::getApplication();
$mailfrom = $mainframe->getCfg('mailfrom');
$fromname = $mainframe->getCfg('fromname');

if ($reschedule_id)
{
	$reschedule_id = base64_decode($reschedule_id);
	$reschedule_id = str_replace($reschedule_perfix,"",$reschedule_id);
	$ticketidcoded = HierarchyFrontendHelper::getTicketID($reschedule_id);
}

$user = $jinput->get('user','','string');
$status = $jinput->get('ad','','INT');

$send_email = '';
$ticketid = '';
if ($ticketidcoded)
{
	$ticketData = HierarchyFrontendHelper::getTicketData($ticketidcoded, $rid);
	$user_id = $ticketData->user_id;
	$user_data = JFactory::getUser($user_id);

	$ticketid = explode('-',$ticketidcoded);
	$ticketid = $ticketid[1];
	
	$user_profile_data = HierarchyFrontendHelper::getUserProfileData($ticketData->user_id);
	
	$manager_data = JFactory::getUser(HierarchyFrontendHelper::getUserManager($ticketData->user_id));

	$bh_user_id = $user_profile_data->business_head;
	if ($bh_user_id)
	{
		$bh_user_data = JFactory::getUser($bh_user_id);
		$bh_name = $bh_user_data->name;
		$bh_email = $bh_user_data->email;	
	}
	if ($hr_email == $bh_email)
	{
		$sameuser = '';
	}
	else
	{
		$sameuser = $user;
	}

	// 1. update db
	
	// status is 1 : decline
	// status is 2 : approve
	if ($hr_email == $bh_email)
	{
		if ($ticketData->hr == 0)
		{
				if ($status == 2)
				{
					// approve 2
					ConfirmationHelper::updateRescheduleData($reschedule_id, 
					$hr = 2, $bh = 2);
					
					// check hr already update status or not
					$newtdata = HierarchyFrontendHelper::getTicketData($ticketidcoded, $rid);
					$bh_status = $newtdata->bh;
					if ($bh_status != 0)
					{
						$send_email = 1;
						if ($bh_status == 2 && $status == 2)
						{
							// approve mail
							$status = 'approved';
						}
						else
						{
							// decline mail
							$status = 'decline';
						}
						// send mail to user
						//echo 'send mail';
					}
				
					// mail to participant
					$emailSubject =  JText::_('COM_HIERARCHY_RESCHEDULES_USER_STATUS_EMAIL_SUBJECT');
					$rowBody =  JText::_('COM_HIERARCHY_RESCHEDULES_USER_BOTH_SAME_APPROVE');

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
					$replaceArr->status_bh = JText::_('COM_HIERARCHY_RESCHEDULES_WORKFLOW_APPROVED');
					$replaceArr->reason_bh = '';
					$replaceArr->status_hr = JText::_('COM_HIERARCHY_RESCHEDULES_WORKFLOW_APPROVED');
					$replaceArr->reason_hr = '';
					
					$cc[] = $params->get('reschedule_manager_email');
					$cc[] = $manager_data->email;
					
					$emailBody = HierarchyFrontendHelper::EmailBody($replaceArr, $rowBody);

					$email = HierarchyFrontendHelper::RescheduleEmail($mailfrom, $fromname, $user_data->email, $emailSubject, $emailBody, $cc);	
				}
				else
				{
					
					?>
					<form method="post" name="adminForm" id="sameemail">
					  <fieldset>
						<label><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_DECLINE_REASON').'*';?></label>
						<textarea cols="100" rows="4" name="reason" id="reason" style="margin-bottom: 10px;"></textarea>
						<input type="button" class="btn btn-primary submit center" id="submit" value="Submit" onclick="setDeclineReason('<?php echo $reschedule_id;?>','<?php echo $status;?>','<?php echo $ticketidcoded;?>','<?php echo $sameuser;?>');"/>
					  </fieldset>
					</form>
					
					<?php
				}

				
				if ($status == 'approved')
				{
					// approve
					echo JText::_('COM_HIERARCHY_RESCHEDULES_THANKS_CONFIRMATION_APPROVE');
				}
				else
				{
					echo '<span id="sameemail_save" style="display:none;">'.JText::_('COM_HIERARCHY_RESCHEDULES_THANKS_CONFIRMATION_DECLINE').'</span>';
					echo '<span id="sameemail_error" style="display:none;">'.JText::_('COM_HIERARCHY_RESCHEDULES_THANKS_CONFIRMATION_DECLINE_ERROR').'</span>';
				}
		}
		else
		{
			echo JText::_('COM_HIERARCHY_RESCHEDULES_ALREADY_THANKS_CONFIRMATION');
		}
	}
	else
	{
		if ($user == 'bh')
		{
			// replay form bh
			if ($ticketData->bh == 0)
			{
				if ($status == 1)
				{
						?>
					<form method="post" name="adminForm" id="sameemail">
					  <fieldset>
						<label><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_DECLINE_REASON').'*';?></label>
						<textarea cols="100" rows="4" name="reason" id="reason" style="margin-bottom: 10px;"></textarea>
						<input type="button" class="btn btn-primary submit center" id="submit" value="Submit" onclick="setDeclineReason('<?php echo $reschedule_id;?>','<?php echo $status;?>','<?php echo $ticketidcoded;?>','<?php echo $sameuser;?>');"/>
					  </fieldset>
					</form>
					<?php
				}
				else
				{
					// approve 2
					//~ $insertObj             = new stdClass;
					//~ $insertObj->id  = $reschedule_id;
					//~ $insertObj->bh       = 2;
					//~ $insertObj->modified_on = JHtml::date($input = 'now', 'Y-m-d h:i:s', false);
					//~ $db->updateObject('#__hierarchy_reschedule', $insertObj, 'id');
					
					ConfirmationHelper::updateRescheduleData($reschedule_id, 
					'', $bh = 2);
				
					// check hr already update status or not
					$hr_status = $ticketData->hr;
					if ($hr_status != 0)
					{
						$send_email = 1;
						// alredy update status by hr
						if ($hr_status == 2 && $status == 2)
						{
							// approve mail
							$status = 'approved';
						}
						else
						{
							// decline mail
							$status = 'decline';
						}
						
					}
					
					if ($status == 2)
					{
						$emailSubject =  JText::_('COM_HIERARCHY_RESCHEDULES_USER_STATUS_EMAIL_SUBJECT');

						$rowBody = 
						JText::_('COM_HIERARCHY_RESCHEDULES_USER_BH_APPROVE_HR_NOT');

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
						$replaceArr->status_bh = 'Approved';
						$replaceArr->reason_bh = '';
						$replaceArr->status_hr = 'Not Responded';
						$replaceArr->reason_hr = '';

						$emailBody = HierarchyFrontendHelper::EmailBody($replaceArr, $rowBody);
						
						$cc[] = $params->get('reschedule_manager_email');
						$cc[] = $manager_data->email;
					
						//print_r($replaceArr); echo $emailBody;die;
						$email = HierarchyFrontendHelper::RescheduleEmail($mailfrom, $fromname, $user_data->email, $emailSubject, $emailBody, $cc); 
						echo JText::_('COM_HIERARCHY_RESCHEDULES_THANKS_CONFIRMATION_APPROVE');
					}
				}
				if ($status == 'approved')
				{	
					$emailSubject =  JText::_('COM_HIERARCHY_RESCHEDULES_USER_STATUS_EMAIL_SUBJECT');

					$rowBody = 
					JText::_('COM_HIERARCHY_RESCHEDULES_USER_BH_APPROVE_HR_APPROVE');

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
					$replaceArr->status_bh = 'Approved';
					$replaceArr->reason_bh = '';
					$replaceArr->status_hr = 'Approved';
					$replaceArr->reason_hr = '';
					
					$cc[] = $params->get('reschedule_manager_email');
					$cc[] = $manager_data->email;

					$emailBody = HierarchyFrontendHelper::EmailBody($replaceArr, $rowBody);
					//print_r($replaceArr); echo $emailBody;die;
					$email = HierarchyFrontendHelper::RescheduleEmail($mailfrom, $fromname, $user_data->email, $emailSubject, $emailBody, $cc); 
					echo JText::_('COM_HIERARCHY_RESCHEDULES_THANKS_CONFIRMATION_APPROVE');
				}
				if ($status == 'decline')
				{	
					$emailSubject =  JText::_('COM_HIERARCHY_RESCHEDULES_USER_STATUS_EMAIL_SUBJECT');

					$rowBody = 
					JText::_('COM_HIERARCHY_RESCHEDULES_USER_BH_APPROVE_HR_REJECT');

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
					$replaceArr->status_bh = 'Approved';
					$replaceArr->reason_bh = '';
					$replaceArr->status_hr = 'Declined';
					$replaceArr->reason_hr = $ticketData->decline_reason_hr;
					
					$cc[] = $params->get('reschedule_manager_email');
					$cc[] = $manager_data->email;

					$emailBody = HierarchyFrontendHelper::EmailBody($replaceArr, $rowBody);
					//print_r($replaceArr); echo $emailBody;die;
					$email = HierarchyFrontendHelper::RescheduleEmail($mailfrom, $fromname, $user_data->email, $emailSubject, $emailBody, $cc); 
					echo JText::_('COM_HIERARCHY_RESCHEDULES_THANKS_CONFIRMATION_APPROVE');
				}
				else
				{
					echo '<span id="sameemail_save" style="display:none;">'.JText::_('COM_HIERARCHY_RESCHEDULES_THANKS_CONFIRMATION_DECLINE').'</span>';
					echo '<span id="sameemail_error" style="display:none;">'.JText::_('COM_HIERARCHY_RESCHEDULES_THANKS_CONFIRMATION_DECLINE_ERROR').'</span>';
				}
			}
			else
			{
				echo JText::_('COM_HIERARCHY_RESCHEDULES_ALREADY_THANKS_CONFIRMATION');
			}
		}
		else
		{
			// replay form hr

			if ($ticketData->hr == 0)
			{
				if ($status == 1)
				{
					?>
					<form method="post" name="adminForm" id="sameemail">
					  <fieldset>
						<label><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_DECLINE_REASON').'*';?></label>
						<textarea cols="100" rows="4" name="reason" id="reason" style="margin-bottom: 10px;"></textarea>
						<input type="button" class="btn btn-primary submit center" 
						id="submit" value="Submit" onclick="setDeclineReason('<?php echo 
						$reschedule_id;?>','<?php echo $status;?>','<?php echo $ticketidcoded;?>','<?php echo $sameuser;?>');"/>
					  </fieldset>
					</form>
					
					<?php
				}
				else
				{
					
					// approve 2
					//~ $insertObj             = new stdClass;
					//~ $insertObj->id  = $reschedule_id;
					//~ $insertObj->hr       = 2;
					//~ $insertObj->modified_on = JHtml::date($input = 'now', 'Y-m-d h:i:s', false);
					//~ $db->updateObject('#__hierarchy_reschedule', $insertObj, 'id');
					
					ConfirmationHelper::updateRescheduleData($reschedule_id, 
					$hr = 2, '');
					// check hr already update status or not
					$bh_status = $ticketData->bh;
					if ($bh_status != 0)
					{
						$send_email = 1;
						if ($bh_status == 2 && $status == 2)
						{
							// approve mail
							$status = 'approved';
						}
						else
						{
							// decline mail
							$status = 'decline';
						}
						// send mail to user
						//echo 'send mail';
					}
				}
				
				//echo $status;die;
				if ($status == 2)
				{
					$emailSubject =  JText::_('COM_HIERARCHY_RESCHEDULES_USER_STATUS_EMAIL_SUBJECT');

					$rowBody = 
					JText::_('COM_HIERARCHY_RESCHEDULES_USER_BH_NOT_HR_APPROVE');

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
					$replaceArr->status_hr = 'Approved';
					$replaceArr->reason_hr = '';
					
					$cc[] = $params->get('reschedule_manager_email');
					$cc[] = $manager_data->email;

					$emailBody = HierarchyFrontendHelper::EmailBody($replaceArr, $rowBody);

					$email = HierarchyFrontendHelper::RescheduleEmail($mailfrom, $fromname, $user_data->email, $emailSubject, $emailBody, $cc);
					echo JText::_('COM_HIERARCHY_RESCHEDULES_THANKS_CONFIRMATION_APPROVE');
				}

				if ($status == 'decline')
				{
					$emailSubject =  JText::_('COM_HIERARCHY_RESCHEDULES_USER_STATUS_EMAIL_SUBJECT');

					$rowBody = 
					JText::_('COM_HIERARCHY_RESCHEDULES_USER_BH_DECLINE_HR_APPROVE');

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
					$replaceArr->status_hr = 'Approved';
					$replaceArr->reason_hr = '';
					
					$cc[] = $params->get('reschedule_manager_email');
					$cc[] = $manager_data->email;

					$emailBody = HierarchyFrontendHelper::EmailBody($replaceArr, $rowBody);

					$email = HierarchyFrontendHelper::RescheduleEmail($mailfrom, $fromname, $user_data->email, $emailSubject, $emailBody, $cc); 
					// approve
					echo JText::_('COM_HIERARCHY_RESCHEDULES_THANKS_CONFIRMATION_APPROVE');
				}
				if ($status == 'approved')
				{
					$emailSubject =  JText::_('COM_HIERARCHY_RESCHEDULES_USER_STATUS_EMAIL_SUBJECT');

					$rowBody = 
					JText::_('COM_HIERARCHY_RESCHEDULES_USER_BH_APPROVE_HR_APPROVE');

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
					$replaceArr->status_bh = 'Approved';
					$replaceArr->reason_bh = '';
					$replaceArr->status_hr = 'Approved';
					$replaceArr->reason_hr = '';
					
					$cc[] = $params->get('reschedule_manager_email');
					$cc[] = $manager_data->email;

					$emailBody = HierarchyFrontendHelper::EmailBody($replaceArr, $rowBody);

					$email = HierarchyFrontendHelper::RescheduleEmail($mailfrom, $fromname, $user_data->email, $emailSubject, $emailBody, $cc); 
					// approve
					echo JText::_('COM_HIERARCHY_RESCHEDULES_THANKS_CONFIRMATION_APPROVE');
				}
				else
				{
					echo '<span id="sameemail_save" style="display:none;">'.JText::_('COM_HIERARCHY_RESCHEDULES_THANKS_CONFIRMATION_DECLINE').'</span>';
					echo '<span id="sameemail_error" style="display:none;">'.JText::_('COM_HIERARCHY_RESCHEDULES_THANKS_CONFIRMATION_DECLINE_ERROR').'</span>';
				}
			}
			else
			{
				echo JText::_('COM_HIERARCHY_RESCHEDULES_ALREADY_THANKS_CONFIRMATION');
			}
		}
		// 3. Mail to manger for assign new course this is user
	}

}
if ($send_email == 1)
{
	
	$user_id = $ticketData->user_id;
	$user_data = JFactory::getUser($user_id);
	$manager = JFactory::getUser(HierarchyFrontendHelper::getUserManager($user_id));

	$emailSubject =  JText::_('COM_HIERARCHY_RESCHEDULES_CONFIRM_EMAIL_USER_SUBJECT');

	$url = JURI::root().'index.php?option=com_hierarchy&view=nominates&nomineeId='.$user_id;
	
	$rowBody_approve = JText::sprintf("COM_HIERARCHY_RESCHEDULES_USER_APPROVE_FINAL_EMAIL",$url);
	
	$rowBody_decline ='<p style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">Hi {user_name}</span></p>
	<p></p>
	<p style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">Your reschedule request with ID {reschedule_no} has been rejected.</span></p>
	<p style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><span>&nbsp;</span></p>
	<p style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">Kindly attend the programme as per the previous nomination done by your manager. For further clarification, mail us at </span><a href="mailto:khushboo.khosla@bajajfinserv.in"><span style="font-size: 12px; font-family: "Open Sans"; color: #1155cc; text-decoration: underline; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">khushboo.khosla@bajajfinserv.in</span></a><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">.</span></p>
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
	

	if ($status === 'approved')
	{
		
		$emailBody = HierarchyFrontendHelper::EmailBody($replaceArr, $rowBody_approve);
		// do cancel jticketing 
		if ($ticketid)
		{
			require_once JPATH_SITE . '/components/com_jticketing/helpers/event.php';
			$cancel = JteventHelper::cancelTicket($ticketid);
		}
		
		$emailSubject =  JText::_('COM_HIERARCHY_RESCHEDULES_CONFIRM_EMAIL_USER_SUBJECT_APPROVAL');
		
		$cc[] = $params->get('reschedule_manager_email');
		$cc[] = $params->get('reschedule_approva_cc_email');
		
		if (strpos($params->get('reschedule_approva_cc_email'),',') !== false)
		{
			$seprate_emails = explode(',',$params->get('reschedule_approva_cc_email'));

			foreach ($seprate_emails as $key=>$value)
			{
				$cc[] = $value;
			}
		}
		//$cc[] = $user_data->email;
		
		$email = HierarchyFrontendHelper::RescheduleEmail($mailfrom, $fromname, $manager->email, $emailSubject, $emailBody, $cc);
	}
	else
	{
			$cc[] = $params->get('reschedule_manager_email');
			$cc[] = $manager->email;
			
			$emailBody = HierarchyFrontendHelper::EmailBody($replaceArr, $rowBody_decline);
			$emailSubject =  JText::_('COM_HIERARCHY_RESCHEDULES_CONFIRM_EMAIL_USER_SUBJECT_REJECTION');
			
			$email = HierarchyFrontendHelper::RescheduleEmail($mailfrom, $fromname, $user_data->email, $emailSubject, $emailBody, $cc);
	}

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
			//~ //$cc[] = $admin_data->email;
		//~ }
	//~ }
	
	

}
?>

<?php
/*
 * @package     JTicketing
 * @subpackage  com_jticketing
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */



// No direct access
defined('_JEXEC') or die;

JLoader::import('event', JPATH_SITE . '/components/com_jticketing/helpers');
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Jtickeing list enrollment controller.
 *
 * @since  __DEPLOY_VERSION__
 */
class JticketingControllerEnrollment extends AdminController
{
	/**
	 * common function to drive enrollment
	 *
	 * @return string
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function save()
	{
		$app                  = Factory::getApplication();
		$exceptions           = array();
		$jtEventHelper        = new JteventHelper;
		$enrollModelObj       = new stdClass;
		$successCount         = 0;
		$failureCount         = 0;
		$alreadyEnrolledCount = 0;

		$userId     = $app->input->get('cid', '0', 'INT');
		$eventId    = $app->input->get('selected_events', '0', 'INT');
		$notify      = $app->input->get('notify_user_enroll', '', 'INT');

		// Enroll each user to every event
		if (!empty($userId))
		{
			// Enrollment Params
			$data = array();

			// Mandatory params
			$data['userId'] = $userId;
			$data['eventId'] = $eventId;

			// Not mandatory params
			$data['notify'] = $notify;

			if (!empty($data))
			{
				$enrollModelObj = $this->getModel('enrollment');
				$result = $enrollModelObj->save($data);

				if ($result)
				{
					if ((int) $result === 2)
					{
						++$alreadyEnrolledCount;
					}
					else
					{
						++$successCount;
					}
				}
				else
				{
					++$failureCount;
				}

				$eventData = $jtEventHelper->getEventData($eventId);
				$userObj   = JFactory::getUser($userId);
				$name      = $userObj->name;

				$title = $eventData->title;
				$error = $enrollModelObj->getError();

				if (!empty($error))
				{
					// Unsuccessful enrollments
					$exceptions[] = Text::sprintf('COM_JTICKETING_ENROLLMENT_ERROR_MESSAGES', $name, $title, $error);
					echo new JResponseJson(null);
					jexit();
				}
				else
				{
					// Successful enrollments
					$error = Text::_("COM_JTICKETING_ENROLLMENT_SUCCESS_MESSAGE_SINGULAR");
					$exceptions[] = Text::sprintf('COM_JTICKETING_ENROLLMENT_ERROR_MESSAGES', $name, $title, $error);
				}
			}
		}

		$failureLog = '';

		if (count($exceptions))
		{
			$failureLog = Text::_("COM_JTICKETING_FAILURE_LOG");
			$enrollmentTitleMessage = Text::sprintf('COM_JTICKETING_ENROLLMENT_MESSAGE', $successCount, $alreadyEnrolledCount, $failureCount, '');

			// Error handling & log writing
			$enrollModelObj->writeEnrollmentLog($exceptions, $enrollmentTitleMessage);
		}

		$taskLink = 'index.php?option=com_jticketing&view=enrollment&task=enrollment.jtEnrollmentDownloadLog';
		$file = '<b><a target="_Blank" href="' . $taskLink . '" >' . $failureLog . '</a></b>';
		$enrollmentErrorLogMsg = Text::sprintf('COM_JTICKETING_ENROLLMENT_MESSAGE', $successCount, $alreadyEnrolledCount, $failureCount, $file);

		echo new JResponseJson($exceptions);
		jexit();
	}
}

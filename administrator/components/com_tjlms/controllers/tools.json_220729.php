<?php
/**
 * @package     TJLms
 * @subpackage  com_shika
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Session\Session;

JLoader::import('components.com_tjlms.helpers.tracking', JPATH_SITE);

/**
 * Tools controller class.
 *
 * @since  1.3.25
 */
class TjlmsControllerTools extends FormController
{
	/**
	 * Method to calculate course progress
	 *
	 * @return  void
	 *
	 * @since   1.3.25
	 */
	public function calculateCourseProgress()
	{
		Session::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app = Factory::getApplication();
		$courseId = $app->input->get('courseId', 0, 'INT');
		$startLimit = $app->input->get('startLimit', 0, 'INT');
		$batchSize = COM_TJLMS_BATCH_SIZE_FOR_AJAX;

		if ($courseId && $batchSize)
		{
			BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjlms/models', 'TjlmsModel');
			$model = BaseDatabaseModel::getInstance('Manageenrollments', 'TjlmsModel', array('ignore_request' => true));
			$model->setState('filter.coursefilter', $courseId);
			$model->setState('list.start', $startLimit);
			$model->setState('list.limit', $batchSize);
			$enrolledUsers = $model->getItems();
			$flag = 0;
			$totalEnrolledUsers = $model->getTotal();
			$trackingHelper = new ComtjlmstrackingHelper;

			try
			{
				if ($startLimit <= $totalEnrolledUsers)
				{
					foreach ($enrolledUsers as $eUser)
					{
						$trackingHelper->addCourseTrackEntry($courseId, $eUser->user_id);
					}

					$flag = 1;
				}

				$result = new stdClass;
				$result->flag = $flag;
				$result->totalEnrolledUsers = $totalEnrolledUsers;
				$result->startLimit = $startLimit;
				echo new JsonResponse($result, Text::_('COM_TJLMS_CALCULATE_COURSE_PROGRESS_SUCCESSFUL'), false);
			}
			catch (Exception $e)
			{
				echo new JsonResponse(null, Text::_('COM_TJLMS_CALCULATE_COURSE_PROGRESS_FAILURE'), true);
			}
		}
		else
		{
			echo new JsonResponse(null, Text::_('COM_TJLMS_INVALID_REQUEST'), true);
		}

		$app->close();
	}
}

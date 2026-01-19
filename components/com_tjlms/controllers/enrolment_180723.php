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

jimport('joomla.application.component.controlleradmin');

/**
 * Tjmodules list controller class.
 *
 * @since  1.0.0
 */
class TjlmsControllerEnrolment extends JControllerAdmin
{
	/**
	 * construct for enrollment
	 *
	 * @param   ARRAY  $config  Array
	 *
	 * @since  1.0.0
	 */
	public function __construct($config = array())
	{
		$this->_db = JFactory::getDbo();

		// Include helper of tjlms
		$path = JPATH_SITE . '/components/com_tjlms/helpers/main.php';
		$this->comtjlmsHelper = '';

		if (JFile::exists($path))
		{
			if (!class_exists('comtjlmsHelper'))
			{
				JLoader::register('comtjlmsHelper', $path);
				JLoader::load('comtjlmsHelper');
			}

			$this->comtjlmsHelper = new comtjlmsHelper;
		}

		// Load jlike model to call api function for assigndetails and other
		$path = JPATH_SITE . '/components/com_jlike/models/recommendations.php';
		$this->JlikeModelRecommendations = "";

		if (JFile::exists($path))
		{
			if (!class_exists('JlikeModelRecommendations'))
			{
				JLoader::register('JlikeModelRecommendations', $path);
				JLoader::load('JlikeModelRecommendations');
			}

			$this->JlikeModelRecommendations = new JlikeModelRecommendations;
		}

		// Load jlike admin model content form to call api to get content id
		$path = JPATH_SITE . '/administrator/components/com_jlike/models/contentform.php';

		$this->JlikeModelContentForm = "";

		if (JFile::exists($path))
		{
			if (!class_exists('JlikeModelContentForm'))
			{
				JLoader::register('JlikeModelContentForm', $path);
				JLoader::load('JlikeModelContentForm');
			}

			$this->JlikeModelContentForm = new JlikeModelContentForm;
		}

		// Load jlike admin model content form to call api to get content id
		$path = JPATH_SITE . '/components/com_jlike/models/recommend.php';

		$this->JlikeModelRecommend = "";

		if (JFile::exists($path))
		{
			if (!class_exists('JlikeModelRecommend'))
			{
				JLoader::register('JlikeModelRecommend', $path);
				JLoader::load('JlikeModelRecommend');
			}

			$this->JlikeModelRecommend = new JlikeModelRecommend;
		}

		parent::__construct($config);
	}

	/**
	 * Assign User to particular course
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function assignUser()
	{
		$app            = JFactory::getApplication();
		$input          = JFactory::getApplication()->input;
		$post           = $input->post;

		$rUrl = $post->get('rUrl', '', 'STRING');
		$link = base64_decode($rUrl);
		$course_al = $input->get('course_al', '0', 'INT');
		$selectedcourse = $post->get('selectedcourse', '', 'ARRAY');
		$data           = array();

		if ($selectedcourse)
		{
			// Filter for selected courses
			$selectedcoursefilter = $app->getUserStateFromRequest('com_tjlms.enrolment.filter.selectedcourse', 'selectedcourse', '', 'ARRAY');
			$this->getModel()->setState('filter.selectedcourse', $selectedcoursefilter);

			$data['selectedcourse'] = $selectedcourse;
		}

		$data['start_date'] = $post->get('start_date', '', 'DATE');

		$data['due_date'] = '';

		if ($post->get('due_date', '', 'DATE'))
		{
			$data['due_date'] = $post->get('due_date', '', 'DATE');
		}

		$data['notify_user'] = $post->get('notify_user', '0', 'INT');
		$userIds      = $post->get('cid', '', 'ARRAY');
		$data['type'] = 'assign';

		// Load tjlms order model to call api createOrder() api
		JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_tjlms/models/orders.php');
		$ordersModel              = JModelLegacy::getInstance('Orders', 'TjlmsModel');
		$enrollmentmodel      = $this->getModel('Enrolment', 'TjlmsModel');
		$tjlmsCoursesHelper = new tjlmsCoursesHelper;
		$model          = $this->getModel('manageenrollments');

		// Loop through each user
		foreach ($selectedcourse as $course)
		{
			$nonEnrolledUsers = $enrollmentmodel->getNonEnrolledUsers($userIds, $course);
			$course_info        = $tjlmsCoursesHelper->getCourseColumn($course, array('type','created_by'));

			if ($nonEnrolledUsers)
			{
				foreach ($nonEnrolledUsers as $key => $cid)
				{
					$data['course_id'] = $course;
					$data['user_id'] = $cid;
					$res = $enrollmentmodel->userAssignment($data);
				}
			}
			else
			{
				$res = 1;
			}
		}

		if ($res)
		{
			// Add a message to the message queue
			$app->enqueueMessage(JText::_('COM_TJLMS_COURSE_ASSIGN_SUCCESS'), 'success');
		}
		else
		{
			// Add a message to the message queue
			$app->enqueueMessage(JText::_('COM_TJLMS_COURSE_ASSIGN_FALIED'), 'error');
		}

		$flink = $this->comtjlmsHelper->tjlmsRoute($link, false);
		$this->setRedirect($flink);
	}

	/**
	 * change Due date
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function batchAssign()
	{
		$model = $this->getModel('manageenrollments');
		$modelEnrollment = $this->getModel('enrolment');
		$app   = JFactory::getApplication();
		$input = JFactory::getApplication()->input;
		$post  = $input->post;
		$data  = array();

		// Get data passed by the post from the view
		$batch_assign_start_date = $post->get('batch_start_date', '', 'DATE');
		$batch_assign_end_date   = $post->get('batch_due_date', '', 'DATE');

		$data['start_date'] = $batch_assign_start_date;
		$data['due_date'] = $batch_assign_end_date;
		$enrollmentsIds = $post->get('cid', '', 'ARRAY');
		$data['notify_user'] = $post->get('notify_user_batch', '', 'INT');

		// Loop through data of each enrolment
		foreach ($enrollmentsIds as $key => $eid)
		{
			$enrollmentdetails = $model->getenrollmentdetails($eid);

			if ($enrollmentdetails)
			{
				$data['course_id'] = $enrollmentdetails->course_id;
				$data['user_id'] = $enrollmentdetails->user_id;

				$res = $modelEnrollment->userEnrollment($data);
			}
		}

		if ($res)
		{
			// Add a message to the message queue
			$app->enqueueMessage(JText::_('COM_TJLMS_ASSIGN_DUEDATE_CHANGE'), 'success');
		}
		else
		{
			// Add a message to the message queue
			$app->enqueueMessage(JText::_('COM_TJLMS_BATCH_UPDATED_SUCCESSFULLY'), 'error');
		}

		$flink = $this->comtjlmsHelper->tjlmsRoute('index.php?option=com_tjlms&view=manageenrollments', false);
		$this->setRedirect($flink);
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param   STRING  $name    model name
	 * @param   STRING  $prefix  model prefix
	 * @param   ARRAY   $config  Array
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function getModel($name = 'enrolment', $prefix = 'TjlmsModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	/**
	 * function to enroll user from backend new
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function enrolUser()
	{
		$app       = JFactory::getApplication();
		$input     = JFactory::getApplication()->input;
		$post      = $input->post;

		$cid       = $post->get('cid', array(), 'array');
		$selectedcourse = $post->get('selectedcourse', '', 'array');

		$selectedcoursefilter = $app->setUserState('com_tjlms.enrolment.filter.selectedcourse', $selectedcourse);

		$notifyUser = ($post->get('notify_user_enroll', '', 'INT')) ? $post->get('notify_user_enroll', '', 'INT') : 0;

		$this->userEnrollment($selectedcourse, $cid, $notifyUser);
	}

	/**
	 * Function for enrollment
	 *
	 * @param   ARRAY  $selectedcourse  course ids
	 * @param   ARRAY  $cid             user ids going to enroll
	 * @param   INT    $notifyUser      mailing parameter
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	private function userEnrollment($selectedcourse, $cid, $notifyUser)
	{
		$app       = JFactory::getApplication();
		$model      = $this->getModel('Enrolment', 'TjlmsModel');
		$input     = JFactory::getApplication()->input;
		$post      = $input->post;

		$rUrl = $post->get('rUrl', '', 'STRING');
		$link = base64_decode($rUrl);
		$msg = JText::_('COM_TJLMS_COURSE_ENROLL_SUCCESS');
		$loggedInUser = JFactory::getUser()->id;
		$type = 'success';

		if (!empty($selectedcourse))
		{
			$tjlmsCoursesHelper = new tjlmsCoursesHelper;
			JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_tjlms/models');

			// Enrollment from manage enrollment view.
			foreach ($selectedcourse as $key => $courseId)
			{
				$nonEnrolledUsers = $model->getNonEnrolledUsers($cid, $courseId);

				foreach ($nonEnrolledUsers as $userId)
				{
					$data = array();
					$data['user_id'] = $userId;
					$data['course_id'] = $courseId;
					$data['notify_user'] = $notifyUser;

					if (!$model->userEnrollment($data))
					{
						$msg = $model->getError();
						$type = 'error';
					}
				}
			}
		}
		else
		{
			$msg = JText::_('COM_TJLMS_NO_COURSE_SELECTED');
			$type = 'error';
		}

		$flink = $this->comtjlmsHelper->tjlmsRoute($link, false);

		$this->setRedirect($flink, $msg, $type);
	}

	/**
	 * common function to drive enrollment and assignment
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function enrollAssignWrapper()
	{
		$app       = JFactory::getApplication();
		$input     = JFactory::getApplication()->input;
		$post      = $input->post;

		$batch_assign_end_date   = $post->get('due_date', '', 'DATE');

		if ($batch_assign_end_date)
		{
			$this->assignUser();
		}
		else
		{
			$this->enrolUser();
		}
	}
}

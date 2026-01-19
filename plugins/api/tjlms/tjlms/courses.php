<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_trading
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * User Api.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_api
 *
 * @since       1.0
 */
class TjlmsApiResourceCourses extends ApiResource
{
	protected $items = array();

	/**
	 * Function post
	 *
	 * @return void
	 */
	public function post()
	{
		$this->plugin->setResponse("Use GET method");
		return;
	}

	/**
	 * Function get for users record.
	 *
	 * @return void
	 */
	public function get()
	{
		$result = new stdClass;
		$result->err_code = '';
		$result->err_message = '';
		$result->data = new stdClass;

		JLoader::import('components.com_tjlms.models.courses', JPATH_SITE);
		$coursesModel = JModelLegacy::getInstance('courses', 'TjlmsModel', array('ignore_request' => true));

		$input 	 		= JFactory::getApplication()->input;
		$filter			= JFilterInput::getInstance();
		$filtersArray 	= $input->get('filters', array(), 'ARRAY');

		if (isset($filtersArray['search']))
		{
			$search = $filter->clean($filtersArray['search'], 'STRING');
			$coursesModel->setState('com_tjlms.filter.filter_search', $search);
		}

		if (isset($filtersArray['category']))
		{
			$category = $filter->clean($filtersArray['category'], 'INT');
			$coursesModel->setState('filter.menu_category', $category);
		}

		if (isset($filtersArray['type']))
		{
			$type = $filter->clean($filtersArray['type'], 'INT');
			$coursesModel->setState('com_tjlms.filter.course_type', $type);
		}

		if (isset($filtersArray['author']))
		{
			$author = $filter->clean($filtersArray['author'], 'INT');
			$coursesModel->setState('com_tjlms.filter.filter_creator', $author);
		}

		$limit = $input->getInt('limit', 0);
		$coursesModel->setState('list.limit', $limit);
		$limitstart = $input->getInt('limitstart', 0);
		$coursesModel->setState('list.start', $limitstart);

		$this->items = $coursesModel->getItems();

		// Get the validation messages.
		$errors = $coursesModel->getErrors();

		if (!empty($errors))
		{
			$msg = array();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$msg[] = $errors[$i]->getMessage();
				}
				else
				{
					$msg[] = $errors[$i];
				}
			}

			$result->err_code		= 500;
			$result->err_message	= implode("\n", $msg);

			$this->plugin->setResponse($result);

			return;
		}

		// Process data for APIs
		$this->getApiItems();

		$result->data->result = $this->items;
		unset($this->items);
		$this->plugin->setResponse($result);

		return;
	}

	/**
	 * Method to process courses data for API.
	 *
	 * @return  null
	 *
	 * @since   1.0.0
	 */
	private function getApiItems()
	{
		if (!empty($this->items))
		{
			$params  = JComponentHelper::getParams('com_tjlms');
			$userCol = $params->get('show_user_or_username', 'name');
			$userCol = ($userCol == 'username') ? 'username' : 'name';

			JLoader::import('components.com_tjlms.helpers.courses', JPATH_SITE);
			JLoader::import('components.com_tjlms.helpers.main', JPATH_SITE);
			JLoader::import('components.com_tjlms.models.enrolment', JPATH_SITE);
			JLoader::import('components.com_tjlms.helpers.tracking', JPATH_SITE);

			$tjlmsCoursesHelper = new TjlmsCoursesHelper;
			$comtjlmsHelper 	= new comtjlmsHelper;
			$enrollmentModel 	= new TjlmsModelEnrolment;
			$trackingHelper		= new ComtjlmstrackingHelper;

			$userId 			= JFactory::getUser()->id;

			foreach ($this->items as $ind => &$objCopy)
			{
				// Course Metadata
				$obj = new stdClass;
				$obj->course_id				= $objCopy->id;
				$obj->course_title			= $objCopy->title;
				$obj->course_description	= $objCopy->description;
				$obj->course_state			= $objCopy->state;
				$obj->course_type			= $objCopy->type;
				$obj->course_cat_id			= $objCopy->cat_id;
				$obj->course_cat_title		= $tjlmsCoursesHelper->getCourseCat($objCopy, 'title');
				$obj->course_creator_id		= $objCopy->created_by;
				$obj->course_creator_name	= JFactory::getUser($objCopy->created_by)->$userCol;
				$obj->course_alias			= $objCopy->alias;

				// Course Image
				if ($objCopy->storage != 'invalid' && !empty($objCopy->image))
				{
					$obj->course_image = $tjlmsCoursesHelper->getCourseImage(array('image' => $objCopy->image, 'storage' => $objCopy->storage), 'S_');
				}

				// Like and dislike count
				$itemLikeDislike = $comtjlmsHelper->getItemJlikes($objCopy->id, 'com_tjlms.course');

				$obj->course_no_of_likes	= isset($itemLikeDislike['likes']) ? (int) $itemLikeDislike['likes'] : 0;
				$obj->course_no_of_dislikes	= isset($itemLikeDislike['dislikes']) ? (int) $itemLikeDislike['dislikes'] : 0;

				// Enrollment Detail
				$enrolled_count 			= count($comtjlmsHelper->getCourseEnrolledUsers($objCopy->id));
				$obj->enrolled_users_cnt 	= $comtjlmsHelper->custom_number_format($enrolled_count);

				// Paid Plan
				if ($objCopy->type)
				{
					$obj->course_subscription_plans = $tjlmsCoursesHelper->getCourseSubplans($objCopy->id);
					$this->processSubscription($obj->course_subscription_plans);
				}

				$obj->enrolled 				= $enrollmentModel->checkUserEnrollment($objCopy->id, $userId) ? 1 : 0;

				// Course Track
				if ($obj->enrolled)
				{
					$courseTrack = $trackingHelper->getCourseTrackEntry($objCopy->id, $userId);

					$user_progress = new stdClass;
					$user_progress->no_of_lesson 			= $courseTrack['totalLessons'];
					$user_progress->no_of_completed_lessons = $courseTrack['completedLessons'];
					$user_progress->completion_percentage	= $courseTrack['completionPercent'];
					$obj->user_progress = $user_progress;
				}

				// Assign the new Object
				$objCopy = $obj;

				$obj = null;
			}
		}
	}

	/**
	 * Method to process courses data for API.
	 *
	 * @param   MIX  &$plans  Plans data
	 *
	 * @return  null
	 *
	 * @since   1.0.0
	 */
	private function processSubscription(&$plans)
	{
		if (!empty($plans))
		{
			foreach ($plans as &$plan)
			{
				// Plan Metadata
				$obj = new stdClass;
				$obj->plan_id 			= $plan->id;
				$obj->plan_time_measure = $plan->time_measure;
				$obj->plan_price 		= $plan->price;
				$obj->plan_duration 	= $plan->duration;

				// Assign the new Object
				$plan = $obj;

				$obj = null;
			}
		}
	}
}

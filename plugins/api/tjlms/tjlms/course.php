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
class TjlmsApiResourceCourse extends ApiResource
{
	protected $item = array();

	protected $userId = 0;

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
		$this->userId = JFactory::getUser()->id;

		JLoader::import('components.com_tjlms.models.course', JPATH_SITE);
		$courseModel = JModelLegacy::getInstance('course', 'TjlmsModel', array('ignore_request' => true));

		$input 	 	= JFactory::getApplication()->input;
		$course_id 	= $input->get('id', 0, 'INT');

		if (empty($course_id))
		{
			// Not given username or user_id to edit the details of user
			$eobj->code = '400';
			$eobj->message = JText::_('PLG_API_TJLMS_REQUIRED_COURSE_DATA_EMPTY_MESSAGE');
			$this->plugin->setResponse($eobj);

			return;
		}
		else
		{
			$this->item = $courseModel->getItem($course_id);

			// Get the validation messages.
			$errors = $courseModel->getErrors();

			if (!empty($errors))
			{
				$code = 500;
				$msg  = array();

				// Push up to three validation messages out to the user.
				for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
				{
					if ($errors[$i] instanceof Exception)
					{
						$code  = $errors[$i]->getCode();
						$msg[] = $errors[$i]->getMessage();
					}
					else
					{
						$msg[] = $errors[$i];
					}
				}

				$result->err_code		= $code;
				$result->err_message	= implode("\n", $msg);

				$this->plugin->setResponse($result);

				return;
			}
			else
			{
				// Process data for APIs
				$this->getApiItem();

				$result->data->result = $this->item;
				unset($this->item);
				$this->plugin->setResponse($result);

				return;
			}
		}
	}

	/**
	 * Method to process courses data for API.
	 *
	 * @return  null
	 *
	 * @since   1.0.0
	 */
	private function getApiItem()
	{
		if (!empty($this->item))
		{
			// Filter course tags data
			if (!empty($this->item->course_tags))
			{
				foreach ($this->item->course_tags as &$tag)
				{
					$obj = new stdClass;
					$obj->title = $tag->title;
					$obj->alias = $tag->alias;

					$tag = $obj;
					$obj = null;
				}
			}

			// Filter Enrolled user data
			if (!empty($this->item->enrolled_users))
			{
				foreach ($this->item->enrolled_users as &$enrolled_users)
				{
					$obj = new stdClass;
					$obj->user_id 		= $enrolled_users->user_id;
					$obj->enrolled_on 	= $enrolled_users->enrolled_on_time;
					$obj->user_name 	= $enrolled_users->username;
					$obj->name 			= $enrolled_users->name;
					$obj->avatar 		= $enrolled_users->avatar;
					$obj->profileurl 	= $enrolled_users->profileurl;

					$enrolled_users = $obj;
					$obj = null;
				}
			}

			// Filter course subscription plans
			if (!empty($this->item->course_subscription_plans))
			{
				$this->processSubscription($this->item->course_subscription_plans);
			}

			// Filter course like & dislike
			$this->item->course_no_of_likes	= $this->item->course_no_of_dislikes = 0;

			if (!empty($this->item->likesData))
			{
				$this->item->course_no_of_likes		= (int) $this->item->likesData['likecount'];
				$this->item->course_no_of_dislikes 	= (int) $this->item->likesData['dislikecount'];
				$this->item->course_likes 			= $this->item->likesData['pwltcb'];
				unset($this->item->likesData);
			}

			// Filter course progress data
			if (!empty($this->item->courseTrack))
			{
				$user_progress = new stdClass;
				$user_progress->no_of_lesson 			= $this->item->courseTrack['totalLessons'];
				$user_progress->no_of_completed_lessons = $this->item->courseTrack['completedLessons'];
				$user_progress->completion_percentage	= $this->item->courseTrack['completionPercent'];
				$this->item->user_progress = $user_progress;
				unset($this->item->courseTrack);
			}

			// Process modules and lesson data
			$this->processModuleLessons();

			// Process course at last. Used in Modules lesson
			$this->processCourseData();

			return;
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

	/**
	 * Method to process Modules data for API.
	 *
	 * @return  null
	 *
	 * @since   1.0.0
	 */
	private function processModuleLessons()
	{
		if (!empty($this->item->course_info) && !empty($this->item->course_toc))
		{
			JLoader::import('components.com_tjlms.helpers.lesson', JPATH_SITE);
			$lessonHelper = new TjlmsLessonHelper;
			$this->item->modules = array();
			$i = 0;

			foreach ($this->item->course_toc['module_data'] as $module)
			{
				$newModule = array();
				$newModule['module_id'] 	= $module->id;
				$newModule['module_title'] 	= $module->name;

				if (!empty($module->lessons))
				{
					$newModule['lessons'] 	= array();

					foreach ($module->lessons as $lesson)
					{
						$newLesson = new stdClass;

						// Basic details
						$newLesson->lesson_id 			= $lesson->id;
						$newLesson->lesson_title 		= $lesson->name;
						$newLesson->lesson_description 	= $lesson->description;
						$newLesson->lesson_state 		= $lesson->state;
						$newLesson->lesson_start_date	= $lesson->start_date;
						$newLesson->lesson_end_date		= $lesson->end_date;
						$newLesson->lesson_format 		= $lesson->format;

						if (!empty($lesson->sub_format))
						{
							$newLesson->lesson_sub_format 	= $lesson->sub_format;
						}

						$newLesson->lesson_course_id 	= $lesson->course_id;
						$newLesson->lesson_alias 		= $lesson->alias;

						// Lesson Image
						if ($lesson->storage != 'invalid' && !empty($lesson->image))
						{
							$newLesson->lesson_image = $lessonHelper->getLessonImage(array('image' => $lesson->image, 'storage' => $lesson->storage), 'S_');
						}

						// Tracking detail
						$newLesson->lesson_tracking_details = new stdClass;
						$newLesson->lesson_tracking_details->no_of_attempts_allowed = $lesson->no_of_attempts;
						$newLesson->lesson_tracking_details->prerequisites = $lesson->eligibilty_criteria;

						// User tracking details
						$newLesson->lesson_user_tracking_details = new stdClass;
						$newLesson->lesson_user_tracking_details->no_of_attempts_done 			= $lesson->attemptsdonebyuser;
						$newLesson->lesson_user_tracking_details->consider_for_course_passing 	= $lesson->consider_marks;
						$newLesson->lesson_user_tracking_details->last_attempt_status 			= $lesson->completed_last_attempt;
						$newLesson->lesson_user_tracking_details->attempts_grading_status 		= $lesson->status;
						$newLesson->lesson_user_tracking_details->score 						= $lesson->score;

						if (!empty($lesson->statusdetails))
						{
							$newLesson->lesson_user_tracking_details	= $lesson->statusdetails;
						}

						$can_access = $lessonHelper->usercanAccess($lesson, $this->item->course_info, $this->userId);

						if (!empty($newLesson->can_access))
						{
							$newLesson->can_access->success 	= $newLesson->can_access->access;
							$newLesson->can_access->error_msg 	= $newLesson->can_access->msg;
						}

						$newModule['lessons'][] = $newLesson;

						unset($newLesson);
					}
				}

				$this->item->modules[$i] = $newModule;
				$i++;
			}

			unset($this->item->course_toc);
		}
	}

	/**
	 * Method to process course data for API.
	 *
	 * @return  null
	 *
	 * @since   1.0.0
	 */
	private function processCourseData()
	{
		if (!empty($this->item->course_info))
		{
			$mapping = array(
				'id' => 'course_id', 'title' => 'course_title', 'description' => 'course_description',
				'state' => 'course_state', 'type' => 'course_type', 'created_by' => 'course_creator_id',
				'alias' => 'course_alias', 'course_access_level' => 'access',
			);

			foreach ($this->item->course_info as $key => $val)
			{
				// If key starts with course_ we can just copy
				if (strpos($key, 'course_') === 0)
				{
					$this->item->$key = $val;
				}
				elseif (array_key_exists($key, $mapping))
				{
					$this->item->$mapping[$key] = $val;
				}
			}
		}

		unset($this->item->course_info);
	}
}

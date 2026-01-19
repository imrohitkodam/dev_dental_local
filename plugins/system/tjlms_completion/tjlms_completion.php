<?php
/**
 * @package    Joomla.Plugin
 * @subpackage System.tjlms_completion
 * @author     TechJoomla <extensions@techjoomla.com>
 * @copyright  Copyright (C) 2025 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * TJLMS Completion Plugin
 */
class PlgSystemTjlms_Completion extends CMSPlugin
{
	/**
	 * Application object
	 *
	 * @var    \Joomla\CMS\Application\CMSApplication
	 * @since  1.0.0
	 */
	protected $app;

	/**
	 * Database object
	 *
	 * @var    \Joomla\Database\DatabaseDriver
	 * @since  1.0.0
	 */
	protected $db;

	/**
	 * Event triggered after a lesson attempt ends
	 *
	 * @param   int     $lesson_id  The lesson ID
	 * @param   int     $attempt    The attempt number
	 * @param   int     $user_id    The user ID
	 * @param   string  $format     The lesson format
	 *
	 * @return  void
	 */
	public function onAfterLessonAttemptEnd($lesson_id, $attempt, $user_id, $format)
	{
		// Load TJLMS Lesson Helper
		if (!class_exists('TjlmsLessonHelper'))
		{
			JLoader::import('components.com_tjlms.helpers.lesson', JPATH_SITE);
		}

		if (!class_exists('TjlmsLessonHelper'))
		{
			return;
		}

		$helper = new TjlmsLessonHelper;
		
		// Get Lesson data to check consider_marks
		$lesson = $helper->getLessonColumn($lesson_id, 'consider_marks');

		// core tracking.php handles trigger if consider_marks == 1
		// We want to trigger it for other cases (e.g. consider_marks == 0)
		if (!empty($lesson) && $lesson->consider_marks != 1)
		{
			// Check if the attempt was actually completed/passed
			// We need to fetch the track record because onAfterLessonAttemptEnd arguments don't include status
			
			$db = Factory::getDbo();
			$query = $db->getQuery(true)
				->select($db->quoteName('lesson_status'))
				->from($db->quoteName('#__tjlms_lesson_track'))
				->where($db->quoteName('lesson_id') . ' = ' . (int) $lesson_id)
				->where($db->quoteName('user_id') . ' = ' . (int) $user_id)
				->where($db->quoteName('attempt') . ' = ' . (int) $attempt);
			
			$db->setQuery($query);
			$status = $db->loadResult();
			Log::add('TJLMS Completion Plugin: Status: ' . $status, Log::INFO, 'tjlms_completion');

			if ($status == 'completed' || $status == 'passed')
			{
				Log::add('TJLMS Completion Plugin: Triggering onAfterLessonCompletion for Lesson ID ' . $lesson_id . ', User ID ' . $user_id, Log::INFO, 'tjlms_completion');
				// $this->app->triggerEvent('onAfterLessonCompletion', array($lesson_id, $attempt, $user_id));
				
				// Certificate Generation Logic (bypassing consider_marks check in tracking.php)
				try
				{
					// Load TjLms class
					if (!class_exists('TjLms'))
					{
						if (file_exists(JPATH_ADMINISTRATOR . '/components/com_tjlms/includes/tjlms.php'))
						{
							require_once JPATH_ADMINISTRATOR . '/components/com_tjlms/includes/tjlms.php';
						}
					}
					Log::add('TJLMS Completion Plugin: 1', Log::INFO, 'tjlms_completion');
					if (class_exists('TjLms'))
					{
						Log::add('TJLMS Completion Plugin: 2', Log::INFO, 'tjlms_completion');
						$courseObj = $helper->getLessonColumn($lesson_id, 'course_id');
						if (!empty($courseObj) && !empty($courseObj->course_id))
						{
							Log::add('TJLMS Completion Plugin: 3', Log::INFO, 'tjlms_completion');
							$courseId = $courseObj->course_id;
							$courseData = TjLms::course($courseId);
							
							if ($courseData && $courseData->certificate_id)
							{
								Log::add('TJLMS Completion Plugin: 4', Log::INFO, 'tjlms_completion');
								// Load Helpers and Model
								JLoader::import('components.com_tjlms.helpers.courses', JPATH_SITE);
								JLoader::import('components.com_tjlms.models.course', JPATH_SITE);
								
								$tjlmsModelcourse = BaseDatabaseModel::getInstance('Course', 'TjlmsModel', array('ignore_request' => true));
								
								if (class_exists('TjlmsCoursesHelper') && $tjlmsModelcourse)
								{
									Log::add('TJLMS Completion Plugin: 5', Log::INFO, 'tjlms_completion');
									// Term 1: Complete All Lessons
									if ($courseData->certificate_term == 1)
									{
										Log::add('TJLMS Completion Plugin: 6', Log::INFO, 'tjlms_completion');
										$tjlmsCoursesHelper = new TjlmsCoursesHelper;
										$courseProgress = $tjlmsCoursesHelper->getCourseProgress($courseId, $user_id);
										
										if ($courseProgress['status'] == 'C')
										{
											Log::add('TJLMS Completion Plugin: 7', Log::INFO, 'tjlms_completion');
											Log::add('TJLMS Completion Plugin: Generating Certificate (Term 1) for Course ' . $courseId, Log::INFO, 'tjlms_completion');
											$tjlmsModelcourse->addCertEntry($courseId, $user_id);
										}
									}
									
									// Term 2: Pass All Lessons
									if ($courseData->certificate_term == 2)
									{
										Log::add('TJLMS Completion Plugin: 8', Log::INFO, 'tjlms_completion');
										$courseTrackObj = TjLms::coursetrack($user_id, $courseId);
										if ($courseTrackObj && method_exists($courseTrackObj, 'checkPassableLessonsPassed'))
										{
											Log::add('TJLMS Completion Plugin: 9', Log::INFO, 'tjlms_completion');
											if ($courseTrackObj->checkPassableLessonsPassed())
											{
												Log::add('TJLMS Completion Plugin: 10', Log::INFO, 'tjlms_completion');
												Log::add('TJLMS Completion Plugin: Generating Certificate (Term 2) for Course ' . $courseId, Log::INFO, 'tjlms_completion');
												$tjlmsModelcourse->addCertEntry($courseId, $user_id);
											}
										}
									}
								}
							}
						}
					}
				}
				catch (Exception $e)
				{
					Log::add('TJLMS Completion Plugin Error: ' . $e->getMessage(), Log::ERROR, 'tjlms_completion');
				}
			}
		}
	}
}

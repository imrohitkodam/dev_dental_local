<?php
/**
 * @package    LMS_Shika
 * @copyright  Copyright (C) 2009-2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license    GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link       http://www.techjoomla.com
 */

// No direct access.
defined('_JEXEC') or die;
jimport('joomla.access.access');
jimport('joomla.application.component.model');
jimport('techjoomla.common');

/**
 * Methods supporting course details view.
 *
 * @since  1.0.0
 */
class TjlmsModelcourse extends JModelItem
{
	/**
	 * constructor function
	 *
	 * @since  1.0
	 */
	public function __construct()
	{
		$this->tjlmsdbhelperObj = new tjlmsdbhelper;
		$this->comtjlmstrackingHelper = new comtjlmstrackingHelper;

		$this->comtjlmsHelper = new comtjlmsHelper;
		$this->tjlmsCoursesHelper = new tjlmsCoursesHelper;

		$path = JPATH_COMPONENT . '/helpers/' . 'lesson.php';

		if (!class_exists('TjlmsLessonHelper'))
		{
			// Require_once $path;
			JLoader::register('TjlmsLessonHelper', $path);
			JLoader::load('TjlmsLessonHelper');
		}

		$this->tjlmsLessonHelper = new TjlmsLessonHelper;

		$path = JPATH_SITE . '/components/com_tjlms/libraries/scorm/' . 'scormhelper.php';

		if (!class_exists('comtjlmsScormHelper'))
		{
			// Require_once $path;
			JLoader::register('comtjlmsScormHelper', $path);
			JLoader::load('comtjlmsScormHelper');
		}

		$this->comtjlmsScormHelper = new ComtjlmsScormHelper;
		$this->techjoomlacommon = new TechjoomlaCommon;

		parent::__construct();
	}

	/**
	 * Function to get course details from courses table
	 *
	 * @param   INT  $course_id  id of course
	 *
	 * @return  object course info
	 *
	 * @since  1.0
	 */
	public function getcourseinfo($course_id)
	{
		// Get course info
		$courseInfo = $this->tjlmsCoursesHelper->getcourseInfo($course_id);

		if (empty($courseInfo))
		{
			// Course does not exisit of bad url
			return array();
		}
		else
		{
			// If cat is unpublished ir trashed
			if (isset($courseInfo->published) && $courseInfo->published != 1)
			{
				$courseInfo->state = 0;
			}

			$courseInfo->authorized = 0;

			// Get User access level
			$user_access = JFactory::getUser()->getAuthorisedViewLevels();

			// Check if user is authorised to take the course
			if (in_array($courseInfo->access, $user_access) && in_array($courseInfo->catAccess, $user_access))
			{
				$courseInfo->authorized = 1;
			}

			// Get name of the creator from users table
			$courseInfo->creator_name = $courseInfo->creator_username = JText::_('COM_TJLMS_BLOCKED_USER');

			if (JUser::getTable()->load($courseInfo->created_by))
			{
				$userInfo = JFactory::getUser($courseInfo->created_by);

				if ($userInfo->block == 0)
				{
					$courseInfo->creator_name = $userInfo->name;
					$courseInfo->creator_username = $userInfo->username;
				}
			}

			// Get image accorfing to storage
			$courseInfo->image = $this->tjlmsCoursesHelper->getCourseImage((array) $courseInfo, 'S_');

			// Set UTC date to orig_start_date
			$courseInfo->orig_start_date = $courseInfo->start_date;
			$lmsparams = JComponentHelper::getParams('com_tjlms');
			$date_format_show = $lmsparams->get('date_format_show', 'Y-m-d H:i:s');
			$courseInfo->start_date = $this->techjoomlacommon->getDateInLocal($courseInfo->start_date, 0, $date_format_show);
		}

		return $courseInfo;
	}

	/**
	 * Method to fetch subs plans assigned for course
	 *
	 * @param   int  $courseId  id of course
	 *
	 * @return  object course Sub plan
	 *
	 * @since  1.0
	 */
	public function getsubs_plan($courseId)
	{
		return $this->tjlmsCoursesHelper->getCourseSubplans($courseId);
	}

	/**
	 * Method to fetch subs plans assigned for course
	 *
	 * @param   int  $courseId  id of course
	 *
	 * @return  object course Sub plan
	 *
	 * @since  1.0
	 */
	public function getCourseRemainingSubPlan($courseId)
	{
		$remainDaysOfPlan = $this->tjlmsCoursesHelper->getCourseRemainingDays($courseId);

		if (isset($remainDaysOfPlan))
		{
			$endDate = new DateTime($remainDaysOfPlan->end_time);
			$return = array();
			$return['remain_sub_plan'] = date_format($endDate, "d M Y");
			$return['unlimited_plan'] = $remainDaysOfPlan->unlimited_plan;

			return $return;
		}
	}

	/**
	 * Get all the modules/sections and their lessons of a course
	 * Called from a lms_course_blocks to get lesson_count and passed_lessons count
	 *
	 * @param   int  $course_id               id of course
	 * @param   int  $getlessonStatusdetails  set 1 if we want to get the status details,
	 * like lastaccessedon , number of attmempts done by user
	 * @param   int  $oluser_id               user fr whom progress has to be get
	 *
	 * @return  object  $module_data
	 *
	 * @since  1.0.0
	 */
	public function getCourseTocdetails($course_id, $getlessonStatusdetails = 1, $oluser_id = '')
	{
		$lmsparams = JComponentHelper::getParams('com_tjlms');
		$date_format_show = $lmsparams->get('date_format_show', 'Y-m-d H:i:s');

		$db = JFactory::getDBO();
		$input = JFactory::getApplication()->input;

		if (empty($oluser_id))
		{
			$oluser_id = JFactory::getUser()->id;
		}

		$lessonHelper = $this->tjlmsLessonHelper;

		// Get data if course if present
		if ($course_id > 0)
		{
			try
			{
				$query = $db->getQuery(true);
				$query->select('id,name');
				$query->from($db->quoteName('#__tjlms_modules'));
				$query->where($db->quoteName('course_id') . ' = ' . $db->quote((int) $course_id));
				$query->where($db->quoteName('state') . ' = 1');
				$query->order($db->quoteName('ordering') . ' ASC');
				$db->setQuery($query);
				$modules = $db->loadobjectlist('id');

				/**
				 * lesson count = total number of lessons present in the course and which are published
				 * lessonConsiderd_forPassing = consider for passing is set yes
				 *  passed_lessons = number of lessons user has passed in, according to attempts grading
				 * */

				$lessonCount = $lessonsConsiderdforPassing = $passedLessons = 0;

				// Get published and formattted lessons belonging each module
				foreach ($modules as $mod_data)
				{
					// Get all published and format uploaded lessons
					$query = $db->getQuery(true);
					$query->select('*');
					$query->from($db->quoteName('#__tjlms_lessons'));
					$query->where($db->quoteName('mod_id') . ' = ' . $db->quote((int) $mod_data->id));
					$query->where($db->quoteName('state') . ' = 1');
					$query->where($db->quoteName('format') . "<> ''");
					$query->where($db->quoteName('media_id') . " >  0");
					$query->where($db->quoteName('media_id') . " <>  ''");
					$query->order($db->quoteName('ordering') . ' ASC');

					$db->setQuery($query);
					$module_lessons = $db->loadobjectlist();

					foreach ($module_lessons as $ind => $lesson)
					{
						/* Additional checking applied to check if the the all the related tables have populated while uploading format files*/
						if ($lesson->format != 'tmtQuiz')
						{
							$queryForFormat = $db->getQuery(true);
							$queryForFormat->select('m.sub_format,m.format,m.source');
							$queryForFormat->from('#__tjlms_media as m');
							$queryForFormat->join('LEFT', '#__tjlms_lessons as l ON l.media_id=m.id');
							$queryForFormat->where("l.id = " . $lesson->id);
							$db->setQuery($queryForFormat);
							$res = $db->loadObject();

							if (!empty($res))
							{
								$plg_type = 'tj' . $res->format;
								$format_subformat = explode('.', $res->sub_format);
								$plg_name = $format_subformat[0];

								JPluginHelper::importPlugin($plg_type);
								$dispatcher = JDispatcher::getInstance();
								$checkFormat = $dispatcher->trigger('additional' . $plg_name . 'FormatCheck', array($lesson->id, $res));

								if (!empty($checkFormat))
								{
									$format_res = $checkFormat[0];

									if (!$format_res)
									{
										unset($module_lessons[$ind]);
										continue;
									}
									else
									{
										$lesson->sub_format = $format_res->sub_format;
										$lesson->format = $format_res->format;

										$plugins = JPluginHelper::getPlugin($plg_type);

										if (!empty($plugins))
										{
											foreach ($plugins as $plugin)
											{
												if ($plugin->name == $plg_name)
												{
													$params 	= new JRegistry($plugin->params);
													$isAssess	= $params->get('assessment', 0);
													$lesson->assessment = $isAssess;
												}
											}
										}
									}
								}
							}
						}

						$review_pending = 0;

						$query = $db->getQuery(true);
						$query->select('COUNT(lesson_status)');
						$query->from($db->quoteName('#__tjlms_lesson_track'));
						$query->where($db->quoteName('lesson_id') . ' = ' . $lesson->id);
						$query->where($db->quoteName('lesson_status') . ' = ' . $db->quote('AP'));
						$query->where($db->quoteName('user_id') . ' = ' . $db->quote(JFactory::getUser()->id));

						$db->setQuery($query);
						$review_pending_count = $db->loadResult();

						if ($review_pending_count)
						{
							$lesson->review_pending = 1;
						}
						else
						{
							$lesson->review_pending = 0;
						}

						$query = $db->getQuery(true);
						$query->select('COUNT(lesson_status)');
						$query->from($db->quoteName('#__tjlms_lesson_track'));
						$query->where($db->quoteName('lesson_id') . ' = ' . $lesson->id);
						$query->where($db->quoteName('lesson_status') . ' = ' . $db->quote('AP'));

						$db->setQuery($query);
						$review_pending_count = $db->loadResult();

						if ($review_pending_count)
						{
							$lesson->review_pending_creater = 1;
						}
						else
						{
							$lesson->review_pending_creater = 0;
						}

						// Lesson start date according to user's timezone
						$lesson->start_date = $this->techjoomlacommon->getDateInLocal($lesson->start_date);

						// Lesson end date according to user's timezone
						$lesson->end_date = $this->techjoomlacommon->getDateInLocal($lesson->end_date);

						// Get last attempt of user including icomplete started
						$module_lessons[$ind]->attemptsdonebyuser = $lessonHelper->getlesson_total_attempts_done($lesson->id, $oluser_id);

						$attemptsdone = $module_lessons[$ind]->attemptsdonebyuser;

						// Get the user status details for lesson of only paramter is set and lesson start date is valid
						if ($getlessonStatusdetails == 1)
						{
							// Get completed attempts
							$module_lessons[$ind]->completed_atttempts = count($lessonHelper->getLessonCompletedattempts($lesson->id, $oluser_id));

							// If Lesson has attempted previously, get last_accessed_on and started_on
							$statusdetails = $lessonHelper->getLessonStatusDetails($lesson->id, $oluser_id, $attemptsdone);
							$module_lessons[$ind]->statusdetails = $statusdetails;

							// Check if user has completed last attempt
							$module_lessons[$ind]->completed_last_attempt = 0;

							if (!empty($statusdetails))
							{
								$module_lessons[$ind]->completed_last_attempt = $lessonHelper->getLastAttemptStatus($lesson->id, $oluser_id, $attemptsdone, $lesson->format);
							}

							// Check if user has fulfilled the criteria to access the lesson
							$res_eligibilty_criteria = $lessonHelper->getLessonEligibiltyCriteria($lesson->id, $oluser_id, $lesson->eligibility_criteria);

							if ($lesson->eligibility_criteria)
							{
								$module_lessons[$ind]->eligibilty_lessons = $lessonHelper->getLessonsName($lesson->eligibility_criteria);
							}

							$module_lessons[$ind]->eligibilty_criteria = $res_eligibilty_criteria['eligibilty_criteria'];
							$module_lessons[$ind]->eligible_toaceess = $res_eligibilty_criteria['eligible_toaceess'];

							// If lesson is scorm, get ists TOC
							if ($lesson->format == 'scorm')
							{
								$module_lessons[$ind]->scorm_toc_tree = $lessonHelper->getLesosnScormData($lesson->id, $oluser_id);
							}
						}

						// Get lesson_status and score by attempts grading
						$statusandscore = $lessonHelper->getLessonScorebyAttemptsgrading($lesson->id, $oluser_id, $attemptsdone);

						$module_lessons[$ind]->score = JText::_('COM_TJLMS_N_A');

						if (isset($statusandscore->score) && $statusandscore->score != " ")
						{
							$module_lessons[$ind]->score = round($statusandscore->score);
						}

						if (isset($statusandscore->lesson_status) && !empty($statusandscore->lesson_status))
						{
							$module_lessons[$ind]->status = $statusandscore->lesson_status;
						}
						else
						{
							$module_lessons[$ind]->status = JText::_('COM_TJLMS_NOT_STARTED');
						}

						$lessonCount++;
					}

					$mod_data->lessons = $module_lessons;
				}
			}
			catch (Exception $e)
			{
				$this->setError($e->getMessage());

			return false;
			}
		}

		$result['module_data'] = $modules;
		$result['lesson_count'] = $lessonCount;

		return $result;
	}

	/**
	 * Function to check if student is enrolled for a particular course.
	 *
	 * @param   int  $courseId     id of course
	 * @param   int  $userId       id of user
	 * @param   int  $course_type  Course ype
	 *
	 * @return  int  $state
	 *
	 * @since  1.0.0
	 */
	public function checkifuserenroled($courseId, $userId, $course_type)
	{
		JLoader::register('TjlmsModelEnrolment', JPATH_SITE . '/components/com_tjlms/models/enrolment.php');
		$tjlmsModelEnrolment = new TjlmsModelEnrolment;
		$result = $tjlmsModelEnrolment->getEnrolledUserColumn($courseId, $userId, '*');

		$state = '';

		if (!empty($result))
		{
			if ($result->state == 1 && $course_type == 0)
			{
				$state = 1;
			}
			elseif ($result->state == 1 && $course_type == 1)
			{
				$end_time = strtotime($result->end_time);
				$today = JFactory::getDate();
				$curdate = strtotime($today);

				if ($curdate < $end_time || $result->unlimited_plan == 1)
				{
					$state = 1;
				}
				elseif ($end_time == '')
				{
					$state = -3;
				}
				else
				{
					$state = -2;
				}
			}
			else
			{
				$state = $result->state;
			}
		}

		return $state;
	}

	/**
	 * Get enrolled student for a particular course.
	 *
	 * @param   int  $course_id  id of course
	 *
	 * @return  Object  $enroled_users
	 *
	 * @since  1.0.0
	 */
	public function getallenroledUsersinfo($course_id)
	{
		$comtjlmsHelper = new comtjlmsHelper;
		$enroled_users = $comtjlmsHelper->getCourseEnrolledUsers($course_id);

		foreach ($enroled_users as $index => $enrolment_info)
		{
			$student = JFactory::getUser($enrolment_info->user_id);
			$enroled_users[$index]->avatar = "";
			$link = '';

			$enroled_users[$index]->username = JText::_('COM_TJLMS_BLOCKED_USER');
			$enroled_users[$index]->name = JText::_('COM_TJLMS_BLOCKED_USER');

			if ($student->block == 0)
			{
				$enroled_users[$index]->avatar = $comtjlmsHelper->sociallibraryobj->getAvatar($student, 50);
				$enroled_users[$index]->username = $student->username;
				$enroled_users[$index]->name = $student->name;
				$link = $comtjlmsHelper->sociallibraryobj->getProfileUrl($student);

				if ($link)
				{
					if (!parse_url($link, PHP_URL_HOST))
					{
						$link = JUri::root() . substr(JRoute::_($comtjlmsHelper->sociallibraryobj->getProfileUrl($student)), strlen(JUri::base(true)) + 1);
					}
				}
			}

			$enroled_users[$index]->profileurl = $link;
		}

		return $enroled_users;
	}

	/**
	 * Function used to get creator info
	 *
	 * @param   int  $user_id  id of user
	 *
	 * @return  Object
	 *
	 * @since  1.0.0
	 */
	public function getCreatedInfo($user_id)
	{
		$comtjlmsHelper = new comtjlmsHelper;
		$taughtBy = new stdclass;

		$link = '';
		$taughtBy->avatar = "";
		$taughtBy->id = 0;
		$taughtBy->name = JText::_('COM_TJLMS_BLOCKED_USER');
		$taughtBy->username = JText::_('COM_TJLMS_BLOCKED_USER');

		if (JUser::getTable()->load($user_id))
		{
			$taughtByUser = JFactory::getUser($user_id);
			$taughtBy->id = $taughtByUser->id;

			if ($taughtByUser->block == 0)
			{
				$taughtBy->avatar = $comtjlmsHelper->sociallibraryobj->getAvatar($taughtByUser, 50);
				$taughtBy->name = $taughtByUser->name;
				$taughtBy->username = $taughtByUser->username;
				$link = $profileUrl = $comtjlmsHelper->sociallibraryobj->getProfileUrl($taughtByUser);

				if ($profileUrl)
				{
					if (!parse_url($profileUrl, PHP_URL_HOST))
					{
						$link = JUri::root() . substr(JRoute::_($comtjlmsHelper->sociallibraryobj->getProfileUrl($taughtByUser)), strlen(JUri::base(true)) + 1);
					}
				}
			}
		}

		$taughtBy->profileurl = $link;

		return $taughtBy;
	}

	/**
	 * Get avatar of user
	 *
	 * @param   int     $id         id of user
	 * @param   string  $to_direct  integration used
	 *
	 * @return  string $avatar
	 *
	 * @since   1.0.0
	 */
	public function getavatar($id, $to_direct)
	{
		$db = JFactory::getDBO();
		$avatar = '';

		if (strcmp($to_direct, "JomSocial") == 0)
		{
			$jspath = JPATH_SITE . '/' . 'components' . '/' . 'com_community';

			if (JFolder::exists($jspath))
			{
				// Fetching the avatar fron amazon S3
				include_once $jspath . '/' . 'libraries' . '/' . 'core.php';
				$user1 = CFactory::getUser($id);
				$uimage = $user1->getThumbAvatar();
				$avatar = str_replace('administrator/', '', $uimage);
			}
		}
		elseif (strcmp($to_direct, "Community Builder") == 0)
		{
			$path = JPATH_SITE . '/' . 'components' . '/' . 'com_comprofiler';

			if (JFolder::exists($path))
			{
				$query = $db->getQuery(true);
				$query->select($db->quoteName('avatar'));
				$query->from($db->quoteName('#__comprofiler'));
				$query->where($db->quoteName('id') . ' = ' . $db->quote((int) $id));
				$db->setQuery($query);
				$avatar = $db->loadResult();

				if ($avatar)
				{
					$avatar = JURI::base() . "images/comprofiler/" . $avatar;
				}
				else
				{
					$avatar = JURI::base() . "components/com_comprofiler/plugin/language/default_language/images/tnnophoto.jpg";
				}
			}
		}

		return $avatar;
	}

	/**
	 * Get course order info
	 *
	 * @param   int  $course_id  id of course
	 *
	 * @return  int $course_user_order_info
	 *
	 * @since   1.0.0
	 */
	public function course_user_order_info($course_id)
	{
		try
		{
			$db = JFactory::getDBO();
			$user_id = JFactory::getUser()->id;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('o.status', 'o.processor')));
			$query->from($db->quoteName('#__tjlms_orders', 'o'));
			$query->join('INNER', $db->qn('#__tjlms_order_items', 'oi') . ' ON (' . $db->qn('oi.order_id') . ' = ' . $db->qn('o.id') . ')');
			$query->where($db->quoteName('o.user_id') . " = " . $db->quote((int) $user_id));
			$query->where($db->quoteName('o.course_id') . " = " . $db->quote((int) $course_id));
			$query->order($db->quoteName('o.id') . ' DESC');
			$db->setQuery($query);

			return $db->loadObject();
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}
	}

	/**
	 * Get assigned courses for the user.
	 *
	 * @param   int  $course_id   id of course
	 * @param   int  $created_by  id of course creator
	 *
	 * @return  ARRAY $record
	 *
	 * @since   1.0.0
	 */
	public function getuserAssignedUsersInfo($course_id, $created_by = 0)
	{
		$comtjlmsHelper = new comtjlmsHelper;
		$assigned_userids = $this->getuserAssignedUsers($course_id, $created_by);
		$assigned_users = array();

		foreach ($assigned_userids as $index => $assign_userid)
		{
			$assigned_users[$index] = new stdClass;
			$student = JFactory::getUser($assign_userid);
			$assigned_users[$index]->avatar = "";

			$link = '';
			$assigned_users[$index]->username = JText::_('COM_TJLMS_BLOCKED_USER');
			$assigned_users[$index]->name = JText::_('COM_TJLMS_BLOCKED_USER');

			if ($student->block == 0)
			{
				$assigned_users[$index]->avatar = $comtjlmsHelper->sociallibraryobj->getAvatar($student, 50);
				$assigned_users[$index]->username = $student->username;
				$assigned_users[$index]->name = $student->name;
				$profileUrl = $comtjlmsHelper->sociallibraryobj->getProfileUrl($student);

				if (!empty($profileUrl))
				{
					$link = JRoute::_($profileUrl);
				}
			}

			$assigned_users[$index]->profileurl = $link;
		}

		return $assigned_users;
	}

	/**
	 * Get assigned courses for the user.
	 *
	 * @param   int  $course_id    id of course
	 * @param   int  $enrolled_by  id of course creator
	 *
	 * @return  ARRAY $record
	 *
	 * @since   1.0.0
	 */
	public function getuserAssignedUsers($course_id, $enrolled_by = 0)
	{
		try
		{
			$db = JFactory::getDBO();
			$user_id = JFactory::getUser()->id;
			$query = $db->getQuery(true);
			$query->select($db->quoteName('eu.user_id'));
			$query->from($db->quoteName('#__tjlms_enrolled_users', 'eu'));
			$query->join('INNER', $db->quoteName('#__users', 'u') . ' ON u.id = eu.user_id');
			$query->where($db->quoteName('eu.course_id') . " = " . $db->quote((int) $course_id));

			if ($enrolled_by)
			{
				$query->where($db->quoteName('eu.enrolled_by') . " = " . $db->quote((int) $enrolled_by));
			}

			$query->where($db->quoteName('eu.state') . '=1');
			$query->setLimit('5');
			$db->setQuery($query);

			return $db->loadColumn();
		}
		catch (Exception $e)
		{
			JError::raiseWarning($e->getCode(), $e->getMessage());
		}
	}

	/**
	 * Get recommend user for the course.
	 *
	 * @param   int  $courseId  id of course
	 * @param   int  $userId    id of user
	 *
	 * @return  ARRAY $record
	 *
	 * @since   1.0.0
	 */
	public function getuserRecommendedUsers($courseId, $userId)
	{
		$comtjlmsHelper = new comtjlmsHelper;

		try
		{
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select($db->quoteName('lr.assigned_to'));
			$query->from($db->quoteName('#__jlike_todos', 'lr'));
			$query->join('INNER', $db->qn('#__jlike_content', 'lc') . ' ON (' . $db->qn('lc.id') . ' = ' . $db->qn('lr.content_id') . ')');
			$query->join('INNER', $db->quoteName('#__users', 'u') . ' ON u.id = lr.assigned_to');
			$query->where($db->quoteName('lr.assigned_by') . '=' . $db->quote((int) $userId));
			$query->where($db->quoteName('lr.type') . '=' . $db->quote('reco'));
			$query->where($db->quoteName('lc.element_id') . '=' . $db->quote((int) $courseId));

			$query->setLimit('6');
			$db->setQuery($query);
			$recommendedusers = $db->loadColumn();
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		foreach ($recommendedusers as $index => $recommend_userid)
		{
			$recommendedusers[$index] = new stdClass;
			$student = JFactory::getUser($recommend_userid);
			$recommendedusers[$index]->avatar = "";
			$link = '';
			$recommendedusers[$index]->username = JText::_('COM_TJLMS_BLOCKED_USER');
			$recommendedusers[$index]->name = JText::_('COM_TJLMS_BLOCKED_USER');

			if ($student->block == 0)
			{
				$recommendedusers[$index]->avatar = $comtjlmsHelper->sociallibraryobj->getAvatar($student, 50);
				$recommendedusers[$index]->username = $student->username;
				$recommendedusers[$index]->name = $student->name;

				$profileUrl = $comtjlmsHelper->sociallibraryobj->getProfileUrl($student);

				if (!empty($profileUrl))
				{
					$link = JRoute::_($profileUrl);
				}
			}

			$recommendedusers[$index]->profileurl = $link;
		}

		return $recommendedusers;
	}

	/**
	 * Check if user has a course track
	 *
	 * @param   int  $course_id  id of course
	 * @param   int  $user_id    id of user
	 *
	 * @return  boolean
	 *
	 * @since   1.0.0
	 */
	public function CheckIfUserHasProgress($course_id, $user_id)
	{
		$db = JFactory::getDBO();

		// Add Table Path
		JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_tjlms/tables');
		$courseTrack = JTable::getInstance('Coursetrack', 'TjlmsTable', array('dbo', $db));
		$courseTrack->load(array('course_id' => (int) $course_id, 'user_id' => (int) $user_id));

		if ($courseTrack->id)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Function to add certificate  entry
	 *
	 * @param   INT  $courseId  Course ID
	 * @param   INT  $userId    user ID
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function addCertEntry($courseId, $userId = '')
	{
		JLoader::import('components.com_tjlms.models.certificate', JPATH_SITE);
		$certModel = JModelLegacy::getInstance('Certificate', 'TjlmsModel');
		$data = array('course_id' => (int) $courseId, 'user_id' => (int) $userId);
		$result = $certModel->save($data);

		return $result;
	}

	/**
	 * function to get html and params for certificate
	 *
	 * @param   INT  $courseId  Course ID
	 *
	 * @param   INT  $userId    User ID
	 *
	 * @return  Array
	 *
	 * @since 1.0
	 */
	public function getcertificateHTML($courseId, $userId = '')
	{
		$db = JFactory::getDBO();
		$comtjlmsHelper = new comtjlmsHelper;
		$html = array();

		$msg = $this->getcourse_Certificate_data($courseId, $userId);
		$msg['course_id'] = $courseId;
		JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_tjlms/tables');
		$course = JTable::getInstance('course', 'TjlmsTable', array('dbo', $db));

		$course->load(array('id' => (int) $courseId));
		$result = isset($course->certificate_id) ? (int) $course->certificate_id : 0;

		$certtmpl = JTable::getInstance('certificatetemplate', 'TjlmsTable', array('dbo', $db));
		$certtmpl->load(array('id' => (int) $result));

		// Get the params for the current certificate template.
		$html['params'] = isset($certtmpl->params) ? $certtmpl->params : '';
		$result = isset($certtmpl->body) ? $certtmpl->body : '';

		// Trigger to replace tag
		JPluginHelper::importPlugin('system');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onBeforeTjlmsTagReplace', array($course, $userId, &$msg));

		if (!empty($result))
		{
			$msg['msg_body'] = $result;
		}

		// Replace Special Tags from Backend Ticket Template
		$html['html']   = $comtjlmsHelper->tagreplace($msg);

		// On after tag replace
		JPluginHelper::importPlugin('system');
		$dispatcher->trigger('onAfterTagReplace', array($html['html']));

		return $html;
	}

	/**
	 * Function to get certificate date
	 *
	 * @param   INT  $c_id    Course ID
	 *
	 * @param   INT  $userId  User ID
	 *
	 * @return  ARRAY
	 *
	 * @since  1.0.0
	 */
	public function getcourse_Certificate_data($c_id, $userId = '')
	{
		$lmsparams = JComponentHelper::getParams('com_tjlms');
		$dateFormat = $lmsparams->get('certificate_date_format', 'j F Y');

		if ($c_id)
		{
			if ($userId != '')
			{
				$user = JFactory::getUser($userId);
			}
			else
			{
				$user = JFactory::getUser();
			}

			$tjlmsCoursesHelper   = new TjlmsCoursesHelper;
			$db                   = JFactory::getDBO();
			$details              = $tjlmsCoursesHelper->getcourseInfo($c_id);
			$result['course']     = $details->title;
			$result['studentname']     = $user->name;
			$result['studentusername'] = $user->username;

			JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_tjlms/tables');
			$coursetrack = JTable::getInstance('coursetrack', 'TjlmsTable', array('dbo', $db));
			$coursetrack->load(array('course_id' => (int) $c_id, 'user_id' => (int) $user->id));
			$certtrack = JTable::getInstance('certificate', 'TjlmsTable', array('dbo', $db));
			$certtrack->load(array('course_id' => (int) $c_id, 'user_id' => (int) $user->id));

			$result['granted_date'] = JHtml::date($certtrack->grant_date, $dateFormat);

			if ($certtrack->exp_date == '0000-00-00 00:00:00')
			{
				$result['expiry_date'] = '';
			}
			else
			{
				$result['expiry_date'] = JHtml::date($certtrack->exp_date, $dateFormat);
			}

			$result['cert_id'] = $certtrack->cert_id;
			$result['total_time'] = $this->getTotalTimeSpentOnCourse($c_id, $user->id);

			return $result;
		}
	}

	/**
	 * Get recommend user for the course.
	 *
	 * @param   int  $courseId  id of course
	 * @param   int  $userId    id of user
	 *
	 * @return  ARRAY $record
	 *
	 * @since   1.0.0
	 */
	public function getAssignedDueDate($courseId, $userId)
	{
		$comtjlmsHelper = new comtjlmsHelper;

		try
		{
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select($db->quoteName('lr.due_date'));
			$query->from($db->quoteName('#__jlike_todos', 'lr'));
			$query->join('INNER', $db->qn('#__jlike_content', 'lc') . ' ON (' . $db->qn('lc.id') . ' = ' . $db->qn('lr.content_id') . ')');
			$query->where($db->quoteName('lr.assigned_to') . '=' . $db->quote((int) $userId));
			$query->where($db->quoteName('lr.type') . '=' . $db->quote('assign'));
			$query->where($db->quoteName('lc.element_id') . '=' . $db->quote((int) $courseId));
			$db->setQuery($query);

			return $db->loadResult();
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}
	}

	/**
	 *  Function to get Total Time spent on Course
	 *
	 * @param   INT  $courseId  course_id
	 *
	 * @param   INT  $userId    logged in userId
	 *
	 * @return  totalSpentTime.
	 *
	 * @since 1.0.0
	 */
	public function getTotalTimeSpentOnCourse($courseId, $userId)
	{
		try
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('SEC_TO_TIME(SUM(TIME_TO_SEC(lt.time_spent))) as timeSpentOnLesson');
			$query->from($db->quoteName('#__tjlms_lesson_track', 'lt'));
			$query->join('LEFT', $db->quoteName('#__tjlms_lessons', 'l') . ' ON (' . $db->quoteName('l.id') . ' = ' . $db->quoteName('lt.lesson_id') . ')');
			$query->where($db->quoteName('lt.user_id') . ' = ' . $db->quote((int) $userId));
			$query->where($db->quoteName('l.course_id') . ' = ' . $db->quote((int) $courseId));
			$db->setQuery($query);

			return $db->loadresult();
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 *
	 * @since	1.6
	 */
	public function getItem($pk = null)
	{
		$user   = JFactory::getUser();
		$userId = $user->id;

		$pk = (!empty($pk)) ? $pk : (int) $this->getState('course.id');

		if ($this->_item === null)
		{
			$this->_item = array();
		}

		if (!isset($this->_item[$pk]))
		{
			try
			{
				JLoader::import('components.com_tjlms.helpers.courses', JPATH_SITE);
				JLoader::import('components.com_tjlms.helpers.tracking', JPATH_SITE);
				JLoader::import('components.com_tjlms.helpers.main', JPATH_SITE);
				JLoader::import('components.com_jlike.models.jlike_likes', JPATH_SITE);

				$tjlmsCoursesHelper = new TjlmsCoursesHelper;
				$comtjlmsHelper 	= new comtjlmsHelper;
				$trackingHelper		= new ComtjlmstrackingHelper;
				$tagsClass 			= new JHelperTags;

				$likesModel 		= JModelLegacy::getInstance('jlike_Likes', 'JlikeModel', array('ignore_request' => true));

				$params  		= JComponentHelper::getParams('com_tjlms');
				$userCol 		= $params->get('show_user_or_username', 'name');
				$enable_tags 	= $params->get('enable_tags', '0', 'INT');

				$db = $this->getDbo();
				$query = $db->getQuery(true);
				$query->select($this->getState('course.select', 'a.*'));
				$query->from($db->quoteName('#__tjlms_courses', 'a'));
				$query->where($db->quoteName('a.id') . '=' . $db->quote((int) $pk));

				$query->select(array($db->qn('c.id', 'course_cat_id'), $db->qn('c.title', 'course_cat_title'), $db->qn('c.access', 'category_access')));
				$query->join('INNER', $db->quoteName('#__categories', 'c') . ' ON (' . $db->quoteName('c.id') . ' = ' . $db->quoteName('a.catid') . ')');
				$query->where($db->quoteName('c.published') . '>' . $db->quote('0'));

				// Join on user table.
				if ($userCol == 'username')
				{
					$query->select($db->quoteName('u.username', 'course_creator_name'));
				}
				else
				{
					$query->select($db->quoteName('u.name', 'course_creator_name'));
				}

				$query->join('LEFT', $db->quoteName('#__users', 'u') . ' ON (' . $db->quoteName('u.id') . ' = ' . $db->quoteName('a.created_by') . ')');

				// Filter by start and end dates.
				$nullDate = $db->quote($db->getNullDate());
				$date = JFactory::getDate();

				$nowDate = $db->quote($date->toSql());

				$query->where("(" . $db->quoteName('a.start_date') . '=' . $nullDate . ' OR ' . $db->quoteName('a.start_date') . ' <= ' . $nowDate . ")");
				$query->where("(" . $db->quoteName('a.end_date') . ' = ' . $nullDate . ' OR ' . $db->quoteName('a.end_date') . ' >= ' . $nowDate . ")");

				// Filter by published state.
				$query->where($db->quoteName('a.state') . ' = ' . $db->quote('1'));
				$query->where($db->quoteName('c.published') . ' = ' . $db->quote('1'));
				$db->setQuery($query);

				$data = new stdClass;
				$data->course_info = $db->loadObject();

				if (empty($data->course_info))
				{
					throw new Exception(JText::_('COM_TJLMS_ERROR_CONTENT_NOT_FOUND'), 400);

					return;
				}

				// If no access filter is set, the layout takes some responsibility for display of limited information.
				$groups = $user->getAuthorisedViewLevels();

				if ($data->course_info->course_cat_id == 0 || $data->course_info->category_access === null)
				{
					$authorize = in_array($data->course_info->access, $groups);
				}
				else
				{
					$authorize = in_array($data->course_info->access, $groups) && in_array($data->course_info->access, $groups);
				}

				if (empty($authorize))
				{
					throw new Exception(JText::_('COM_TJLMS_ERROR_NOT_AUTHORIZED'), 403);

					return;
				}

				// Course Image
				if ($data->course_info->storage != 'invalid' && !empty($data->course_info->image))
				{
					$data->course_info->course_image = $tjlmsCoursesHelper->getCourseImage(
						array(
							'image' => $data->course_info->image, 'storage' => $data->course_info->storage), 'S_'
						);
				}

				// Like and dislike data
				$extraParams 		= array('plg_name' => 'jlike_tjlms', 'plg_type' => 'content');
				$data->likesData	= $likesModel->getData($pk, 'com_tjlms.course', true, $extraParams);

				// Course Tags
				if ($enable_tags == 1)
				{
					$data->course_tags = $tagsClass->getItemTags('com_tjlms.course', $pk);
				}

				// Enrollment Detail
				$data->enrolled_users		= $this->getallenroledUsersinfo($pk);
				$enrolled_count 			= count($data->enrolled_users);
				$data->enrolled_users_cnt 	= $comtjlmsHelper->custom_number_format($enrolled_count);
				$data->enrolled 			= (int) $this->checkifuserenroled($pk, $userId, $data->course_info->type);

				// Course Track
				if ($data->enrolled)
				{
					$data->courseTrack = $trackingHelper->getCourseTrackEntry($pk, $userId);
				}

				// Paid Plan
				if ($data->course_info->type)
				{
					$data->course_subscription_plans = $tjlmsCoursesHelper->getCourseSubplans($pk);
				}

				$data->course_toc  = $this->getCourseTocdetails($pk);

				$this->_item[$pk] = $data;

				unset($data);
			}
			catch (Exception $e)
			{
				// Need to go thru the error handler to allow Redirect to work.
				$this->setError($e);
				$this->_item[$pk] = false;
			}
		}

		return $this->_item[$pk];
	}
}

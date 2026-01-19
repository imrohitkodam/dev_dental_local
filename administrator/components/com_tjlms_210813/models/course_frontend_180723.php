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
			$courseInfo->creator_name = JFactory::getUser($courseInfo->created_by)->name;
			$courseInfo->creator_username = JFactory::getUser($courseInfo->created_by)->username;

			// Get image accorfing to storage
			$courseInfo->image = $this->tjlmsCoursesHelper->getCourseImage((array) $courseInfo, 'S_');
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
			/*$query = $db->getQuery(true);
			$query->select(array("l . * , m.name"));
			$query->from($db->quoteName('#__tjlms_lessons', 'l'));
			$query->join('LEFT', $db->quoteName('#__tjlms_modules', 'm') . ' ON (' . $db->quoteName('l.mod_id ') . ' = ' . $db->quoteName('m.id') . ')');
			$query->where($db->quoteName('l.state') . ' = 1');
			$query->where($db->quoteName('l.course_id') . ' = ' . $course_id);
			$query->where($db->quoteName('l.format') . " <> '' " );
			$query->group($db->quoteName(array('l.mod_id, l.id')));
			$query->order('m.ordering ASC');
			$db->setQuery($query);
			$tocData = $db->loadobjectlist('id');*/

			$query = $db->getQuery(true);
			$query->select('id,name');
			$query->from($db->quoteName('#__tjlms_modules'));
			$query->where($db->quoteName('course_id') . ' = ' . $course_id);
			$query->where($db->quoteName('state') . ' = 1');
			$query->order('ordering ASC');

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
				$query->where($db->quoteName('mod_id') . ' = ' . $mod_data->id);
				$query->where($db->quoteName('state') . ' = 1');
				$query->where($db->quoteName('format') . "<> ''");
				$query->order('ordering ASC');

				$db->setQuery($query);
				$module_lessons = $db->loadobjectlist();

				foreach ($module_lessons as $ind => $lesson)
				{
					/* Additional checking applied to check if the the all the related tables have populated while uploading format files*/
					if ($lesson->format != 'tmtQuiz')
					{
						$queryForFormat = $db->getQuery(true);
						$queryForFormat->select('m.sub_format,m.format');
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
								}
							}
						}
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

		$result['module_data'] = $modules;
		$result['lesson_count'] = $lessonCount;

		return $result;
	}

	/**
	 * Function to check if student is enrolled for a particular course.
	 *
	 * @param   int  $course_id    id of course
	 * @param   int  $user_id      id of user
	 * @param   int  $course_type  Course ype
	 *
	 * @return  int  $state
	 *
	 * @since  1.0.0
	 */
	public function checkifuserenroled($course_id, $user_id, $course_type)
	{
		$result = $this->tjlmsdbhelperObj->get_records('*', 'tjlms_enrolled_users', array(
			"course_id" => $course_id,
			"user_id" => $user_id
		), '', 'loadObject');

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

				if ($curdate < $end_time)
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
			$enrol_userid = $enrolment_info->user_id;
			$enroled_users[$index]->username = JFactory::getUser($enrol_userid)->username;
			$enroled_users[$index]->avatar = $comtjlmsHelper->sociallibraryobj->getAvatar($student, 50);
			$link = '';
			$link = $profileUrl = $comtjlmsHelper->sociallibraryobj->getProfileUrl($student);

			if ($profileUrl)
			{
				if (!parse_url($profileUrl, PHP_URL_HOST))
				{
					$link = JUri::root() . substr(JRoute::_($comtjlmsHelper->sociallibraryobj->getProfileUrl($student)), strlen(JUri::base(true)) + 1);
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
		$taughtByUser = JFactory::getUser($user_id);
		$taughtBy = new stdclass;
		$taughtBy->id = $user_id;
		$taughtBy->name = JFactory::getUser($user_id)->name;
		$taughtBy->avatar = $comtjlmsHelper->sociallibraryobj->getAvatar($taughtByUser, 100);
		$link = '';
		$link = $profileUrl = $comtjlmsHelper->sociallibraryobj->getProfileUrl($taughtByUser);

		if ($profileUrl)
		{
			if (!parse_url($profileUrl, PHP_URL_HOST))
			{
				$link = JUri::root() . substr(JRoute::_($comtjlmsHelper->sociallibraryobj->getProfileUrl($taughtByUser)), strlen(JUri::base(true)) + 1);
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
		$database = JFactory::getDBO();
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
				$q = "SELECT avatar
						FROM #__comprofiler
						WHERE id=$id";
				$database->setQuery($q);
				$avatar = $database->loadResult();

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
		$db = JFactory::getDBO();
		$user_id = JFactory::getUser()->id;

		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('o.status', 'o.processor')));
		$query->from($db->quoteName('#__tjlms_orders') . 'as o');
		$query->join('INNER', $db->quoteName('#__tjlms_order_items') . 'as oi ON oi.order_id=o.id');
		$query->where('o.user_id=' . $user_id);
		$query->where('o.course_id=' . $course_id);
		$query->order('o.id DESC');

		$db->setQuery($query);
		$course_user_order_info = $db->loadObject();

		return $course_user_order_info;
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
			$enrol_userid = $assign_userid;
			$assigned_users[$index]->username = JFactory::getUser($enrol_userid)->username;
			$assigned_users[$index]->name = JFactory::getUser($enrol_userid)->name;
			$assigned_users[$index]->avatar = $comtjlmsHelper->sociallibraryobj->getAvatar($student, 50);

			$link = '';
			$profileUrl = $comtjlmsHelper->sociallibraryobj->getProfileUrl($student);

			if (!empty($profileUrl))
			{
				$link = JRoute::_($profileUrl);
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
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('eu.user_id');
		$query->from('`#__tjlms_enrolled_users` as eu');
		$query->where('eu.course_id=' . (int) $course_id);

		if ($enrolled_by)
		{
			$query->where('eu.enrolled_by=' . (int) $enrolled_by . '');
		}

		$query->where('eu.state=1 LIMIT 0,5');

		// Set the query for execution.
		$db->setQuery($query);

		return $db->loadColumn();
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

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('lr.assigned_to');
		$query->from('`#__jlike_todos` as lr');
		$query->join('INNER', '`#__jlike_content` as lc ON lc.id=lr.content_id');
		$query->where('lr.assigned_by=' . (int) $userId);
		$query->where('lr.type="reco"');
		$query->where('lc.element_id=' . (int) $courseId . ' LIMIT 0,5');

		// Set the query for execution.
		$db->setQuery($query);

		$recommendedusers = $db->loadColumn();

		foreach ($recommendedusers as $index => $recommend_userid)
		{
			$recommendedusers[$index] = new stdClass;
			$student = JFactory::getUser($recommend_userid);

			$recommendedusers[$index]->username = JFactory::getUser($recommend_userid)->username;
			$recommendedusers[$index]->name = JFactory::getUser($recommend_userid)->name;
			$recommendedusers[$index]->avatar = $comtjlmsHelper->sociallibraryobj->getAvatar($student, 50);
			$link = '';
			$profileUrl = $comtjlmsHelper->sociallibraryobj->getProfileUrl($student);

			if (!empty($profileUrl))
			{
				$link = JRoute::_($profileUrl);
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
		$courseTrack->load(array('course_id' => $course_id, 'user_id' => $user_id));

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
		$data = array('course_id' => $courseId, 'user_id' => $userId);
		$result = $certModel->save($data);

		return $result;
	}

	/**
	 * function to get html for certificate
	 *
	 * @param   INT  $courseId  Course ID
	 *
	 * @param   INT  $userId    User ID
	 *
	 * @return  Html
	 *
	 * @since 1.0
	 */
	public function getcertificateHTML($courseId, $userId = '')
	{
		$db = JFactory::getDBO();
		$comtjlmsHelper = new comtjlmsHelper;

		$msg = $this->getcourse_Certificate_data($courseId, $userId);
		$msg['course_id'] = $courseId;
		JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_tjlms/tables');
		$course = JTable::getInstance('course', 'TjlmsTable', array('dbo', $db));
		$course->load(array('id' => $courseId));
		$result = isset($course->certificate_id) ? (int) $course->certificate_id : 0;

		$certtmpl = JTable::getInstance('certificatetemplate', 'TjlmsTable', array('dbo', $db));
		$certtmpl->load(array('id' => $result));
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
		$html = $comtjlmsHelper->tagreplace($msg);

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
			$coursetrack->load(array('course_id' => $c_id, 'user_id' => $user->id));
			$certtrack = JTable::getInstance('certificate', 'TjlmsTable', array('dbo', $db));
			$certtrack->load(array('course_id' => $c_id, 'user_id' => $user->id));

			$result['granted_date'] = JHtml::date($certtrack->grant_date, 'j F Y');

			if ($certtrack->exp_date == '0000-00-00 00:00:00')
			{
				$result['expiry_date'] = '';
			}
			else
			{
				$result['expiry_date'] = JHtml::date($certtrack->exp_date, 'j F Y');
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

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('lr.due_date');
		$query->from('`#__jlike_todos` as lr');
		$query->join('INNER', '`#__jlike_content` as lc ON lc.id=lr.content_id');
		$query->where('lr.assigned_to=' . (int) $userId);
		$query->where('lr.type="assign"');
		$query->where('lc.element_id=' . (int) $courseId);

		// Set the query for execution.
		$db->setQuery($query);

		$assignment_due_date = $db->loadResult();

		return $assignment_due_date;
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
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Sum of time spent by user
		$query->select('SEC_TO_TIME(SUM(TIME_TO_SEC(lt.time_spent))) as timeSpentOnLesson');
		$query->from($db->quoteName('#__tjlms_lesson_track') . ' as lt');
		$query->join('LEFT', '#__tjlms_lessons as l ON l.id=lt.lesson_id');
		$query->where($db->quoteName('lt.user_id') . ' = ' . (int) $userId);
		$query->where($db->quoteName('l.course_id') . ' = ' . (int) $courseId);

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$totalSpentTime = $db->loadresult();

		return $totalSpentTime;
	}

	/**
	 * Method to get the form for extra fields.This form file will be created by field manager.
	 *
	 * @param   array  $id  An optional array of data for the form to interogate.
	 *
	 * @return	void
	 *
	 * @since	1.6
	 */
	public function getDataExtra($id = null)
	{
		if (empty($id))
		{
			$id = $this->getState('course.id');
		}

		if (empty($id))
		{
			return false;
		}

		// Get course details
		$c_info = $this->getcourseInfo($id);
		$TjfieldsHelperPath = JPATH_SITE . DS . 'components' . DS . 'com_tjfields' . DS . 'helpers' . DS . 'tjfields.php';

		if (!class_exists('TjfieldsHelper'))
		{
			JLoader::register('TjfieldsHelper', $TjfieldsHelperPath);
			JLoader::load('TjfieldsHelper');
		}

		$tjFieldsHelper = new TjfieldsHelper;
		$data               = array();
		$data['client']     = 'com_tjlms.course';
		$data['content_id'] = $id;

		// Pass course creator id & extra fields data
		$extra_fields_data = $tjFieldsHelper->FetchDatavalue($data, '');

		return $extra_fields_data;
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
				$query = $db->getQuery(true)
					->select(
						$this->getState(
							'course.select', 'a.*'
						)
					);

				$query->from('#__tjlms_courses AS a')
					->where('a.id = ' . (int) $pk);

				// Join on category table.
				$query->select('c.id course_cat_id, c.title AS course_cat_title, c.access AS category_access')
					->innerJoin('#__categories AS c on c.id = a.cat_id')
					->where('c.published > 0');

				// Join on user table.
				if ($userCol == 'username')
				{
					$query->select('u.username AS course_creator_name');
				}
				else
				{
					$query->select('u.name AS course_creator_name');
				}

				$query->join('LEFT', '#__users AS u on u.id = a.created_by');

				// Filter by start and end dates.
				$nullDate = $db->quote($db->getNullDate());
				$date = JFactory::getDate();

				$nowDate = $db->quote($date->toSql());

				$query->where('(a.start_date = ' . $nullDate . ' OR a.start_date <= ' . $nowDate . ')')
					->where('(a.end_date = ' . $nullDate . ' OR a.end_date >= ' . $nowDate . ')');

				// Filter by published state.
				$query->where("a.state = 1");
				$query->where("c.published = 1");

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

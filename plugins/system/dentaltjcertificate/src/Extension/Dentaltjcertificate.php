<?php
/**
 * @package    Plg_System_Dentaltjcertificate
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (C) 2009 - 2025 Techjoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Plugin\System\Dentaltjcertificate\Extension;

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Table\Table;
/**
 * Dental tjcertificate Plugin
 *
 * @since  1.0.0
 */
class Dentaltjcertificate extends CMSPlugin
{
	/**
	 * Application object
	 *
	 * @var    \Joomla\CMS\Application\CMSApplication
	 * @since  4.0.0
	 */
	protected $app;

	/**
	 * Database object
	 *
	 * @var    DatabaseInterface
	 * @since  4.0.0
	 */
	protected $db;

	/**
	 * Load the language file on instantiation
	 *
	 * @var    boolean
	 * @since  4.0.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Function used as a trigger after user com[lete a lesson. While considering attempt grading
	 *
	 * @param   INT  $lessonId  Lesson ID
	 * @param   INT  $attempt   attempt number of the user
	 * @param   INT  $actorId   User who is attempting the lesson
	 *
	 * @return  boolean true or false
	 *
	 * @since  1.0.0
	 */
	public function onTjlmsAfterLessonCompletion($lessonId, $attempt, $actorId)
	{
		$obj = $this->TjlmsLessonHelper->getLessonColumn($lessonId, 'course_id');
		$this->onAddCourseTrackEntry($obj->course_id, $actorId, $lessonId);

		return true;
	}

	/**
	 * Function onBeforeTjlmsTagReplace to tagreplace on certificate with course's custom fields
	 *
	 * @param   object|int  $courseOrId  course object or course ID
	 * @param   INT         $userId      User id
	 * @param   Array       &$msg        message
	 *
	 * @return  boolean true or false
	 *
	 * @since  1.0.0
	 */
	public function onBeforeTjlmsTagReplace($course, $userId, &$msg)
	{
		// Handle both course object and course ID
		if (is_object($course))
		{
			$courseId = isset($course->id) ? $course->id : (isset($course->course_id) ? $course->course_id : 0);
		}
		else
		{
			$courseId = (int) $course;
		}

		if (empty($courseId) && is_object($course))
		{
			// Try to get course_id from msg if available
			$courseId = isset($msg['course_id']) ? $msg['course_id'] : (isset($msg->course_id) ? $msg->course_id : 0);
		}

		if (empty($courseId))
		{
			return false;
		}

		// Don't convert $msg to array - keep it as object to preserve structure
		// PHP allows array notation on objects, so we can use both
		// Initialize other object if it doesn't exist
		if (is_object($msg))
		{
			if (!isset($msg->other) || !is_object($msg->other))
			{
				$msg->other = new \stdClass;
			}
			// Also set as array key for tag replacement
			$msg->other_array = $msg->other;
		}
		else
		{
			if (!isset($msg['other']) || !is_object($msg['other']))
			{
				$msg['other'] = new \stdClass;
			}
		}

		$user = \ES::user($userId);
		$link = $user->getGoalsPermalink('updateavatar');

		// Handle course fields if available
		if (is_object($course) && isset($course->course_info->fields))
		{
			foreach ($course->course_info->fields as $field)
			{
				$msg['course.field.' . $field->name] = $field->value;
			}
		}

		$cpdDate = $this->getCpdDate($userId);

		$professionData = $this->getProfession($userId);
		$profession = array();

		foreach ($professionData as $item)
		{
			array_push($profession, $item->title);
		}

		$professionStr = implode(', ', $profession);
		
		// Set values in both object and array format
		if (is_object($msg))
		{
			$msg->other->profession = $professionStr;
			// For tag replacement, we need array keys
			// PHP allows array notation on objects, but we'll set it explicitly
			$msg->{'other.profession'} = $professionStr;
		}
		else
		{
			$msg['other']->profession = $professionStr;
			$msg['other.profession'] = $professionStr;
		}

		$answers = $this->getSurveyAnswer($userId, $courseId);
		if ($answers !== false && !empty($answers) && is_array($answers))
		{
			$answersStr = implode(', ', $answers);
			$options = str_replace("0", "A", $answersStr);
			$options = str_replace("1", "B", $options);
			$options = str_replace("2", "C", $options);
			$options = str_replace("3", "D", $options);
		}
		else
		{
			$options = '';
		}

		// Set values in both object and array format
		if (is_object($msg))
		{
			$msg->other->cpd_development_outcomes = $options;
			$msg->{'other.cpd_development_outcomes'} = $options;
		}
		else
		{
			$msg['other']->cpd_development_outcomes = $options;
			$msg['other.cpd_development_outcomes'] = $options;
		}

		$timeSpendOnCourse = $this->timeSpentOnCourse($userId, $courseId, $professionStr);
		$totalFiveYearTimeSpentOnCourse = $this->totalFiveYearTimeSpentOnCourse($userId, $courseId, $professionStr);
		$totalTimeSpentOnCourse = $this->totalTimeSpentOnCourse($userId, $courseId);
		$timeSpentforCompletingCourse = $this->timeSpentforCompletingCourse($userId, $courseId);

		$yearTime = (!empty($timeSpendOnCourse)) ? $timeSpendOnCourse : Text::_('COM_TJLMS_COURSE_CERTIFICATE_TIMESET_EMPTY');
		$fiveYearTotalTime = (!empty($totalFiveYearTimeSpentOnCourse)) ?
			$totalFiveYearTimeSpentOnCourse : Text::_('COM_TJLMS_COURSE_CERTIFICATE_TIMESET_EMPTY');
		$totalTime = (!empty($totalTimeSpentOnCourse)) ? $totalTimeSpentOnCourse : Text::_('COM_TJLMS_COURSE_CERTIFICATE_TIMESET_EMPTY');
		$timeSpent = (!empty($timeSpentforCompletingCourse)) ?
			$timeSpentforCompletingCourse : Text::_('COM_TJLMS_COURSE_CERTIFICATE_TIMESET_EMPTY');

		// Set values in both object and array format
		if (is_object($msg))
		{
			$msg->other->year_time = $yearTime;
			$msg->{'other.year_time'} = $yearTime;
			$msg->other->five_year_total_time = $fiveYearTotalTime;
			$msg->{'other.five_year_total_time'} = $fiveYearTotalTime;
			$msg->other->total_time = $totalTime;
			$msg->{'other.total_time'} = $totalTime;
			$msg->other->time_spent = $timeSpent;
			$msg->{'other.time_spent'} = $timeSpent;
		}
		else
		{
			$msg['other']->year_time = $yearTime;
			$msg['other.year_time'] = $yearTime;
			$msg['other']->five_year_total_time = $fiveYearTotalTime;
			$msg['other.five_year_total_time'] = $fiveYearTotalTime;
			$msg['other']->total_time = $totalTime;
			$msg['other.total_time'] = $totalTime;
			$msg['other']->time_spent = $timeSpent;
			$msg['other.time_spent'] = $timeSpent;
		}

		$cycleStartDate = "";
		$cycleEndDate = "";

		// Check if user is Dentist profession
		if (strtolower($professionStr) == 'dentist')
		{
			$cycleEndDate = Text::sprintf('COM_TJLMS_COURSE_CERTIFICATE_FIVE_YEAR_CYCLE', $link);

			// Check for cpd date is set or not
			if (!empty($cpdDate))
			{
				// CPD Cycle start date set to 1 Jan
				$cycleStartDate = HTMLHelper::date('01 January' . date('Y', strtotime($cpdDate)), Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT'), true);

				// CPD Cycle end date set to 31 Dec
				$cycleEndDate = HTMLHelper::date('31 December' . date('Y', strtotime($cpdDate)) . '+4 year',
					Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT'), true
					);
			}
		}
		else
		{
			// For other than dentist profession

			$cycleEndDate = Text::sprintf('COM_TJLMS_COURSE_CERTIFICATE_FIVE_YEAR_CYCLE', $link);

			// Check for cpd date is set
			if (!empty($cpdDate))
			{
				// Flag date set as 31 July for comparison
				$flagDate = HTMLHelper::date('31 July' . date('Y', strtotime($cpdDate)), Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT'), true);
				$tempDate = HTMLHelper::date($cpdDate, Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT'), true);

				// CPD Cycle start date set to 1 Aug
				$cycleStartDate = HTMLHelper::date('01 August' . date('Y', strtotime($cpdDate)), Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT'), true);

				// Check for cpd date is less than 31 Aug
				if (strtotime($tempDate) <= strtotime($flagDate))
				{
					$cycleStartDate = HTMLHelper::date('01 August' . date('Y', strtotime($cpdDate)) . '-1 year', Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT'), true);
				}

				// CPD Cycle End date after 5 year
				$cycleEndDate = HTMLHelper::date('31 July' . date('Y', strtotime($cycleStartDate)) . '+5 year',
						Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT'), true
					);
			}
		}

		// Set the cycle dates in the message array
		if (is_object($msg))
		{
			$msg->other->cycle_start_date = $cycleStartDate;
			$msg->{'other.cycle_start_date'} = $cycleStartDate;
			$msg->other->cycle_end_date = $cycleEndDate;
			$msg->{'other.cycle_end_date'} = $cycleEndDate;
		}
		else
		{
			$msg['other']->cycle_start_date = $cycleStartDate;
			$msg['other.cycle_start_date'] = $cycleStartDate;
			$msg['other']->cycle_end_date = $cycleEndDate;
			$msg['other.cycle_end_date'] = $cycleEndDate;
		}
	}

	/**
	 * Function to get Survey answer
	 *
	 * @param   INT  $userId    logged in userId
	 *
	 * @param   INT  $courseId  course_id
	 *
	 * @return  mixed $result.
	 *
	 * @since 1.0.0
	 */
	public function getSurveyAnswer($userId, $courseId)
	{
		$questionText = Text::_('PLG_DENTALTJCERTIFICATE_CPD_DEVELOPMENT_QUESTION_TEXT');

		// Get a db connection.
		$db = $this->db;

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get Survey Id
		$query->select($db->quoteName('tm.source'));
		$query->from($db->quoteName('#__tjlms_media', 'tm'));
		$query->join('LEFT', $db->quoteName('#__tjlms_lessons', 'l') . 'ON' . $db->quoteName('l.media_id') . '=' . $db->quoteName('tm.id'));
		$query->where($db->quoteName('l.course_id') . ' = ' . (int) $courseId);
		$query->where($db->quoteName('l.format') . ' = ' . $db->quote('survey'));

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$surveyIds = $db->loadColumn();

		if (!empty($surveyIds))
		{
			foreach ($surveyIds as $surveyId)
			{
				// Create a new query object.
				$query = $db->getQuery(true);

				// Get starts id
				$query->select($db->quoteName('sfus.id'));
				$query->from($db->quoteName('#__survey_force_user_starts', 'sfus'));
				$query->where($db->quoteName('sfus.survey_id') . ' = ' . (int) $surveyId);
				$query->where($db->quoteName('sfus.user_id') . ' = ' . (int) $userId);
				$db->setQuery($query);
				$startIds = $db->loadColumn();

				if (!empty($startIds))
				{
					foreach ($startIds as $startId)
					{
						$questionId = $this->getQuestionId($surveyId, $questionText);

						// Create a new query object.
						$query = $db->getQuery(true);

						// Get order of answer
						$query->select(' DISTINCT ' . $db->quoteName('sf.ordering'));
						$query->from($db->quoteName('#__survey_force_fields', 'sf'));
						$query->join(
						'LEFT', $db->quoteName('#__survey_force_user_answers', 'sa') . 'ON' . $db->quoteName('sa.quest_id') . '=' . $db->quoteName('sf.quest_id')
						);
						$query->join(
						'LEFT', $db->quoteName('#__survey_force_quests', 'sq') . 'ON' . $db->quoteName('sq.sf_survey') . '=' . $db->quoteName('sa.survey_id')
						);
						$query->where($db->quoteName('sa.start_id') . ' = ' . (int) $startId);
						$query->where($db->quoteName('sa.quest_id') . ' = ' . (int) $questionId);
						$query->where($db->quoteName('sf.id') . ' = ' . $db->quoteName('sa.answer'));
						$query->where($db->quoteName('sq.sf_qtext') . ' LIKE ' . $db->quote($db->escape($questionText)));

						$db->setQuery($query);

						return $db->loadColumn();
					}
				}
			}
		}

		return false;
	}

	/**
	 * Function to get Time spent on Course for that year
	 *
	 * @param   INT  $userId      logged in userId
	 *
	 * @param   INT  $courseId    course_id
	 *
	 * @param   INT  $profession  profession
	 *
	 * @return  mixed $result.
	 *
	 * @since 1.0.0
	 */
	public function timeSpentOnCourse($userId, $courseId, $profession)
	{
		// Get a db connection.
		$db = $this->db;

		$cpdDate = $this->getCpdDate($userId);
		$date = Factory::getDate()->Format('Y');

		if (!empty($cpdDate))
		{
			if (strtolower($profession) == 'dentist')
			{
				if ($date >= date("Y", strtotime($cpdDate)) && $date <= date("Y", strtotime($cpdDate)) + 4)
				{
					// Create a new query object.
					$query = $db->getQuery(true);

					$query->select('SEC_TO_TIME(SUM(TIME_TO_SEC(lt.time_spent))) AS timePerYear');
					$query->from($db->quoteName('#__tjlms_lesson_track', 'lt'));
					$query->join('LEFT', $db->quoteName('#__tjlms_lessons', 'l') . 'ON' . $db->quoteName('l.id') . '=' . $db->quoteName('lt.lesson_id'));
					$query->where($db->quoteName('lt.user_id') . ' = ' . (int) $userId);
					$query->where($db->quoteName('l.course_id') . ' = ' . (int) $courseId);
					$query->where('YEAR(' . $db->quoteName('lt.timestart') . ') = YEAR(CURRENT_DATE)');

					// Reset the query using our newly populated query object.
					$db->setQuery($query);

					return $db->loadResult();
				}
			}
			else
			{
				$flagDate = HTMLHelper::date('31 July' . date('Y', strtotime($cpdDate)), Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT_FOR_CALCULATION'), true);
				$tempDate = HTMLHelper::date($cpdDate, Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT_FOR_CALCULATION'), true);

				// If Other than Dentist Profession then Start date set to 1 Aug
				$cycleStartDateForOther = HTMLHelper::date('01 August' . date('Y', strtotime($cpdDate)),
					Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT_FOR_CALCULATION'), true
					);

				// Check for cpd date is less than 31 Aug
				if (strtotime($tempDate) <= strtotime($flagDate))
				{
					$cycleStartDateForOther = HTMLHelper::date('01 August' . date('Y', strtotime($cpdDate)) . '-1 year',
					Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT_FOR_CALCULATION'), true
					);
				}

				// If Other than Dentist Profession then end date set to 31 Aug
				$cycleEndDateForOther = HTMLHelper::date('31 July' . date('Y', strtotime($cycleStartDateForOther)) . '+5 year',
				Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT_FOR_CALCULATION'), true
				);

				$currentDate = HTMLHelper::date('', Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT_FOR_CALCULATION'), true);

				// Check current date is between start date and end date
				if ($currentDate >= $cycleStartDateForOther && $currentDate <= $cycleEndDateForOther)
				{
					$flagDate = HTMLHelper::date('31 July' . date('Y', strtotime($currentDate)), Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT_FOR_CALCULATION'), true);

					$thisYearStartDateForOther = HTMLHelper::date('01 August' . date('Y', strtotime($currentDate)),
						Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT_FOR_CALCULATION'), true
						);

					// Check for current date is less than 31 Aug
					if ($currentDate <= $flagDate)
					{
						$thisYearStartDateForOther = HTMLHelper::date('01 August' . date('Y', strtotime($currentDate)) . '-1 year',
						Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT_FOR_CALCULATION'), true
						);
					}

					$thisYearEndDateForOther = HTMLHelper::date('31 July' . date('Y', strtotime($thisYearStartDateForOther)) . '+1 year',
					Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT_FOR_CALCULATION'), true
					);

					// Create a new query object.
					$query = $db->getQuery(true);

					$query->select('SEC_TO_TIME(SUM(TIME_TO_SEC(lt.time_spent))) AS timePerYear');
					$query->from($db->quoteName('#__tjlms_lesson_track', 'lt'));
					$query->join('LEFT', $db->quoteName('#__tjlms_lessons', 'l') . 'ON' . $db->quoteName('l.id') . '=' . $db->quoteName('lt.lesson_id'));
					$query->where($db->quoteName('lt.user_id') . ' = ' . (int) $userId);
					$query->where($db->quoteName('l.course_id') . ' = ' . (int) $courseId);
					$query->where($db->quoteName('lt.timestart') . ' >= ' . $db->quote($thisYearStartDateForOther));
					$query->where($db->quoteName('lt.timeend') . ' <= ' . $db->quote($thisYearEndDateForOther));

					// Reset the query using our newly populated query object.
					$db->setQuery($query);

					return $db->loadResult();
				}
			}
		}
		elseif (empty($cpdDate))
		{
			$currentDate = HTMLHelper::date('', Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT_FOR_CALCULATION'), true);

			if (strtolower($profession) == 'dentist')
			{
				$cycleStartDateForDentist = HTMLHelper::date('01 January' . date('Y', strtotime($currentDate)),
				Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT_FOR_CALCULATION'), true
				);
				$cycleEndDateForDentist = HTMLHelper::date('31 December' . date('Y', strtotime($currentDate)) . '+4 year',
				Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT_FOR_CALCULATION'), true
				);

				if (date("Y", strtotime($currentDate)) >= date("Y", strtotime($cycleStartDateForDentist))
					&& date("Y", strtotime($currentDate)) <= date("Y", strtotime($cycleEndDateForDentist)))
				{
					// Create a new query object.
					$query = $db->getQuery(true);

					$query->select('SEC_TO_TIME(SUM(TIME_TO_SEC(lt.time_spent))) AS timePerYear');
					$query->from($db->quoteName('#__tjlms_lesson_track', 'lt'));
					$query->join('LEFT', $db->quoteName('#__tjlms_lessons', 'l') . 'ON' . $db->quoteName('l.id') . '=' . $db->quoteName('lt.lesson_id'));
					$query->where($db->quoteName('lt.user_id') . ' = ' . (int) $userId);
					$query->where($db->quoteName('l.course_id') . ' = ' . (int) $courseId);
					$query->where('YEAR(' . $db->quoteName('lt.timestart') . ') = YEAR(CURRENT_DATE)');

					// Reset the query using our newly populated query object.
					$db->setQuery($query);

					return $db->loadResult();
				}
			}
			else
			{
				$flagDate = HTMLHelper::date('31 July' . date('Y', strtotime($currentDate)), Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT_FOR_CALCULATION'), true);

				$cycleStartDateForOther = HTMLHelper::date('01 August' . date('Y', strtotime($currentDate)),
					Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT_FOR_CALCULATION'), true
					);

				// Check for current date is less than 31 Aug
				if ($currentDate <= $flagDate)
				{
					$cycleStartDateForOther = HTMLHelper::date('01 August' . date('Y', strtotime($currentDate)) . '-1 year',
					Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT_FOR_CALCULATION'), true
					);
				}

				$cycleEndDateForOther = HTMLHelper::date('31 July' . date('Y', strtotime($cycleStartDateForOther)) . '+5 year',
				Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT_FOR_CALCULATION'), true
				);

				if ($currentDate >= $cycleStartDateForOther && $currentDate <= $cycleEndDateForOther)
				{
					$thisYearStartDateForOther = HTMLHelper::date('01 August' . date('Y', strtotime($currentDate)),
						Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT_FOR_CALCULATION'), true
						);

					// Check for current date is less than 31 Aug
					if ($currentDate <= $flagDate)
					{
						$thisYearStartDateForOther = HTMLHelper::date('01 August' . date('Y', strtotime($currentDate)) . '-1 year',
						Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT_FOR_CALCULATION'), true
						);
					}

					$thisYearEndDateForOther = HTMLHelper::date('31 July' . date('Y', strtotime($thisYearStartDateForOther)) . '+1 year',
					Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT_FOR_CALCULATION'), true
					);

					// Create a new query object.
					$query = $db->getQuery(true);

					$query->select('SEC_TO_TIME(SUM(TIME_TO_SEC(lt.time_spent))) AS timePerYear');
					$query->from($db->quoteName('#__tjlms_lesson_track', 'lt'));
					$query->join('LEFT', $db->quoteName('#__tjlms_lessons', 'l') . 'ON' . $db->quoteName('l.id') . '=' . $db->quoteName('lt.lesson_id'));
					$query->where($db->quoteName('lt.user_id') . ' = ' . (int) $userId);
					$query->where($db->quoteName('l.course_id') . ' = ' . (int) $courseId);
					$query->where($db->quoteName('lt.timestart') . ' >= ' . $db->quote($thisYearStartDateForOther));
					$query->where($db->quoteName('lt.timeend') . ' <= ' . $db->quote($thisYearEndDateForOther));

					// Reset the query using our newly populated query object.
					$db->setQuery($query);

					return $db->loadResult();
				}
			}
		}
	}

	/**
	 * Function to get Total Time spent on Course for 5 years cycle
	 *
	 * @param   INT     $userId      logged in userId
	 *
	 * @param   INT     $courseId    course_id
	 *
	 * @param   STRING  $profession  profession
	 *
	 * @return  mixed $result.
	 *
	 * @since 1.0.0
	 */
	public function totalFiveYearTimeSpentOnCourse($userId, $courseId, $profession)
	{
		$cpdDate = $this->getCpdDate($userId);

		if (!empty($cpdDate))
		{
			// Get a db connection.
			$db = $this->db;

			// Create a new query object.
			$query = $db->getQuery(true);

			$query->select('SEC_TO_TIME(SUM(TIME_TO_SEC(lt.time_spent))) AS timePerYear');
			$query->from($db->quoteName('#__tjlms_lesson_track', 'lt'));
			$query->join('LEFT', $db->quoteName('#__tjlms_lessons', 'l') . 'ON' . $db->quoteName('l.id') . '=' . $db->quoteName('lt.lesson_id'));
			$query->where($db->quoteName('lt.user_id') . ' = ' . (int) $userId);
			$query->where($db->quoteName('l.course_id') . ' = ' . (int) $courseId);

			if (strtolower($profession) == 'dentist')
			{
				// If Dentist Profession then Start date set to 1 Jan
				$cycleStartDateForDentist = HTMLHelper::date('01 January' . date('Y', strtotime($cpdDate)),
				Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT_FOR_CALCULATION'), true
				);

				// If Dentist Profession then End date set to 31 Dec
				$cycleEndDateForDentist = HTMLHelper::date('31 December' . date('Y', strtotime($cpdDate)) . '+4 year',
				Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT_FOR_CALCULATION'), true
				);

				$query->where('YEAR(' . $db->quoteName('lt.timestart') . ') >= YEAR(' . $db->quote($cycleStartDateForDentist) . ')');
				$query->where('YEAR(' . $db->quoteName('lt.timeend') . ') <= YEAR(' . $db->quote($cycleEndDateForDentist) . ')');
			}
			else
			{
				$flagDate = HTMLHelper::date('31 July' . date('Y', strtotime($cpdDate)), Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT'), true);
				$tempDate = HTMLHelper::date($cpdDate, Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT'), true);

				// If Other than Dentist Profession then Start date set to 1 Aug
				$cycleStartDateForOther = HTMLHelper::date('01 August' . date('Y', strtotime($cpdDate)),
					Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT_FOR_CALCULATION'), true
					);

				// Check for cpd date is less than 31 Aug
				if (strtotime($tempDate) <= strtotime($flagDate))
				{
					$cycleStartDateForOther = HTMLHelper::date('01 August' . date('Y', strtotime($cpdDate)) . '-1 year',
					Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT_FOR_CALCULATION'), true
					);
				}

				// If Other than Dentist Profession then end date set to 1 Aug with after +5 year
				$cycleEndDateForOther = HTMLHelper::date('31 July' . date('Y', strtotime($cycleStartDateForOther)) . '+5 year',
					Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT_FOR_CALCULATION'), true
					);

				$query->where($db->quoteName('lt.timestart') . ' > ' . $db->quote($cycleStartDateForOther));
				$query->where($db->quoteName('lt.timeend') . ' <' . $db->quote($cycleEndDateForOther));
			}

			// Reset the query using our newly populated query object.
			$db->setQuery($query);

			return $db->loadResult();
		}

		return Text::_('COM_TJLMS_COURSE_CERTIFICATE_TIMESET_FIVE_YEAR_CYCLE');
	}

	/**
	 * Function to get Profession
	 *
	 * @param   INT  $userId  userId
	 *
	 * @return  mixed $result.
	 *
	 * @since 1.0.0
	 */
	public function getProfession($userId)
	{
		$db = $this->db;

		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select($db->quoteName('sfd.data'));
		$query->from($db->quoteName('#__social_fields_data', 'sfd'));
		$query->join(
		'LEFT', $db->quoteName('#__social_fields', 'sf') . 'ON' . $db->quoteName('sf.id') . '=' . $db->quoteName('sfd.field_id')
		);
		$query->where($db->quoteName('sfd.uid') . " = " . (int) $userId);
		$query->where($db->quoteName('sf.unique_key') . ' = "DENTAL_PROFESSION"');
		$db->setQuery($query);
		$dataOfProfession = $db->loadResult();

		$decodedData = json_decode($dataOfProfession);
		if (is_array($decodedData))
		{
			$dataOfProfession = '"'.implode('","', $decodedData).'"';
		}
		else
		{
			$dataOfProfession = '';
		}

		$mainquery = $db->getQuery(true);
		$mainquery->select($db->quoteName('sfo.title'));
		$mainquery->from($db->quoteName('#__social_fields_options', 'sfo'));
		$mainquery->join(
						'LEFT', $db->quoteName('#__social_fields', 'sf') . 'ON' . $db->quoteName('sf.id') . '=' . $db->quoteName('sfo.parent_id')
						);
		$mainquery->where($db->quoteName('sfo.value') . " in ( " . $dataOfProfession . ")");
		$mainquery->where($db->quoteName('sf.unique_key') . ' = "DENTAL_PROFESSION"');

		$db->setQuery($mainquery);

		return $db->loadObjectList();
	}

	/**
	 * Function to get Total Time, spent on Course for all years
	 *
	 * @param   INT  $userId    logged in userId
	 *
	 * @param   INT  $courseId  course_id
	 *
	 * @return  mixed $result.
	 *
	 * @since 1.0.0
	 */
	public function totalTimeSpentOnCourse($userId, $courseId)
	{
		// Get a db connection.
		$db = $this->db;

		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select('SEC_TO_TIME(SUM(TIME_TO_SEC(lt.time_spent))) AS timePerYear');
		$query->from($db->quoteName('#__tjlms_lesson_track', 'lt'));
		$query->join('LEFT', $db->quoteName('#__tjlms_lessons', 'l') . 'ON' . $db->quoteName('l.id') . '=' . $db->quoteName('lt.lesson_id'));
		$query->where($db->quoteName('lt.user_id') . ' = ' . (int) $userId);
		$query->where($db->quoteName('l.course_id') . ' = ' . (int) $courseId);

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Function to get Time spent for completing course first time
	 *
	 * @param   INT  $userId    logged in userId
	 *
	 * @param   INT  $courseId  course_id
	 *
	 * @return  mixed $result.
	 *
	 * @since 1.0.0
	 */
	public function timeSpentforCompletingCourse($userId, $courseId)
	{
		// Get a db connection.
		$db = $this->db;

		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select(array('SEC_TO_TIME(SUM( TIME_TO_SEC(lt.time_spent))) AS firstCompleted', $db->quoteName('ct.user_id')));
		$query->from($db->quoteName('#__tjlms_lesson_track', 'lt'));
		$query->join('LEFT', $db->quoteName('#__tjlms_lessons', 'l') . 'ON' . $db->quoteName('l.id') . '=' . $db->quoteName('lt.lesson_id'));
		$query->join('LEFT', $db->quoteName('#__tjlms_course_track', 'ct') . 'ON' . $db->quoteName('ct.course_id') . '=' . $db->quoteName('l.course_id'));
		$query->where($db->quoteName('l.course_id') . ' = ' . (int) $courseId);
		$query->where($db->quoteName('lt.user_id') . ' = ' . (int) $userId);
		$query->where($db->quoteName('lt.attempt') . ' = ' . (int) 1);
		$query->where('(' . $db->quoteName('lt.timestart') . ' BETWEEN ' . $db->quoteName('ct.timestart') . ' AND ' . $db->quoteName('ct.timeend') . ')');
		$query->group($db->quoteName('ct.id'));
		$query->having($db->quoteName('ct.user_id') . " = " . (int) $userId);

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Function to get CPD date
	 *
	 * @param   INT  $userId  logged in userId
	 *
	 * @return  mixed $result.
	 *
	 * @since 1.0.0
	 */
	public function getCpdDate($userId)
	{
		$db = $this->db;

		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select($db->quoteName('sfd.data'));
		$query->from($db->quoteName('#__social_fields_data', 'sfd'));
		$query->join(
						'LEFT', $db->quoteName('#__social_fields', 'sf') . 'ON' . $db->quoteName('sf.id') . '=' . $db->quoteName('sfd.field_id')
						);
		$query->where($db->quoteName('sfd.uid') . " = " . (int) $userId);
		$query->where($db->quoteName('sf.unique_key') . ' = "CPD5YEARCYCLESTARTS"');
		$query->where($db->quoteName('sfd.datakey') . ' = "date"');

		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Function to get Question Id
	 *
	 * @param   INT     $surveyId      surveyId
	 *
	 * @param   STRING  $questionText  questionText
	 *
	 * @return  mixed $result.
	 *
	 * @since 1.0.0
	 */
	public function getQuestionId($surveyId, $questionText)
	{
		$db = $this->db;

		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select($db->quoteName('sq.id'));
		$query->from($db->quoteName('#__survey_force_quests', 'sq'));
		$query->where($db->quoteName('sq.sf_survey') . " = " . (int) $surveyId);
		$query->where($db->quoteName('sq.sf_qtext') . " LIKE " . $db->quote($db->escape($questionText)));

		$db->setQuery($query);

		return $db->loadResult();
	}

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
		// die("onAfterLessonAttemptEnd here");
		// Load TJLMS Lesson Helper
		if (!class_exists('\TjlmsLessonHelper'))
		{
			\JLoader::import('components.com_tjlms.helpers.lesson', JPATH_SITE);
		}

		if (!class_exists('\TjlmsLessonHelper'))
		{
			return;
		}

		$helper = new \TjlmsLessonHelper;
		
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

			if ($status == 'completed' || $status == 'passed')
			{
				Log::add('TJLMS Completion Plugin: Triggering onAfterLessonCompletion for Lesson ID ' . $lesson_id . ', User ID ' . $user_id, Log::INFO, 'tjlms_completion');
				$this->app->triggerEvent('onAfterLessonCompletion', array($lesson_id, $attempt, $user_id));
				
				// Certificate Generation Logic (bypassing consider_marks check in tracking.php)
				try
				{
					// Load TjLms class
					if (!class_exists('\TjLms'))
					{
						if (file_exists(JPATH_ADMINISTRATOR . '/components/com_tjlms/includes/tjlms.php'))
						{
							require_once JPATH_ADMINISTRATOR . '/components/com_tjlms/includes/tjlms.php';
						}
					}
					
					if (class_exists('\TjLms'))
					{
						$courseObj = $helper->getLessonColumn($lesson_id, 'course_id');
						if (!empty($courseObj) && !empty($courseObj->course_id))
						{
							$courseId = $courseObj->course_id;
							$courseData = \TjLms::course($courseId);
							
							if ($courseData && $courseData->certificate_id)
							{
								// Load Helpers and Model
								\JLoader::import('components.com_tjlms.helpers.courses', JPATH_SITE);
								\JLoader::import('components.com_tjlms.models.course', JPATH_SITE);
								
								$tjlmsModelcourse = BaseDatabaseModel::getInstance('Course', 'TjlmsModel', array('ignore_request' => true));
								
								if (class_exists('\TjlmsCoursesHelper') && $tjlmsModelcourse)
								{
									// Term 1: Complete All Lessons
									if ($courseData->certificate_term == 1)
									{
										$tjlmsCoursesHelper = new \TjlmsCoursesHelper;
										$courseProgress = $tjlmsCoursesHelper->getCourseProgress($courseId, $user_id);
										
										if ($courseProgress['status'] == 'C')
										{
											Log::add('TJLMS Completion Plugin: Generating Certificate (Term 1) for Course ' . $courseId, Log::INFO, 'tjlms_completion');
											$tjlmsModelcourse->addCertEntry($courseId, $user_id);
										}
									}
									
									// Term 2: Pass All Lessons
									if ($courseData->certificate_term == 2)
									{
										$courseTrackObj = \TjLms::coursetrack($user_id, $courseId);
										if ($courseTrackObj && method_exists($courseTrackObj, 'checkPassableLessonsPassed'))
										{
											if ($courseTrackObj->checkPassableLessonsPassed())
											{
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
				catch (\Exception $e)
				{
					Log::add('TJLMS Completion Plugin Error: ' . $e->getMessage(), Log::ERROR, 'tjlms_completion');
				}
			}
		}
	}

}


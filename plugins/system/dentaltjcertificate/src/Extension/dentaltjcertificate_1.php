<?php
/**
 * @package    Plg_System_Dentaltjcertificate
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (C) 2009 - 2024 Techjoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Date\Date;

/**
 * Dental tjcertificate Plugin
 *
 * @since  1.0.0
 */

class PlgSystemDentaltjcertificate extends CMSPlugin
{
	/**
	 * Function onBeforeTjlmsTagReplace to tagreplace on certificate with course's custom fields
	 *
	 * @param   object  $course  course object
	 * @param   INT     $userId  User id
	 * @param   Array   &$msg    message
	 *
	 * @return  boolean true or false
	 *
	 * @since  1.0.0
	 */
	public function onBeforeTjlmsTagReplace($courseId, $userId, &$msg)
	{
		$user = ES::user($userId);
		$link = $user->getGoalsPermalink('updateavatar');

		$cpdDate = $this->getCpdDate($userId);
		
		$professionData = $this->getProfession($userId);
		$profession = array();

		foreach ($professionData as $item)
		{
			array_push($profession, $item->title);
		}
		
		$msg->other->profession = implode(', ', $profession);
		
		$msg->other->date = new Date;

		$answers = $this->getSurveyAnswer($userId, $courseId);
		$answers = implode(', ', $answers);
		$options = str_replace("0", "A", $answers);
		$options = str_replace("1", "B", $options);
		$options = str_replace("2", "C", $options);
		$options = str_replace("3", "D", $options);
		$msg->other->cpd_development_outcomes = $options;

		$timeSpendOnCourse = $this->timeSpentOnCourse($userId, $courseId, $msg->other->profession);
		$totalFiveYearTimeSpentOnCourse = $this->totalFiveYearTimeSpentOnCourse($userId, $courseId, $msg->other->profession);
		$totalTimeSpentOnCourse = $this->totalTimeSpentOnCourse($userId, $courseId);
		$timeSpentforCompletingCourse = $this->timeSpentforCompletingCourse($userId, $courseId);

		$msg->other->year_time = (!empty($timeSpendOnCourse)) ? $timeSpendOnCourse : Text::_('COM_TJLMS_COURSE_CERTIFICATE_TIMESET_EMPTY');
		$msg->other->five_year_total_time = (!empty($totalFiveYearTimeSpentOnCourse)) ?
			$totalFiveYearTimeSpentOnCourse : Text::_('COM_TJLMS_COURSE_CERTIFICATE_TIMESET_EMPTY');
		$msg->other->total_time = (!empty($totalTimeSpentOnCourse)) ? $totalTimeSpentOnCourse : Text::_('COM_TJLMS_COURSE_CERTIFICATE_TIMESET_EMPTY');
		$msg->other->time_spent = (!empty($timeSpentforCompletingCourse)) ?
			$timeSpentforCompletingCourse : Text::_('COM_TJLMS_COURSE_CERTIFICATE_TIMESET_EMPTY');

		$msg->other->cycle_start_date = "";

		// Check if user is Dentist profession
		if (strtolower($msg->other->profession) == 'dentist')
		{
			$msg->other->cycle_end_date = Text::sprintf('COM_TJLMS_COURSE_CERTIFICATE_FIVE_YEAR_CYCLE', $link);

			// Check for cpd date is set or not
			if (!empty($cpdDate))
			{
				// CPD Cycle start date set to 1 Jan
				$msg->other->cycle_start_date = HTMLHelper::date('01 January' . date('Y', strtotime($cpdDate)), Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT'), true);

				// CPD Cycle end date set to 31 Dec
				$msg->other->cycle_end_date = HTMLHelper::date('31 December' . date('Y', strtotime($cpdDate)) . '+4 year',
					Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT'), true
					);
			}
		}
		else
		{
			// For other than dentist profession

			// Flag date set as 31 July for comparison
			$flagDate = HTMLHelper::date('31 July' . date('Y', strtotime($cpdDate)), Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT'), true);
			$tempDate = HTMLHelper::date($cpdDate, Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT'), true);

			$msg->other->cycle_end_date = Text::sprintf('COM_TJLMS_COURSE_CERTIFICATE_FIVE_YEAR_CYCLE', $link);

			// Check for cpd date is set
			if (!empty($cpdDate))
			{
				// CPD Cycle start date set to 1 Aug
				$cycleStartDate = HTMLHelper::date('01 August' . date('Y', strtotime($cpdDate)), Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT'), true);

				// Check for cpd date is less than 31 Aug
				if (strtotime($tempDate) <= strtotime($flagDate))
				{
					$cycleStartDate = HTMLHelper::date('01 August' . date('Y', strtotime($cpdDate)) . '-1 year', Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT'), true);
				}

				$msg->other->cycle_start_date = $cycleStartDate;

				// CPD Cycle End date after 5 year
				$msg->other->cycle_end_date = HTMLHelper::date('31 July' . date('Y', strtotime($cycleStartDate)) . '+5 year',
						Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT'), true
					);
			}
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
		$db = Factory::getDbo();

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
		$db = Factory::getDbo();

		$cpdDate = $this->getCpdDate($userId);
		$date = Factory::getDate()->format('Y');

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

				$currentDate = HTMLHelper::date(Factory::getDate(), Text::_('PLG_DENTALTJCERTIFICATE_DATE_FORMAT_FOR_CALCULATION'), true);

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
			$db = Factory::getDbo();

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
		$db = Factory::getDbo();

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

		$dataOfProfession = '"'.implode('","', json_decode($dataOfProfession)).'"';

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
		$db = Factory::getDbo();

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
		$db = Factory::getDbo();

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
		$db = Factory::getDbo();

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
		$db = Factory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		$query->select($db->quoteName('sq.id'));
		$query->from($db->quoteName('#__survey_force_quests', 'sq'));
		$query->where($db->quoteName('sq.sf_survey') . " = " . (int) $surveyId);
		$query->where($db->quoteName('sq.sf_qtext') . " LIKE " . $db->quote($db->escape($questionText)));

		$db->setQuery($query);

		return $db->loadResult();
	}
}

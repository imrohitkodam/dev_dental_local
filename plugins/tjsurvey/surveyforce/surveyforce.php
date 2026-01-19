<?php
/**
 * @package    Shika
 * @author     TechJoomla | <extensions@techjoomla.com>
 * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.folder');
jimport('joomla.plugin.plugin');

$lang = JFactory::getLanguage();
$lang->load('plg_tjsurvey_surveyforce', JPATH_ADMINISTRATOR);

/**
 * Survey plugin from techjoomla
 *
 * @since  1.0.0
 */

class PlgTjsurveySurveyforce extends JPlugin
{
	/**
	 * Plugin that supports uploading and tracking the videos for jWplayer plugin
	 *
	 * @param   string   &$subject  The context of the content being passed to the plugin.
	 * @param   integer  $config    Optional page number. Unused. Defaults to zero.
	 *
	 * @return  void.
	 *
	 * @since 1.0.0
	 */

	public function plgTjsurveySurveyforce(&$subject, $config)
	{
		$trackPath = JPATH_SITE . '/components/com_tjlms/helpers/tracking.php';

		if (file_exists($trackPath))
		{
			require_once $trackPath;
			$this->comtjlmstrackingHelper = new comtjlmstrackingHelper;
		}

		parent::__construct($subject, $config);
	}

	/**
	 * Function to check if the scorm tables has been uploaded while adding lesson
	 *
	 * @param   INT  $lessonId  lessonId
	 * @param   OBJ  $mediaObj  media object
	 *
	 * @return  media object of format and subformat
	 *
	 * @since 1.0.0
	 */
	public function additionalsurveyforceFormatCheck($lessonId, $mediaObj)
	{
		return $mediaObj;
	}

	/**
	 * Function to get Sub Format options when creating / editing lesson format
	 * the name of function should follow standard getSubFormat_<plugin_type>ContentInfo
	 *
	 * @param   ARRAY  $config  config specifying allowed plugins
	 *
	 * @return  object.
	 *
	 * @since 1.0.0
	 */
	public function GetSubFormat_tjsurveyContentInfo($config = array('surveyforce'))
	{
		if (!in_array($this->_name, $config))
		{
			return;
		}

		$obj 			= array();
		$obj['name']	= $this->params->get('plugin_name', 'surveyforce');
		$obj['id']		= $this->_name;

		return $obj;
	}

	/**
	 * Function to get Sub Format HTML when creating / editing lesson format
	 * the name of function should follow standard getSubFormat_<plugin_name>ContentHTML
	 *
	 * @param   INT    $mod_id       id of the module to which lesson belongs
	 * @param   INT    $lesson_id    id of the lesson
	 * @param   MIXED  $lesson       Object of lesson
	 * @param   ARRAY  $comp_params  Params of component
	 *
	 * @return  html
	 *
	 * @since 1.0.0
	 */
	public function GetSubFormat_surveyforceContentHTML($mod_id, $lesson_id, $lesson, $comp_params)
	{
		$result = array();
		$plugin_name = $this->_name;
		$surveylist = $this->getSurveyList();

		// Load the layout & push variables
		ob_start();
		$layout = $this->buildLayoutPath('creator');
		include $layout;
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	/**
	 * Function to render the video
	 *
	 * @param   ARRAY  $config  data to be used to play video
	 *
	 * @return  complete html along with script is return.
	 *
	 * @since 1.0.0
	 */
	public function renderPluginHTML($config)
	{
		$input = JFactory::getApplication()->input;
		$input = JFactory::getApplication()->input;
		$mode = $input->get('mode', '', 'STRING');
		$config['plgtask'] = 'updateData';
		$config['plgtype'] = $this->_type;
		$config['plgname'] = $this->_name;

		$surveyId = $config['lesson_typedata']->source;
		$surveyStartId = $this->getSurveyForceStartsId($surveyId);

		// Load the layout & push variables
		ob_start();
		$layout = $this->buildLayoutPath('default');
		include $layout;
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	/**
	 * function used to save time spent for html content
	 *
	 * @return  void
	 *
	 * @since 1.0.0
	 * */
	public function html_updatedata()
	{
		$input = JFactory::getApplication()->input;
		$post = $input->post;
		$lesson_id = $post->get('lesson_id', '', 'INT');
		$user_id = JFactory::getUser()->id;

		if (!empty ($post->get('time_spent', '', 'FLOAT')))
		{
			$db = JFactory::getDBO();
			$query = $db->getQuery('true');
			$query->select('lt.*');
			$query->from('#__tjlms_lesson_track as lt');
			$query->where('lt.lesson_id = ' . $lesson_id);
			$query->where('lt.user_id = ' . $user_id);
			$query->order('lt.id DESC');
			$db->setQuery($query);

			$usertrack = $db->loadObject();

			if (!empty ($usertrack ))
			{
				$trackObj = new stdClass;
				$trackObj->id = $usertrack->id;
				$trackObj->status = $usertrack->status;
				$trackObj->attempt = $usertrack->attempt;
				$trackObj->time_spent = $post->get('time_spent', '', 'FLOAT');
				$trackingid = $this->comtjlmstrackingHelper->update_lesson_track($lesson_id, $user_id, $trackObj);
			}
		}

		$trackingid = json_encode($trackingid);
		echo $trackingid;
		jexit();
	}

	/**
	 * Internal use functions
	 *
	 * @return  file
	 *
	 * @since 1.0.0
	 */
	public function getSurveyList()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery('true');

		$query->select('a.*');
		$query->from('#__survey_force_survs as a');
		$query->where('a.published = 1');
		$query->order('a.sf_name');
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Launch HTML function
	 *
	 * @param   INT  $eventid  eventid
	 *
	 * @return  file
	 *
	 * @since 1.0.0
	 */
	public function launchHtml($eventid)
	{
	}

	/**
	 * Internal use functions
	 *
	 * @param   STRING  $layout  layout
	 *
	 * @return  file
	 *
	 * @since 1.0.0
	 */
	public function buildLayoutPath($layout)
	{
		$app = JFactory::getApplication();
		$core_file 	= dirname(__FILE__) . '/' . $this->_name . '/tmpl/' . $layout . '.php';
		$override = JPATH_BASE . '/templates/' . $app->getTemplate() . '/html/plugins/' . $this->_type . '/' . $this->_name . '/' . $layout . '.php';

		if (JFile::exists($override))
		{
			return $override;
		}
		else
		{
			return $core_file;
		}
	}

	/**
	 * Internal use functions
	 *
	 * @param   INT  $sid  survey id
	 *
	 * @return  file
	 *
	 * @since 1.0.0
	 */
	public function onAfterSurveyStart($sid)
	{
		$user = JFactory::getUser();
		$jinput	= JFactory::getApplication()->input;
		$cid 	=	$jinput->get('id', 0, 'INT');
		$attempt = 1;
		$flag = 1;
		$lessons = $this->getSurveyLessonsIds($sid);

		if (!empty($lessons))
		{
			foreach ($lessons as $lesson)
			{
				$lessontrack = $this->getLessonTrackData($user->id, $lesson->id);

				if (!empty ($lessontrack))
				{
					if ($lessontrack->lesson_status == 'completed')
					{
						$attempt = $lessontrack->attempt + 1;
					}
					else
					{
						$flag = 0;
					}
				}

				if ($flag == 1)
				{
					/* Update tjlms_lesson_tracking */
					$trackObj   = new stdClass;
					$trackObj->lesson_id		= $lesson->id;
					$trackObj->attempt			= $attempt;
					$trackObj->mode				= '';
					$trackObj->lesson_status	= 'incomplete';
					$trackObj->time_spent		= 0;
					$trackingid = $this->comtjlmstrackingHelper->update_lesson_track($lesson->id, $user->id, $trackObj);
				}
			}
		}
	}

	/**
	 * Internal use functions
	 *
	 * @param   INT  $sid  survey id
	 *
	 * @return  file
	 *
	 * @since 1.0.0
	 */
	public function onAfterSurveyFinish($sid)
	{
		$user = JFactory::getUser();
		$lessons = $this->getSurveyLessonsIds($sid);

		if (!empty($lessons))
		{
			foreach ($lessons as $lesson)
			{
				$lessontrack = $this->getLessonTrackData($user->id, $lesson->id);
				$trackObj   = new stdClass;

				if (!empty($lessontrack))
				{
					$trackObj->id = $lessontrack->id;
					$trackObj->lesson_status = 'completed';
					$trackObj->lesson_id	 = $lesson->id;
					$trackObj->attempt		 = $lessontrack->attempt;
					$trackingid = $this->comtjlmstrackingHelper->update_lesson_track($lesson->id, $user->id, $trackObj);
				}
			}
		}
	}

	/**
	 * Function to get needed data for this API
	 *
	 * @return  id from tjlms_lesson_tracking
	 *
	 * @since 1.0.0
	 */
	public function updateData()
	{
		header('Content-type: application/json');
		$input = JFactory::getApplication()->input;
		$user_id = JFactory::getUser()->id;
		$post = $input->post;
		$sid = $post->get('lesson_id', '', 'INT');

		$trackObj = new stdClass;
		$trackObj->current_position = $post->get('current_position', '', 'INT');
		$trackObj->total_content = $post->get('total_content', '', 'INT');
		$trackObj->time_spent = $post->get('time_spent', '', 'INT');
		$trackObj->attempt = $post->get('attempt', '', 'INT');
		$trackObj->score = 0;
		$trackObj->lesson_status = $post->get('lesson_status', '', 'STRING');
		$lessons = $this->getSurveyLessonsIds($sid);

		if (!empty($lessons))
		{
			foreach ($lessons as $lesson)
			{
				$trackingid = $this->comtjlmstrackingHelper->update_lesson_track($lesson->id, $user_id, $trackObj);
			}
		}
	}

	/**
	 * Function to get lesson track object
	 *
	 * @param   INT    $userId    user_id
	 * @param   ARRAY  $lessonId  lessonId
	 *
	 * @return  lesson track object
	 *
	 * @since 1.0.0
	 */
	public function getLessonTrackData($userId, $lessonId)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery('true');
		$query->select('lt.*');
		$query->from('#__tjlms_lesson_track lt');
		$query->where('lt.lesson_id = ' . $lessonId);
		$query->where('lt.user_id = ' . $userId);
		$query->order('lt.id DESC');
		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Function to get lesson track object
	 *
	 * @param   INT  $surveyId  surveyId
	 *
	 * @return  lesson track object
	 *
	 * @since 1.0.0
	 */
	public function getSurveyForceStartsId($surveyId)
	{
		if (empty($surveyId))
		{
			return 0;
		}

		$db = JFactory::getDBO();
		$query = $db->getQuery('true');
		$query->select('sfus.id');
		$query->from('#__survey_force_user_starts sfus');
		$query->where('sfus.survey_id = ' . (int) $surveyId);
		$query->where('sfus.user_id = ' . (int) JFactory::getUser()->id);
		$query->where('is_complete = 1');
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Function to get media id array
	 *
	 * @param   INT  $sid  Source id
	 *
	 * @return  media id's array
	 *
	 * @since 1.0.0
	 */
	public function getSurveyLessonsIds($sid)
	{
		$db       = JFactory::getDbo();
		$subQuery = $db->getQuery(true);
		$query    = $db->getQuery(true);

		// Create the base subQuery select statement.
		$subQuery->select('m.id')
		->from('`#__tjlms_media` as m')
		->where($db->quoteName('m.source') . ' = ' . (int) $sid)
		->where($db->quoteName('m.format') . ' = "survey"');

		// Create the base select statement.
		$query->select('l.id')
		->from('`#__tjlms_lessons` as l')
		->where($db->quoteName('l.media_id') . ' IN (' . $subQuery . ')');

		// Set the query and load the result.
		$db->setQuery($query);

		return $db->loadObjectList();
	}
}

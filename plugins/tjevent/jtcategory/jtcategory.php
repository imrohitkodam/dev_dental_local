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
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Filesystem\File;

jimport('joomla.filesystem.folder');
jimport('joomla.plugin.plugin');

$lang = Factory::getLanguage();
$lang->load('plg_tjevent_jtcategory', JPATH_ADMINISTRATOR);
require_once JPATH_SITE . '/components/com_tjlms/helpers/courses.php';
require_once JPATH_SITE . '/components/com_tjlms/helpers/media.php';
require_once JPATH_SITE . '/components/com_tjlms/models/enrolment.php';
/**
 * Vimeo plugin from techjoomla
 *
 * @since  1.0.0
 */

class PlgTjeventJtcategory extends CMSPlugin
{
	/**
	 * Check if jticketing is installed and plugin should run
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $canRun = false;

	protected $jtEventHelper = null;

	protected $jtModelorders = null;

	protected $jtMainHelper = null;

	protected $jticketingModelEvents = null;

	protected $userid = 0;

	protected $dateFormat = null;

	protected $items = null;

	protected $escape = 'htmlspecialchars';

	protected $charset = 'UTF-8';

	/**
	 * Plugin that supports online and offline event and tracking them
	 *
	 * @param   string   &$subject  The context of the content being passed to the plugin.
	 * @param   integer  $config    Optional page number. Unused. Defaults to zero.
	 *
	 * @since 1.0.0
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$componentPath = JPATH_SITE . "/components/com_jticketing";

		if (file_exists($componentPath))
		{
			$this->canRun = true;
			require_once JPATH_SITE . "/components/com_jticketing/controllers/orders.php";
			require_once JPATH_SITE . "/components/com_jticketing/helpers/frontendhelper.php";
			require_once JPATH_SITE . "/components/com_jticketing/helpers/main.php";
			require_once JPATH_SITE . "/components/com_jticketing/helpers/event.php";
			require_once JPATH_SITE . "/components/com_jticketing/models/orders.php";
			require_once JPATH_SITE . "/components/com_jticketing/models/events.php";

			$this->jtEventHelper = new JteventHelper;
			$this->jtModelorders = new JticketingModelorders;
			$this->jtMainHelper  = new jticketingmainhelper;
			$this->jticketingModelEvents = new JticketingModelEvents;
		}
	}

	/**
	 * Function to check if the scorm tables has been uploaded while adding lesson
	 *
	 * @param   INT     $lessonId  lessonId
	 * @param   object  $mediaObj  media object
	 *
	 * @return  media object of format and subformat
	 *
	 * @since 1.0.0
	 */
	public function onAdditionaljtcategoryFormatCheck($lessonId, $mediaObj)
	{
		return $mediaObj;
	}

	/**
	 * Function to get Sub Format options when creating / editing lesson format
	 * the name of function should follow standard getSubFormat_<plugin_type>ContentInfo
	 *
	 * @param   ARRAY  $config  config specifying allowed plugins
	 *
	 * @return  array.
	 *
	 * @since 1.0.0
	 */
	public function onGetSubFormat_tjeventContentInfo($config = array('jtcategory'))
	{
		if (!in_array($this->_name, $config))
		{
			return;
		}

		$obj 			= array();
		$obj['name']	= $this->params->get('plugin_name', 'Jtcategory');
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
	 * @return  string
	 *
	 * @since 1.0.0
	 */
	public function onGetSubFormat_jtcategoryContentHTML($mod_id, $lesson_id, $lesson, $comp_params)
	{
		if (!$this->canRun)
		{
			return '<div class="alert-error alert">' . Text::_('PLG_TJEVENT_JTICKETING_NOT_INSTALLED') . '</div>';
		}

		$result      = array();
		$plugin_name = $this->_name;

		$tjlmsCoursesHelper = new TjlmsCoursesHelper;
		$courseDetail  = $tjlmsCoursesHelper->getCourseColumn($lesson->course_id, 'type');
		$courseLessons = $tjlmsCoursesHelper->getSameFormatLessonsByCourse($lesson->course_id, 'event', 'm.source');

		// Load the layout & push variables
		ob_start();
		$layout = PluginHelper::getLayoutPath($this->_type, $this->_name, 'creator');
		include $layout;
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	/**
	 * Function to render the video
	 *
	 * @param   ARRAY  $config    data to be used to play video
	 *
	 * @param   INT    $lessonId  lesson Id
	 *
	 * @return  mixed  string html on success and null on failure.
	 *
	 * @since 1.0.0
	 */
	public function onjtcategoryrenderPluginHTML($config, $lessonId)
	{
		if (!$this->canRun)
		{
			return null;
		}

		$user            = Factory::getUser();
		$this->userid    = $user->id;
		$input           = Factory::getApplication()->input;
		$lesson_typedata = $config['lesson_typedata'];

		$params            = json_decode($lesson_typedata->params);
		$jticketing_params = ComponentHelper::getParams('com_jticketing');
		$this->dateFormat  = $jticketing_params->get('date_format_show');

		BaseDatabaseModel::addIncludePath(JPATH_SITE . '/components/com_jticketing/models');
		$eventsModel = BaseDatabaseModel::getInstance('Events', 'JticketingModel');
		$app = Factory::getApplication();
		$app->setUserState('com_jticketing.events.filter_events_cat', $lesson_typedata->source);
		$app->setUserState('com_jticketing.events.events_to_show', '0');

		$this->items = $eventsModel->getItems();

		require_once JPATH_SITE . '/components/com_tjlms/helpers/lesson.php';
		$tjlmsLessonHelper = new TjlmsLessonHelper;
		$lessonData = $tjlmsLessonHelper->getLessonScorebyAttemptsgrading($lessonId, $this->userid);

		$html = '<script>
				jQuery(window).load(function()
				{
					hideImage();
				});
				</script>';

		ob_start();
		$layout = $this->buildLayoutPath('default');
		include $layout;
		$html .= ob_get_contents();
		ob_end_clean();

		// This may be an iframe directly
		return $html;
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
	 * @return  string
	 *
	 * @since 1.0.0
	 */
	public function buildLayoutPath($layout)
	{
		$app = Factory::getApplication();

		$core_file 	= dirname(__FILE__) . '/tmpl/' . $layout . '.php';
		$override = JPATH_BASE . '/templates/' . $app->getTemplate() . '/html/plugins/' . $this->_type . '/' . $this->_name . '/' . $layout . '.php';

		if (File::exists($override))
		{
			return $override;
		}
		else
		{
			return $core_file;
		}
	}

	/**
	 * Used after the user has checked-in in an Event
	 *
	 * @param   OBJ  $event  event details
	 * @param   INT  $state  publish/unpublish
	 *
	 * @return  boolean|void
	 *
	 * @since 1.0.0
	 */
	public function on_AttendanceEvent($event, $state)
	{
		$jticketingConfig = JT::config();

		if ($jticketingConfig->get('integration') != '2')
		{
			return false;
		}

		// $event array contain event id which is #_jticketing_integration_xref id and we required #_jticketing_events id
		$eventClassObj = JT::event();
		$eventData = $eventClassObj->loadByIntegration($event['eventid']);

		$tjCategory = $this->jtEventHelper->getEventColumn($eventData->id, 'catid');
		$events     = $this->jtEventHelper->getCategorySpecificEvents($tjCategory->catid);
		$lessonCount = 0;

		foreach ($events as $e)
		{
				$db = Factory::getDBO();
				$query = $db->getQuery('true');

				$query->select($db->quoteName('c.id'));
				$query->from($db->quoteName('#__jticketing_checkindetails', 'c'));
				$query->join('LEFT', $db->quoteName('#__jticketing_attendees', 'a') . 'ON(' .
				$db->quoteName('a.id') . '=' . $db->quoteName('c.attendee_id') . ')');
				$query->where($db->quoteName('c.eventid') . '=' . $e->id);
				$query->where($db->quoteName('c.checkin') . '= 1');
				$query->where($db->quoteName('a.owner_id') . '=' . $event['owner_id']);

				$db->setQuery($query);
				$result = $db->loadObject();

				if (!empty($result->id))
				{
					$lessonCount++;
				}
		}

		$db    = Factory::getDBO();
		$query = $db->getQuery('true');

		$query->select(array('l.course_id, l.id'));
		$query->select($db->quoteName(array('m.params')));
		$query->from($db->quoteName('#__tjlms_lessons', 'l'));
		$query->join('INNER', $db->quoteName('#__tjlms_media', 'm') . 'ON(' .
		$db->quoteName('l.media_id') . '=' . $db->quoteName('m.id') . ')');
		$query->where($db->quoteName('m.source') . '=' . $tjCategory->catid);
		$query->where($db->quoteName('m.sub_format') . '= "jtcategory.category"');
		$db->setQuery($query);
		$catResult = $db->loadObject();

		$params = json_decode($catResult->params);

		if ($params->numberOfEvents <= $lessonCount)
		{
			$cat_data = array();
			$cat_data['cat_id']     = $tjCategory->catid;
			$cat_data['spent_time'] = $event['spend_time'];
			$cat_data['completed']  = 0;
			$cat_data['state']      = $state;

			if (!$state)
			{
				return false;
			}

			if ($state == 1)
			{
				$cat_data['completed'] = 1;
			}

			$this->updateLessonTrack($event['owner_id'], $cat_data);
		}
	}

	/**
	 * Used to update the tracking
	 *
	 * @param   INT    $userId  userId
	 * @param   Array  $rs      result of array
	 *
	 * @return  avoid
	 *
	 * @since 1.0.0
	 */
	public function updateLessonTrack($userId, $rs)
	{
		// Find lesson data
		$db           = Factory::getDBO();
		$query        = $db->getQuery(true);
		$query->select('m.id AS media_id');
		$query->from('`#__tjlms_media` AS m');
		$query->where('m.source = ' . $db->quote($rs['cat_id']));
		$query->where('m.sub_format = "jtcategory.category"');
		$db->setQuery($query);
		$mediaData = $db->loadAssocList();

		if (!empty($mediaData))
		{
			foreach ($mediaData as $rsMedia)
			{
				$query        = $db->getQuery(true);
				$query->select('l.id AS lesson_id, l.course_id');
				$query->from('`#__tjlms_lessons` AS l');
				$query->where('l.media_id = ' . $db->quote($rsMedia['media_id']));
				$query->where('l.format = "event"');
				$db->setQuery($query);
				$lessonData = $db->loadAssoc();

				$lesson_status = 'incomplete';

				if ($rs['completed'] == 1)
				{
					$lesson_status = 'completed';
				}

				$trackObj = new stdClass;
				$trackObj->attempt          = 1;
				$trackObj->score            = 0;
				$trackObj->total_content    = '';
				$trackObj->current_position = '';
				$trackObj->lesson_status    = $lesson_status;
				$trackObj->current_position = 0;
				$trackObj->total_content    = 0;

				if (isset($rs['spent_time']) && $rs['spent_time'] != '')
				{
					$trackObj->time_spent = $rs['spent_time'];
				}

				$state = isset($rs['state']) && $rs['state'] == 0 ? $rs['state'] : 1;

				// Delete lesson track when jticketing checkin status is zero

				if ($state == 0 && isset($lessonData['lesson_id']))
				{
					require_once JPATH_SITE . '/components/com_tjlms/helpers/lesson.php';
					$tjlmsLessonHelper = new TjlmsLessonHelper;
					$tjlmsLessonHelper->deleteLessonTracks(array($lessonData['lesson_id']), $userId);
				}
				elseif ($state == 1)
				{
					// Update lesson status
					require_once JPATH_SITE . '/components/com_tjlms/helpers/tracking.php';
					$comtjlmstrackingHelper = new comtjlmstrackingHelper;
					$comtjlmstrackingHelper->update_lesson_track($lessonData['lesson_id'], $userId, $trackObj, 1);
				}
			}
		}

		return;
	}

	/**
	 * Function use to delete event id from params column into enrolled uses table
	 *
	 * @param   STRING  $plug_type  plug_type
	 * @param   INT     $courseId   courseId
	 * @param   INT     $lessonId   lessonId
	 * @param   OBJECT  $mediaObj   mediaId
	 *
	 * @return  boolean  true or false
	 *
	 * @since  1.0.0
	 *
	 */
	public function onBeforeLessonDeletion($plug_type, $courseId, $lessonId, $mediaObj)
	{
		if ($mediaObj->format == 'event')
		{
			$tjlmsModelEnrolment = new TjlmsModelEnrolment;
			$userIds             = $tjlmsModelEnrolment->getCourseEnrolledUsers($courseId);

			$tjlmsmediaHelper   = new TjlmsmediaHelper;
			$mediaDetails       = $tjlmsmediaHelper->getMediaParams($mediaObj->media_id);
			$eventId            = json_decode($mediaDetails->source);
			$deleteEvent        = array($eventId => $eventId);
			$jtModelorders      = new JticketingModelorders;

			foreach ($userIds as $userId)
			{
				$userParams = (array) $tjlmsModelEnrolment->getEnrolledUserParams($courseId, $userId);

				if (!empty($userParams[1]))
				{
					$jtModelorders->update_order_status($userParams[1], 'P');
				}

				$this->updateEnrollParams($courseId, $userId, $deleteEvent, 'delete');
			}
		}

		return true;
	}

	/**
	 * Function used to update enrolled user params with event id and event order id
	 *
	 * @param   INT     $courseId   courseId
	 * @param   INT     $userId     userId
	 * @param   Array   $newParams  updated userParams with event id and event order id
	 * @param   string  $operation  add or delete
	 *
	 * @return  boolean  true or false
	 *
	 * @since  1.0.0
	 *
	 */
	public function updateEnrollParams($courseId, $userId, $newParams, $operation)
	{
		$tjlmsCoursesHelper = new TjlmsCoursesHelper;

		if ($operation == 'add')
		{
			$userParams = json_encode($newParams);
			$tjlmsCoursesHelper->updateCourseEnrolledParams($courseId, $userId, $userParams, 'params');
		}
		elseif ($operation == 'delete')
		{
			$tjlmsModelEnrolment = new TjlmsModelEnrolment;
			$userParams          = (array) $tjlmsModelEnrolment->getEnrolledUserParams($courseId, $userId);
			$userParams          = json_encode(array_diff_key($userParams, $newParams));
			$tjlmsCoursesHelper->updateCourseEnrolledParams($courseId, $userId, $userParams, 'params');
		}
	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * If escaping mechanism is either htmlspecialchars or htmlentities, uses
	 * {@link $_encoding} setting.
	 *
	 * @param   mixed  $var  The output to escape.
	 *
	 * @return  mixed  The escaped value.
	 */
	public function escape($var)
	{
		if (in_array($this->escape, array('htmlspecialchars', 'htmlentities')))
		{
			return call_user_func($this->escape, $var, ENT_COMPAT, $this->charset);
		}

		return call_user_func($this->_escape, $var);
	}
}

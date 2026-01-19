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
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Filesystem\File;

jimport('joomla.filesystem.folder');
jimport('joomla.plugin.plugin');

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;

HTMLHelper::_('stylesheet', 'media/com_tjlms/css/artificiers.css');
$lang = Factory::getLanguage();
$lang->load('plg_tjevent_jtevents', JPATH_ADMINISTRATOR);

$extension = 'com_jticketing';
$base_dir = JPATH_SITE;
$language_tag = 'en-GB';
$reload = true;
$lang->load($extension, $base_dir, $language_tag, $reload);

require_once JPATH_SITE . '/components/com_tjlms/helpers/courses.php';
require_once JPATH_SITE . '/components/com_tjlms/helpers/media.php';
require_once JPATH_SITE . '/components/com_tjlms/models/enrolment.php';

if (ComponentHelper::isEnabled('com_jticketing'))
{
	require_once JPATH_SITE . '/components/com_jticketing/includes/jticketing.php';
}

/**
 * Vimeo plugin from techjoomla
 *
 * @since  1.0.0
 */

class PlgTjeventJtevents extends CMSPlugin
{
	/**
	 * Check if jticketing is installed and plugin should run
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $canRun = false;

	protected $userid = 0;

	protected $jtEventHelper = null;

	protected $jtModelorders = null;

	protected $jtMainHelper = null;

	protected $jticketingModelEvents = null;

	protected $item = null;

	protected $currentTime = null;

	protected $beforeEventStartTime = null;

	protected $showAdobeButton = 0;

	protected $isEventAttended = null;

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

		if (file_exists($componentPath) && ComponentHelper::isEnabled('com_jticketing'))
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
	 * @param   INT  $lessonId  lessonId
	 * @param   OBJ  $mediaObj  media object
	 *
	 * @return  media object of format and subformat
	 *
	 * @since 1.0.0
	 */
	public function onAdditionaljteventsFormatCheck($lessonId, $mediaObj)
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
	public function onGetSubFormat_tjeventContentInfo($config = array('jtevents'))
	{
		if (!in_array($this->_name, $config))
		{
			return;
		}

		$obj 			= array();
		$obj['name']	= $this->params->get('plugin_name', 'Jtevents');
		$obj['id']		= $this->_name;
		$obj['assessment'] = $this->params->get('assessment', '0');

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
	public function onGetSubFormat_jteventsContentHTML($mod_id, $lesson_id, $lesson, $comp_params)
	{
		if (!$this->canRun)
		{
			return '<div class="alert-error alert">' . Text::_('PLG_TJEVENT_JTICKETING_NOT_INSTALLED') . '</div>';
		}

		$result          = array();
		$plugin_name     = $this->_name;
		$getEventDetails = $this->getEventDetails();

		$tjlmsCoursesHelper = new TjlmsCoursesHelper;
		$courseDetail       = $tjlmsCoursesHelper->getCourseColumn($lesson->course_id, 'type');
		$courseLessons      = $tjlmsCoursesHelper->getSameFormatLessonsByCourse($lesson->course_id, 'event', 'm.source');
		$eventlist          = $this->getEventList($courseLessons);

		// Load the layout & push variables
		ob_start();
		$layout = PluginHelper::getLayoutPath($this->_type, $this->_name, 'creator');
		include $layout;
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	/**
	 * Function to get HTML to be shown insted on LAUNCH button
	 *
	 * @param   OBJ  $lesson  lesson object
	 *
	 * @return  mixed string on success and null on failure
	 *
	 * @since 1.0.0
	 */
	public function onGetjteventsLaunchButtonHtml($lesson)
	{
		if (!$this->canRun)
		{
			return null;
		}

		$tjlmsmediaHelper = new TjlmsmediaHelper;
		$mediaDetails     = $tjlmsmediaHelper->getMediaParams($lesson->media_id);
		require_once JPATH_SITE . "/components/com_tjlms/helpers/main.php";
		$result           = $this->jtEventHelper->getEventColumn($mediaDetails->source, 'id');

		if (empty($result))
		{
			$hiddenText = Text::_("PLG_TJEVENT_HIDDEN_LAUNCH");
			$span = '<i rel="popover" class="icon-lock" ></i><span class="lesson_attempt_action">' . Text::_("PLG_TJEVENT_LAUNCH") . '</span>';
			$return['html'] = '<button rel="popover" data-original-content=" ' . $hiddenText . '" class="btn btn-small btn-disabled">' . $span . ' </button>';
			$return['supress_lms_launch'] = 1;

			return $return;
		}

		$user            = Factory::getUser();
		$this->userid    = $user->id;
		$uri             = Uri::getInstance();
		$pageURL         = $uri->toString();
		$redirectionUrl  = base64_encode($pageURL);

		$eventDetails   = $this->jticketingModelEvents->getTJEventDetails($mediaDetails->source, $redirectionUrl);
		$eventHTML      = '';
		$tjlmsparams    = ComponentHelper::getParams('com_tjlms');
		$launchLesson   = $tjlmsparams->get('launch_full_screen');
		$tjlmshelperObj = new comtjlmsHelper;
		$today          = Factory::getDate()->toSql();
		$event          = $this->jtMainHelper->getAllEventDetails($mediaDetails->source);
		$isEventInclude = json_decode($mediaDetails->params);

		// Load the layout & push variables
		ob_start();
		$layout = $this->buildLayoutPath('launch');
		include $layout;
		$html = ob_get_contents();
		ob_end_clean();

		$return['html'] = $html;

		if ($this->params->get('detail_page') == 1 && $eventDetails['isboughtEvent'] == 1
			&& ($today < $eventDetails['enddate']) && $event->online_events == 1)
		{
			$return['supress_lms_launch'] = 1;
		}
		else
		{
			$return['supress_lms_launch'] = 0;
		}

		return $return;
	}

	/**
	 * Function to render the video
	 *
	 * @param   ARRAY  $config    data to be used to play video
	 * @param   INT    $lessonId  lesson id
	 *
	 * @return  mixed string on success and null on failure
	 *
	 * @since 1.0.0
	 */
	public function onjteventsrenderPluginHTML($config, $lessonId)
	{
		if (!$this->canRun)
		{
			return null;
		}

		$user            = Factory::getUser();
		$this->userid    = $user->id;
		$input           = Factory::getApplication()->input;
		$lesson_typedata = $config['lesson_typedata'];
		$uri             = Uri::getInstance();
		$pageURL         = $uri->toString();
		$redirectionUrl  = base64_encode($pageURL);

		$eventData = $this->jticketingModelEvents->getTJEventDetails($lesson_typedata->source, $redirectionUrl);

		$html = '<script>
				jQuery(window).load(function()
				{
					hideImage();
				});
				</script>';

		$this->item        = new stdClass;
		$event             = $this->jtMainHelper->getAllEventDetails($lesson_typedata->source);
		$eventInfo         = JT::event($event->id);
		$isFreeEvent       = $this->jtMainHelper->isFreeEvent($event->id);

		$this->currentTime = Factory::getDate()->toSql();
		$plugin            = PluginHelper::getPlugin('tjevents', 'plug_tjevents_adobeconnect');
		$params            = new Registry($plugin->params);

		$this->beforeEventStartTime = $params->get('show_em_btn', '5');
		$this->showAdobeButton      = 0;

		if ($event->online_events == 1)
		{
			$time     = strtotime($event->startdate);
			$time     = $time - ($this->beforeEventStartTime * 60);
			$current  = strtotime($this->currentTime);
			$date     = date("Y-m-d H:i:s", $time);
			$datetime = strtotime($date);

			if ($event->created_by == $this->userid)
			{
				$eventData['isboughtEvent'] = 1;
			}

			if ($datetime < $current  or $this->userid == $event->created_by)
			{
				$this->showAdobeButton = 1;
			}
		}

		$this->item->venue = $event->venue;
		$this->item->id    = $event->ordering;

		require_once JPATH_SITE . '/components/com_tjlms/helpers/lesson.php';
		$tjlmsLessonHelper = new TjlmsLessonHelper;
		$lessonData = $tjlmsLessonHelper->getLessonScorebyAttemptsgrading($lessonId, $this->userid);

		ob_start();
		$layout = $this->buildLayoutPath('default');
		include $layout;
		$html .= ob_get_contents();
		ob_end_clean();

		// This may be an iframe directly
		return $html;
	}

	/**
	 * getEventList to use get events
	 *
	 * @param   ARRAY  $lessons  event_id
	 *
	 * @return  file
	 *
	 * @since 1.0.0
	 */
	public function getEventList($lessons)
	{
		$now = Factory::getDate()->toSql();
		$db = Factory::getDBO();
		$query = $db->getQuery('true');

		$query->select('a.*');
		$query->from('#__jticketing_events as a');
		$query->where('a.state = 1');

		if (!empty($lessons))
		{
			$query->where("a.id NOT IN(" . implode(',', $lessons) . ")");
		}

		$query->where($db->quoteName('a.enddate') . " >= " . $db->quote($now));
		$query->order('online_events desc');
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Internal use functions
	 *
	 * @param   string  $event_id  TO ADD
	 * @param   string  $col       TO ADD
	 *
	 * @return  file
	 *
	 * @since 1.0.0
	 */
	public function getEventDetails($event_id='', $col='')
	{
		$db = Factory::getDBO();
		$query = $db->getQuery('true');

		if ($col)
		{
			$query->select($col);
		}
		else
		{
			$query->select('*');
		}

		$query->from('#__jticketing_events');

		if ($event_id)
		{
			$query->where('id = ' . $event_id);
		}

		$db->setQuery($query);

		return $db->loadObject();
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
	 * @param   array  $event  event details
	 * @param   INT    $state  publish/unpublish
	 *
	 * @return  void|boolean
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

		if (!empty($event))
		{
			$event_data = array();
			$event_data['event_id']   = $eventData->id;
			$event_data['spent_time'] = $event['spent_time'];
			$event_data['completed']  = 0;
			$event_data['state']      = $state;

			if ($state == 1)
			{
				$event_data['completed'] = 1;
			}

			$this->updateLessonTrack($event['owner_id'], $event_data);
		}
	}

	/**
	 * Used to update the tracking
	 *
	 * @param   INT    $userId  userId
	 * @param   Array  $rs      result of array
	 *
	 * @return  void|boolean
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
		$query->where('m.source = ' . $db->quote($rs['event_id']));
		$query->where('m.sub_format = "jtevents.event"');
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
					$plugin = PluginHelper::getPlugin('tjevent', 'jtevents');
					$params = new Registry($plugin->params);

					$asssessment = $params->get('assessment', 1);

					if ($asssessment)
					{
						require_once JPATH_ROOT . '/components/com_tjlms/models/assessments.php';
						$assessmentModel = new TjlmsModelAssessments;
						$lessonAssessment = $assessmentModel->getLessonAssessments($mediaData['lesson_id']);

						if ($lessonAssessment)
						{
							$lesson_status = 'AP';
						}
						else
						{
							$lesson_status = 'completed';
						}
					}
				}

				$trackObj = new stdClass;
				$trackObj = (object) array_merge((array) $trackObj, $rs);
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
					$trackingid = $comtjlmstrackingHelper->update_lesson_track($lessonData['lesson_id'], $userId, $trackObj, 0);
				}
			}
		}

		return;
	}

	/**
	 * Used to create jticketing orders and call after order status change
	 *
	 * @param   INT  $data  data
	 *
	 * @return  void|boolean
	 *
	 * @since 1.0.0
	 */
	public function onAfterOrderCreation($data)
	{
		if (empty($data['user_id']))
		{
			$userId = $user = Factory::getUser()->id;
		}
		else
		{
			$userId = $data['user_id'];
		}

		$db = Factory::getDBO();
		$query = $db->getQuery('true');

		$query->select($db->quoteName('l.course_id'));
		$query->from($db->quoteName('#__tjlms_lessons', 'l'));
		$query->join('LEFT', $db->quoteName('#__tjlms_media', 'm') . 'ON(' .
		$db->quoteName('l.media_id') . '=' . $db->quoteName('m.id') . ')');
		$query->where($db->quoteName('m.source') . '=' . $data['eventid']);
		$query->where($db->quoteName('m.sub_format') . '= "jtevents.event"');
		$db->setQuery($query);
		$result = $db->loadObject();

		$userParams = array();
		$userParams[$data['eventid']] = $data['order_id'];

		// Add new entry for new created lesson into enrolled user params column
		$this->updateEnrollParams($result->course_id, $userId, $userParams, 'add');

		return $data['order_id'];
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
			$tjlmsCoursesHelper = new TjlmsCoursesHelper;
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
	 * @param   INT    $courseId   courseId
	 * @param   INT    $userId     userId
	 * @param   Array  $newParams  updated userParams with event id and event order id
	 * @param   INT    $operation  add or delete
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
			$userParams = json_encode($newParams, true);
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
}

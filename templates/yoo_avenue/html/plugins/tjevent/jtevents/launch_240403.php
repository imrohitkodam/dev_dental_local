<?php
/**
 * @package    Tjlms
 * @copyright  Copyright (C) 2005 - 2018. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.modal', 'a.modal');
$document = JFactory::getDocument();
$document->addscript(JUri::root(true) . '/media/com_jticketing/vendors/js/jquery.countdown.min.js');
$document->addscript(JUri::root(true) . '/media/com_jticketing/js/jticketing.js');
$document->addscript(JUri::root(true) . '/media/com_tjlms/js/tjlms.js');
$document->addStyleSheet(JUri::root() . 'media/com_jticketing/css/artificiers.css');

$input 		= Factory::getApplication()->input;

// Give access to enter into online event
// $plugin = JPluginHelper::getPlugin('tjevents', 'plug_tjevents_adobeconnect');
// $params = new JRegistry($plugin->params);

$this->beforeEventStartTime = JComponentHelper::getParams('com_jticketing')->get('show_em_btn', 5);
$this->showAdobeButton = 0;
$currentDate = JFactory::getDate()->toSql();

// Give access to enter into online event
// $plugin = JPluginHelper::getPlugin('tjevents', 'plug_tjevents_adobeconnect');
// $pluginParams = new JRegistry($plugin->params);
$accessToEvent = $this->beforeEventStartTime;

JLoader::register('JticketingCommonHelper', JPATH_SITE . '/components/com_jticketing/helpers/common.php');

JticketingCommonHelper::getLanguageConstant();

JText::script('PLG_TJEVENT_JTEVENTS_ENROLMENT_FAILURE');


if ($event->online_events == 1)
{
	$time     = strtotime($event->startdate);
	$time     = $time - ($this->beforeEventStartTime * 60);
	$current  = strtotime($today);
	$date     = date("Y-m-d H:i:s", $time);
	$datetime = strtotime($date);

	if ($event->created_by == $this->userid)
	{
		$eventDetails['isboughtEvent'] = 1;
	}

	if ($datetime < $current  or $this->userid == $event->created_by)
	{
		$this->showAdobeButton = 1;
	}
}

$disabled = $lock_icon = '';
$active_btn_class = 'btn-small btn-primary tjlms-btn-flat';

JLoader::import('course', JPATH_SITE . '/components/com_tjlms/models');
$courseModel = new TjlmsModelcourse;
$course_id = $input->get('id', '', 'INT');
$course_info = $courseModel->getcourseinfo($course_id);
$checkifuserenroled = $courseModel->checkifuserenroled($course_id, $this->userid, $course_info->type);

JLoader::import('enrollment', JPATH_SITE . '/components/com_jticketing/models');
$enrollmentModel = new JticketingModelEnrollment;
$isEnrolled = $enrollmentModel->isAlreadyEnrolled($mediaDetails->source, $this->userid);

JLoader::import('lesson', JPATH_SITE . '/components/com_tjlms/models');
$lessonModel = BaseDatabaseModel::getInstance('lesson', 'TjlmsModel');
$usercanAccess = $lessonModel->canUserLaunchFromCourse($course_info, $lesson, $this->userid);

$active_btn_class = 'btn-small btn-primary';

if(!empty($checkifuserenroled) || $usercanAccess['access'] == 1)
{
	$active_btn_class = 'btn-small btn-primary btn-enabled';
}
else
{
	$active_btn_class = 'btn-small btn-disabled bg-grey';
	$lock_icon="<i class='fa fa-lock' aria-hidden='true'></i>";
}

$lesson_url = $tjlmshelperObj->tjlmsRoute("index.php?option=com_tjlms&view=lesson&lesson_id=" . $lesson->id . "&tmpl=component&lessonscreen=1", false);
$lesson_url = addslashes(htmlspecialchars($lesson_url));

?>
<script type="text/javascript" src="<?php echo Juri::root() . '/plugins/tjevent/' .
$this->_name . '/' . $this->_name . '/assets/js/default.js?v=1234';?>"></script>

<link rel="stylesheet" type="text/css"  href="<?php echo JUri::root(true) . '/plugins/tjevent/'
. $this->_name . '/' . $this->_name . '/assets/css/jtevents.css';?>"></link>

<div class="event-count pl-15" id="event-countdown<?php echo $mediaDetails->source; ?>"></div>

<?php
if ($eventDetails['isboughtEvent'] == '' && !empty($eventDetails['enrol_button']) || !empty($eventDetails['buy_button_link']))
{
		$isEventExpired = (strtotime($event->booking_end_date) - strtotime($currentDate));

		if (($isEventExpired > 0) && !empty($eventDetails['buy_button_link']) && $eventDetails['isboughtEvent'] == '')
		{
			?>
			<a class="btn btn-success"
			href="<?php echo htmlspecialchars($eventDetails['buy_button_link']);?>">
				<?php echo JText::_("PLG_TJEVENT_BUY");?>
			</a>
			<?php
		}
		elseif(($isEventExpired > 0) && !empty($eventDetails['buy_button_link']) && $eventDetails['isboughtEvent'] == '1')
		{
			?>

			<span class="tool-tip" data-toggle="tooltip" data-placement="top"
			title="<?php echo JText::_('PLG_JTEVENT_ONLINE_EVENT_ALREADY_BOUGHT');?>">
				<a class="btn btn-success"
				href="<?php echo htmlspecialchars($eventDetails['buy_button_link']);?>">
					<?php echo JText::_("PLG_TJEVENT_BUY");?>
				</a>
			</span>
			<?php
		}

		if (($isEventExpired > 0) && $eventDetails['isboughtEvent'] == '' && empty($isFreeEvent)
			&& !empty($isFreeEvent) || !empty($eventDetails['enrol_button']))
		{
			?>
			<button type="button" class="br-0 btn <?php echo $active_btn_class; ?>"
			id = "enroll_button<?php echo $mediaDetails->source; ?>"
			data-loading-text="<i class='fa fa-spinner fa-spin '></i>Loading.."><?php echo $lock_icon; ?>
			<?php echo JText::_("PLG_TJEVENT_LAUNCH");?>
			<span class="glyphicon glyphicon-play <?php echo ($lock_icon) ? 'hidden' : 'visible-sm visible-xs';?>" aria-hidden="true"></span>
			</button>
		<?php
		}
}
elseif (!empty($eventDetails['enrolled_button']) && $event->online_events == 0)
{
	echo $eventDetails['enrolled_button'];
}
elseif (!empty($eventDetails['enroll_pending_button']))
{
	echo $eventDetails['enroll_pending_button'];
}
elseif (!empty($eventDetails['enroll_cancel_button']))
{
	echo $eventDetails['enroll_cancel_button'];
}
elseif (!empty($eventDetails['waitinglist_button']))
{
	echo $eventDetails['waitinglist_button'];
}

elseif (!empty($eventDetails['waitlisted_button']))
{
	echo $eventDetails['waitlisted_button'];
}
else
{
	if ($event->online_events == 1 && $eventDetails['isboughtEvent'] == 1 && $event->enddate > $today && $this->params->get('detail_page') == 1)
	{
		if ($this->showAdobeButton == 1)
		{
		?>
			<button type="button" class="btn btn-info enable meet-button"
			id="jt-enterMeeting<?php echo $mediaDetails->source; ?>"
			data-loading-text="<i class='fa fa-spinner fa-spin '></i> Loading..">
			<?php echo JText::_('PLG_TJEVENT_LAUNCH');?>
			</button>
		<?php
		}
		elseif ($this->showAdobeButton == 0)
		{
			?>
			<span class="tool-tip" data-toggle="tooltip" data-placement="top"
			title="<?php echo JText::sprintf('COM_JT_MEETING_ACCESS', $this->beforeEventStartTime);?>">
				<button class="btn btn-info com_jticketing_button" disabled="disabled"><?php echo JText::_('PLG_TJEVENT_LAUNCH');?></button>
			</span>
		<?php
		}
	}
}

$currentDate = JHtml::date($currentDate, 'Y-m-d H:i:s');
$startDate = JHtml::date($event->startdate, 'Y-m-d H:i:s');
$endDate = JHtml::date($event->enddate, 'Y-m-d H:i:s');
$cDate = JFactory::getDate()->toSql();
?>
<script>
	var event_till = "<?php echo strtotime($event->startdate) - strtotime($cDate); ?>";
	var event_count_till = "<?php echo strtotime($event->enddate) - strtotime($cDate); ?>";
	var accessToEvent = "<?php echo $accessToEvent;?>";
	var eventId = "<?php echo (int) $event->id; ?>";
	var currentDate ="<?php echo $currentDate;?>";
	var startDate = "<?php echo $startDate;?>";
	var endDate = "<?php echo $endDate;?>";
	
	var source = "<?php echo (int) $mediaDetails->source; ?>";
	var jticketing_baseurl = "<?php echo JUri::root();?>";
	var recording_error = "<?php echo JText::_('COM_JTICKETING_NO_RECORDING_FOUND');?>";
	var recording_name = "<?php echo JText::_('COM_JTICKETING_RECORDING_NAME');?>";
	var cid = "<?php echo (int) $this->userid;?>";
	var launch_lesson_full_screen = "<?php echo $launchLesson;?>";
	var checkifuserenroled = "<?php echo $checkifuserenroled;?>";
	var creator = "<?php echo $event->created_by;?>";
	var usercanAccess = "<?php echo $usercanAccess['access'];?>";
	
	jQuery(document).on("click", "#enroll_button<?php echo $mediaDetails->source; ?>,#jt-enterMeeting<?php echo $mediaDetails->source; ?>", function(){
		if(checkifuserenroled == 1 || cid == creator || usercanAccess == 1)
		{
			tjevent.event.enrolment(this, <?php echo $mediaDetails->source; ?>, cid, "<?php echo $lesson_url;?>", "<?php echo $isEnrolled;?>");
		}
		else
		{
			return false;
		}
	});

	jQuery(document).on("click", "#jt-meetingRecording<?php echo $mediaDetails->source; ?>", function(){
		jtSite.event.meetingRecordingUrl(this, "<?php echo $mediaDetails->source; ?>");
	});

	jtCounter.jtCountDown("event-countdown<?php echo $mediaDetails->source; ?>", startDate, endDate, currentDate);
</script>

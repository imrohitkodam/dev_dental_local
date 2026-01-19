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

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;

JLoader::register('JticketingCommonHelper', JPATH_SITE . '/components/com_jticketing/helpers/common.php');

JticketingCommonHelper::getLanguageConstant();

if (empty($event->avatar))
{
	$imagePath = Route::_(Uri::base() . 'media/com_jticketing/images/default-event-image.png', false);
}
else
{
	$imagePath = $event->avatar;
}

$this->userid     = Factory::getUser()->id;
$ticketTypes      = $this->jtMainHelper->getEventDetails($event->id);
$jtFrontendHelper = new Jticketingfrontendhelper;

/* If venue address is not empty then get venue address otherwise load event location address*/
if (!empty($event->venue))
{
	$venue = $jtFrontendHelper->getVenue($event->venue);
}

// Event latitude & longitude
$lat = !empty($event->latitude)?$event->latitude:0;
$long = !empty($event->longitude)?$event->longitude:0;

$eventAddress = empty($venue) ? $event->location: $venue->name;
$venueLocation = !empty($eventAddress) ? $eventAddress : $event->venue;

$document = Factory::getDocument();
$document->addScript(Uri::root() . 'components/com_tmt/assets/js/jquery.countdown.js');
$document->addscript(Uri::root(true) . '/media/com_jticketing/js/init.js');
$document->addscript(Uri::root(true) . '/media/com_jticketing/js/ajax.js');
$document->addscript(Uri::root(true) . '/media/com_jticketing/js/jticketing.js');
$document->addStyleSheet(Uri::root() . 'media/com_jticketing/css/artificiers.css');

// Give access to enter into online event
$plugin        = PluginHelper::getPlugin('tjevents', 'plug_tjevents_adobeconnect');
$pluginParams  = new Registry($plugin->params);
$accessToEvent = $pluginParams->get('show_em_btn', 1);
$jtparams      = ComponentHelper::getParams('com_jticketing');

JLoader::import('enrollment', JPATH_SITE . '/components/com_jticketing/models');
$enrollmentModel = new JticketingModelEnrollment;

$isEnrolled = $enrollmentModel->isAlreadyEnrolled($event->id, $this->userid);

?>

<link rel="stylesheet" type="text/css"  href="<?php echo Uri::root(true) . '/plugins/tjevent/' .
$this->_name . '/' . $this->_name . '/assets/css/jtevents.css';?>"></link>

<div class="tjlms-wrapper">
	<?php if ($this->showAdobeButton == 1): ?>
			<div class="center alert alert-info text-center event_con" >
				<h2><?php echo JText::_("PLG_TJEVENT_JTEVENTS_WAITING_MESSAGE");?></h2>
				<h3><?php echo JText::_("PLG_TJEVENT_JTEVENTS_ADOBE_CONNECT_FAILURE");?></h3>
			</div>
		<?php else: ?>
			<div class="center alert alert-info text-center event_con" >
				<h2><?php echo JText::sprintf("PLG_TJEVENT_JTEVENTS_WAITING_MESSAGE_WARNING", $this->beforeEventStartTime);?></h2>
				<h3><?php echo JText::_("PLG_TJEVENT_JTEVENTS_ADOBE_CONNECT_FAILURE");?></h3>
			</div>
		<?php endif ?>
	<?php
	$currentDate  = Factory::getDate()->toSql();
	$isEventOwner = $event->created_by == Factory::getUser()->id && $currentDate > $event->enddate;

	if ($lessonData->lesson_status == 'completed' || $isEventOwner)
	{
		?>
		<div class="center alert alert-success text-center event_con">
			<i class="fa fa-thumbs-o-up" aria-hidden="true"></i>
			<span class="center"><?php echo Text::_("PLG_TJEVENT_EVENT_THANK_YOU");?></span>
		</div>
		<?php
	}
	elseif ($currentDate > $event->enddate && $event->online_events == 0)
	{
		?>
		<div class="center alert alert-danger text-center event_con">
			<span class="center"><?php echo Text::_("PLG_TJEVENT_EVENT_MISSED_EVENT");?></span>
		</div>
		<?php
	}
	else
	{
		?>
		<div class="center alert alert-info text-center event_con" >
			<?php echo Text::_('PLG_JTEVENTS_LESSON_COMPLETE_PREQUISITES');?>
		</div>
		<?php
	}
	?>
	<div class="tab-content" id="myTabContent">
		<div id="details" class="tab-pane active jt_event">
				<hr>
				<div class="row">
					<div class="col-xs-12 col-sm-12">
					<!---Map-->
					<?php
						if ($event->online_events != 1)
						{
							$address = $event->venue > 0 ? $eventAddress : $event->location;
						?>
						<div id="jticketing-event-map" class="responsive-embed responsive-embed-21by9">
							<div id="evnetGoogleMapLocation">
								<iframe width="100%" src="https://www.google.com/maps/embed/v1/place?key=<?php echo $jtparams->get('google_map_api_key'); ?>&q=<?php echo($address); ?>" allowfullscreen>
								</iframe>
							</div>
						</div>
						<?php
						}
						?>
					<!---Map end-->
					</div>
					<div class="col-xs-12 col-sm-4 margint20">

					</div>
				</div>



				<div class="col-lg-5 col-md-5 col-sm-6 col-xs-12 span5">
						<?php
					if ($event->online_events == 1)
					{
						?>
						<div class="modal fade" id="recordingUrl" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										<h4 class="modal-title" id="myModalLabel"><?php echo Text::_('COM_JTICKETING_RECORDING_LIST');?></h4>
									</div>
									<div class="modal-body" id="recordingContent">

									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Text::_('COM_JTICKETING_MODAL_CLOSE');?></button>
									</div>
								</div>
							</div>
						</div>
						<?php
					}
						?>
				</div>
		</div>
	</div>
</div>

<?php
$date        = Factory::getDate();
$currentDate = HTMLHelper::date($date, 'Y-m-d H:i:s');
$startDate   = HTMLHelper::date($event->startdate, 'Y-m-d H:i:s');
$endDate     = HTMLHelper::date($event->enddate, 'Y-m-d H:i:s');

?>

<script type="text/javascript" src="<?php echo Uri::root() . '/plugins/tjevent/' .
$this->_name . '/' . $this->_name . '/assets/js/default.js?v=1234';?>"></script>
<script>
	var event_till         = "<?php echo strtotime($event->startdate) - strtotime($currentDate); ?>"
	var event_count_till   = "<?php echo strtotime($event->enddate) - strtotime($currentDate); ?>";
	var accessToEvent      = "<?php echo $accessToEvent;?>";
	var baseurl            = "<?php echo Uri::root();?>";
	var eventId            = "<?php echo (int) $event->id;?>";
	var onlineEvent        = "<?php echo $event->online_events;?>";
	var jticketing_baseurl = "<?php echo Uri::root();?>";
	var recording_error    = "<?php echo Text::_('COM_JTICKETING_NO_RECORDING_FOUND');?>";
	var recording_name     = "<?php echo Text::_('COM_JTICKETING_RECORDING_NAME');?>";
	var event_id           = "<?php echo $event->id;?>";
	var currentDate        ="<?php echo $currentDate;?>";
	var startDate          = "<?php echo $startDate;?>";
	var endDate            = "<?php echo $endDate;?>";
	var onlineEvent        = "<?php echo $event->online_events;?>";
	
	var isboughtEvent = "<?php echo $eventData['isboughtEvent'];?>";
	var enddate = "<?php echo $event->enddate;?>";
	var startdate = "<?php echo $event->startdate;?>";
	var currentTime = "<?php echo $this->currentTime;?>";
	var showAdobeButton = "<?php echo $this->showAdobeButton;?>";
	var source = "<?php echo (int) $event->id;?>";
	var isEnrolled = "<?php echo $isEnrolled;?>";

	jQuery(document).on("click", "#jt-enterMeeting<?php echo $event->id; ?>", function(){
		tjevent.event.onlineMeetingUrl("<?php echo $event->id; ?>");
	});

	jQuery(document).on("click", "#jt-meetingRecording<?php echo $event->id; ?>", function(){
		jtSite.event.meetingRecordingUrl(this, "<?php echo $event->id; ?>");
	});

	jQuery(document).ready(function() {
		if(isEnrolled != '' || showAdobeButton == 1 )
		{
			if (onlineEvent == 1 && isboughtEvent == 1 && enddate > currentTime)
			{
					tjevent.event.onlineMeetingUrl(eventId);
			}
		}
	});

	jtSite.event.initEventDetailJs();

</script>

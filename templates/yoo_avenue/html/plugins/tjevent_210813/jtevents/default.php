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

JLoader::register('JticketingCommonHelper', JPATH_SITE . '/components/com_jticketing/helpers/common.php');

JticketingCommonHelper::getLanguageConstant();

if (empty($event->avatar))
{
	$imagePath = JRoute::_(JUri::base() . 'media/com_jticketing/images/default-event-image.png', false);
}
else
{
	$imagePath = $event->avatar;
}

$this->userid     = JFactory::getUser()->id;
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

$document = JFactory::getDocument();
$document->addScript(JUri::root() . 'components/com_tmt/assets/js/jquery.countdown.js');

$document->addscript(JUri::root(true) . '/media/com_jticketing/js/jticketing.js');

$document->addscript(JUri::root(true) . '/media/com_jticketing/js/googlemap.js');

$document->addStyleSheet(JUri::root() . 'media/com_jticketing/css/artificiers.css');

// Give access to enter into online event
$plugin = JPluginHelper::getPlugin('tjevents', 'plug_tjevents_adobeconnect');
$pluginParams = new JRegistry($plugin->params);
$accessToEvent = $pluginParams->get('show_em_btn', 1);

JLoader::import('enrollment', JPATH_SITE . '/components/com_jticketing/models');
$enrollmentModel = new JticketingModelEnrollment;

$isEnrolled = $enrollmentModel->isAlreadyEnrolled($event->id, $this->userid);

?>

<link rel="stylesheet" type="text/css"  href="<?php echo JUri::root(true) . '/plugins/tjevent/' .
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
	$currentDate = JFactory::getDate()->toSql();
	$isEventOwner = $event->created_by == JFactory::getUser()->id && $currentDate > $event->enddate;

	if ($lessonData->lesson_status == 'completed' || $isEventOwner)
	{
		?>
		<div class="center alert alert-success text-center event_con">
			<i class="fa fa-thumbs-o-up" aria-hidden="true"></i>
			<span class="center"><?php echo JText::_("PLG_TJEVENT_EVENT_THANK_YOU");?></span>
		</div>
		<?php
	}
	elseif ($currentDate > $event->enddate && $event->online_events == 0)
	{
		?>
		<div class="center alert alert-danger text-center event_con">
			<span class="center"><?php echo JText::_("PLG_TJEVENT_EVENT_MISSED_EVENT");?></span>
		</div>
		<?php
	}
	else
	{
		?>
		<div class="center alert alert-info text-center event_con" >
			<?php echo JText::_('PLG_JTEVENTS_LESSON_COMPLETE_PREQUISITES');?>
		</div>
		<?php
	}
	?>
	<div class="tab-content" id="myTabContent">
		<div id="details" class="tab-pane active jt_event">
				<hr>
				<div class="row">
					<div class="col-xs-12 col-sm-12">
						<?php
						if ($event->online_events != 1 && isset($eventAddress))
						{
							$this->item->event_address = $eventAddress;
							$address = str_replace(" ", "+", $this->item->event_address);
							$url = "http://maps.google.com/maps/api/geocode/json?address=$address&sensor=true";
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, $url);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
							curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
							curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
							$response = curl_exec($ch);
							curl_close($ch);
							$this->response_a = json_decode($response);

							if ($this->response_a->status == 'OK')
							{
								?>
								<div id="jticketing-event-map" class="jticketing-event-map box-style p-10">
									<?php
										if ($this->response_a)
										{
											$lat = $this->response_a->results[0]->geometry->location->lat;
											$long = $this->response_a->results[0]->geometry->location->lng;
										}?>
									<div id="evnetGoogleMapLocation" >
										<?php echo JText::_('COM_JTICKETING_MAPS_LOADING'); ?>
									</div>
								</div>
							<?php
							}
						}

						?>
					</div>
					<div class="col-xs-12 col-sm-4 margint20">

					</div>
				</div>
		</div>
	</div>
</div>

<?php
$jticketing_params  = JComponentHelper::getParams('com_jticketing');
$google_map_api_key = $jticketing_params->get('google_map_api_key');
$defaultGMapLevel   = $jticketing_params->get('gmaps_default_zoom_level');

if ($event->online_events == '0')
{
	$lat = $lat;
	$long = $long;
}
else
{
	$lat = 0;
	$long = 0;
}
?>
<script type="text/javascript" src="<?php echo Juri::root() . '/plugins/tjevent/' .
$this->_name . '/' . $this->_name . '/assets/js/default.js';?>"></script>

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true&key=<?php echo $google_map_api_key;?>"></script>
<script>
var event_till = "<?php echo strtotime($event->startdate) - strtotime($currentDate); ?>";
var event_count_till = "<?php echo strtotime($event->enddate) - strtotime($currentDate); ?>";
var accessToEvent = "<?php echo $accessToEvent;?>";
var baseurl = "<?php echo JUri::root();?>";
var eventid = "<?php echo (int) $event->id;?>";
var onlineEvent = "<?php echo $event->online_events;?>";
var isboughtEvent = "<?php echo $eventData['isboughtEvent'];?>";
var enddate = "<?php echo $event->enddate;?>";
var startdate = "<?php echo $event->startdate;?>";
var currentTime = "<?php echo $this->currentTime;?>";
var showAdobeButton = "<?php echo $this->showAdobeButton;?>";
var jticketing_baseurl = "<?php echo JUri::root();?>";
var recording_error = "<?php echo JText::_('COM_JTICKETING_NO_RECORDING_FOUND');?>";
var recording_name = "<?php echo JText::_('COM_JTICKETING_RECORDING_NAME');?>";
var source = "<?php echo (int) $event->id;?>";
var isEnrolled = "<?php echo $isEnrolled;?>";

	showLabels(currentTime,startdate,enddate);

	jQuery('#countdown_timer'+source).countdown({
	/** global: event_till */
		until:event_till,
		compact: true,
		onTick: watchCountdown
	});

	jQuery(document).on("click", "#jt-enterMeeting<?php echo $event->id; ?>", function(){
		tjevent.event.onlineMeetingUrl(this, "<?php echo $event->id; ?>");
	});

	jQuery(document).on("click", "#jt-meetingRecording<?php echo $event->id; ?>", function(){
		jtSite.event.meetingRecordingUrl(this, "<?php echo $event->id; ?>");
	});

	jQuery(document).ready(function() {
		if(isEnrolled != '' || showAdobeButton == 1 )
		{
			if (onlineEvent == 1 && isboughtEvent == 1 && enddate > currentTime)
			{
					tjevent.event.onlineMeetingUrl(eventid);
			}
		}
	});

	if(onlineEvent != "1")
	{
		var lat = "<?php echo $lat;?>";
		var lon = "<?php echo $long;?>";
	}

defaultGMapLevel = <?php echo $defaultGMapLevel; ?>;
google.maps.event.addDomListener(window, 'load', initialize);
</script>


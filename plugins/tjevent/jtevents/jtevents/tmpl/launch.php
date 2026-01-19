<?php
/**
 * @package    Tjlms
 * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
$document->addScript(JUri::root() . 'components/com_tmt/assets/js/jquery.countdown.js');

// Give access to enter into online event
$plugin = JPluginHelper::getPlugin('tjevents', 'plug_tjevents_adobeconnect');
$pluginParams = new JRegistry($plugin->params);
$accessToEvent = $pluginParams->get('show_em_btn', 1);
?>
<script>
jQuery(document).ready(function() {

	jQuery('#event-to-start<?php echo $mediaDetails->source; ?>').show();

	if (jQuery(".event_btn<?php echo $mediaDetails->source; ?>").length)
	{
		<?php if($event->created_by != JFactory::getUser()->id)
		{?>
			jQuery(".event_btn<?php echo $mediaDetails->source; ?>").hide();
			var meetingBtn = '<a rel="popover" class="btn adobe-hidden-btn<?php echo $mediaDetails->source; ?>"><?php echo JText::_("PLG_TJEVENT_MEETING_BUTTON") ?></a>';
			jQuery(".adobe_btn<?php echo $mediaDetails->source; ?>").append(meetingBtn);
			<?php
		}?>
	}

	jQuery('[rel="popover"]').on('click', function (e) {
		jQuery('[rel="popover"]').not(this).popover('hide');
	});

	jQuery('.adobe-hidden-btn<?php echo $mediaDetails->source; ?>').popover({
		html: true,
		trigger: 'click',
		placement: 'top',
		content: function () {
			return '<button type="button" id="close" class="close" onclick="popup_close(this);">&times;</button><div class="tj-popover"><div class="tj-content"><?php echo JText::sprintf("PLG_TJEVENT_MEETING_ACCESS", $accessToEvent);?></div></div>';
		}
	});

	jQuery('#countdown_timer_<?php echo $mediaDetails->source; ?>').countdown({
			until:'<?php echo strtotime($eventDetails['startdate']) - strtotime($today); ?>',
			compact: true,
			onTick: watchCountdown_<?php echo $mediaDetails->source; ?>
	});
});

/*Popover close button*/
function popup_close(btn)
{
	jQuery(btn).closest('.popover').hide();
}

function watchCountdown_<?php echo $mediaDetails->source; ?>(periods)
{
	var accessToEvent = "<?php echo $accessToEvent;?>";

	if (jQuery.countdown.periodsToSeconds(periods) < (accessToEvent * 60))
	{
		jQuery('.adobe-hidden-btn<?php echo $mediaDetails->source; ?>').hide();
		jQuery('.event_btn<?php echo $mediaDetails->source; ?>').show();
	}

	if ((jQuery.countdown.periodsToSeconds(periods) <= 1*0))
	{
		watchEventcountdowns_<?php echo $mediaDetails->source; ?>()
	}
}

function watchEventcountdowns_<?php echo $mediaDetails->source; ?>()
{
	jQuery('#countdown_endtimer_<?php echo $mediaDetails->source; ?>').countdown({
	until: '<?php echo strtotime($eventDetails['enddate']) - strtotime($today); ?>',
	compact: true,
	onTick: watchRevcountdowns_<?php echo $mediaDetails->source; ?>
	});
}

function watchRevcountdowns_<?php echo $mediaDetails->source; ?>(periods)
{
	jQuery("#event-to-start<?php echo $mediaDetails->source;?>").hide();
	jQuery("#event-ended<?php echo $mediaDetails->source;?>").hide();
	jQuery("#event-to-end<?php echo $mediaDetails->source;?>").show();

	if ((jQuery.countdown.periodsToSeconds(periods) <= 1*0))
	{
		jQuery("#event-to-start<?php echo $mediaDetails->source; ?>").hide();
		jQuery("#event-to-end<?php echo $mediaDetails->source; ?>").hide();
		jQuery("#event-ended<?php echo $mediaDetails->source; ?>").show();
		jQuery(".event_btn<?php echo $mediaDetails->source; ?>").remove();
	}
}
</script>
<?php
$lesson_url = $tjlmshelperObj->tjlmsRoute("index.php?option=com_tjlms&view=lesson&lesson_id=" . $lesson->id . "&tmpl=component", false);
?>
<div>
	<div class="event-to-start tj_hide_btn" id="event-to-start<?php echo $mediaDetails->source;?>">
		<span class="text-info"><?php echo JText::_("PLG_TJEVENT_JTEVENTS_EVENT_STARTSIN");?></span>
		<span class="text-success" id='countdown_timer_<?php echo $mediaDetails->source; ?>'></span>
	</div>
	<div class="event-to-end tj_hide_btn" id="event-to-end<?php echo $mediaDetails->source;?>">
		<span class="text-warning"><?php echo JText::_("PLG_TJEVENT_JTEVENTS_EVENT_ENDSIN");?></span>
		<span class="text-success" id='countdown_endtimer_<?php echo $mediaDetails->source; ?>'></span>
	</div>
	<div class="event-ended tj_hide_btn" id="event-ended<?php echo $mediaDetails->source;?>">
		<span class="text-danger"><?php echo JText::_("PLG_TJEVENT_JTEVENTS_EVENT_ENDED");?></span>
	</div>
</div>
<?php
if (isset($eventDetails['adobe_connect']['url']) && ($today < $eventDetails['enddate']) && ($this->params->get('detail_page') == 1))
{	?>
<div class="adobe_btn<?php echo $mediaDetails->source;?>">
<a class="btn btn-success event_btn<?php echo $mediaDetails->source; ?>" target="_blank"
	href="<?php echo htmlspecialchars($eventDetails['adobe_connect']['url']); ?>">
	<span class="editlinktip " >
		<?php echo $eventDetails['adobe_connect']['text'];?>
	</span>
</a>
</div>
<?php
}?>
<style>
.tj_hide_btn{
display: none;
}
</style>

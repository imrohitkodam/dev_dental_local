jQuery(document).ready(function() {
	jQuery('[data-toggle="tooltip"]').tooltip();
	jQuery('.endevent, .event-ended, #multi_recordings').hide();
	jQuery('.startevent').show();

	jQuery('#countdown_timer').countdown({
		/** global: event_till */
			until:event_till,
			compact: true,
			onTick: watchCountdown
	});
});

function watchCountdown(periods)
{
	jQuery('#countdown_timer').css('color','#468847');

	/** global: accessToEvent */
	if (jQuery.countdown.periodsToSeconds(periods) < (accessToEvent * 60))
	{
		jQuery('#reverse_timer').addClass('text-success');
		jQuery('.startevent, .event_btn').show();
		jQuery('.endevent, .adobe-hidden-btn').hide();
	}

	if ((jQuery.countdown.periodsToSeconds(periods) <= 1*0))
	{
		watchEventcountdowns()
	}
}

function watchEventcountdowns()
{
	jQuery('#countdown_timer, .adobe-hidden-btn').hide();
	jQuery('#reverse_timer').countdown({
	/** global: event_count_till */
	until: event_count_till,
	compact: true,
	onTick: watchRevcountdowns
	});
}

function watchRevcountdowns(periods)
{
	jQuery('.counters').css('color', 'red');
	jQuery('.endevent').show();
	jQuery('.startevent, .event-ended').hide();

	if ((jQuery.countdown.periodsToSeconds(periods) < 5*60))
	{
		jQuery('.counters').css('color', 'red');
		jQuery('.endevent').show();
		jQuery('.startevent').hide();
	}

	if ((jQuery.countdown.periodsToSeconds(periods) <= 1*0))
	{
		jQuery('.event-ended').show();
		jQuery('.startevent, .endevent, .countertime').hide();
	}
}

jQuery("#jt-enterMeeting").on("click", function() {
	/** global: eventid */
	jtSite.event.onlineMeetingUrl(this, eventid);
});

jQuery("#jt-meetingRecording").on("click", function() {
	/** global: eventid */
	jtSite.event.meetingRecordingUrl(this, eventid);
});

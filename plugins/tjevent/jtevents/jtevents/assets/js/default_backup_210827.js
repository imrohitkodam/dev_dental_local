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

var tjevent =
{
	event:
	{
		enrolment : function(thisVal, selected_events, cid, lesson_url, isEnrolled)
		{
			if(isEnrolled == '' && cid != creator)
			{
				jQuery.ajax({
					type:'POST',
					url: Joomla.getOptions('system.paths').base + '/index.php?option=com_jticketing&task=enrollment.save&format=json',
					data: {
						cid: cid,
						selected_events: selected_events},
					datatype:"json",
					beforeSend: function () {
						jQuery(thisVal).button('loading');
					},
					complete: function () {
						location.reload();
					},
					success:function(respone){
						var returnedData = JSON.parse(respone);

						if(returnedData.data !== null)
						{
							var open = window.open(lesson_url,'_blank');
							if (open == null || typeof(open)=='undefined')
							{
								alert(Joomla.JText._('COM_JTICKETING_EVENTS_ENTER_MEETING_SITE_POPUPS'));
							}
						}
						else
						{
							alert(Joomla.JText._('PLG_TJEVENT_JTEVENTS_ENROLMENT_FAILURE'));
							location.reload();
						}
					},
					error: function(xhr,status,error) {
					}
				});
			}
			else
			{
				var open = window.open(lesson_url,'_blank');
				if (open == null || typeof(open)=='undefined')
				{
					alert(Joomla.JText._('COM_JTICKETING_EVENTS_ENTER_MEETING_SITE_POPUPS'));
				}
			}
		},

		onlineMeetingUrl : function(eventId)
		{
			if (eventId == undefined)
			{
				var eventId = jQuery('#event_id').val();
			}

			jQuery.ajax({
				url: Joomla.getOptions('system.paths').base + '/index.php?option=com_jticketing&view=event&task=event.onlineMeetingUrl&eventId='+eventId,
				type: 'GET',
				dataType: 'json',
				success: function(data)
				{
					if(data.data == 1)
					{
						top.location.href = Joomla.getOptions('system.paths').base + '/index.php?option=com_users&view=login';
						return;
					}

					top.location.href = data.data;

					if (window) {
					//Browser has allowed it to be opened
					window.focus();
					} else {
					//Browser has blocked it
					alert(Joomla.JText._('COM_JTICKETING_EVENTS_ENTER_MEETING_SITE_POPUPS'));
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
				}
			});
		},
	}
}

<?php
$imagePath = JRoute::_(JUri::base() . 'media/com_jticketing/images/' . $event->image, false);
$ticketTypes = $this->jtMainHelper->getEventDetails($event->id);
$jtFrontendHelper = new Jticketingfrontendhelper;
$venue = $jtFrontendHelper->getVenue($event->venue);
/* If venue address is not empty then get venue address otherwise load event location address*/
$EventAddress = empty($venue) ? $event->location: $venue->address;
$venueLocation = empty($event->venue) ? $EventAddress : $event->venue;
$document = JFactory::getDocument();
$document->addScript(JUri::root() . 'components/com_tmt/assets/js/jquery.countdown.js');

// Give access to enter into online event
$plugin = JPluginHelper::getPlugin('tjevents', 'plug_tjevents_adobeconnect');
$pluginParams = new JRegistry($plugin->params);
$accessToEvent = $pluginParams->get('show_em_btn', 1);

?>
<link rel="stylesheet" type="text/css"  href="<?php echo JUri::root(true). '/plugins/tjevent/' . $this->_name . '/' . $this->_name .'/style/jtevents.css';?>"></link>
<div class="tjlms-wrapper">
<?php	$currentDate = JFactory::getDate()->toSql();
		$isEventOwner = $event->created_by == JFactory::getUser()->id && $currentDate > $event->enddate;

	if ($this->isEventAttended || $isEventOwner)
	{	?>
		<div class="center alert alert-success event_con">
			<i class="fa fa-thumbs-o-up" aria-hidden="true"></i>
			<span class="center"><?php echo JText::_("PLG_TJEVENT_EVENT_THANK_YOU");?></span>
		</div>
<?php
	}elseif ($currentDate > $event->enddate && $event->online_events == 0)
	{	?>
		<div class="center alert alert-danger event_con">
			<span class="center"><?php echo JText::_("PLG_TJEVENT_EVENT_MISSED_EVENT");?></span>
		</div>
<?php
	}else{?>
	<div class="center alert alert-info event_con" >
		<?php echo JText::_('PLG_JTEVENTS_LESSON_COMPLETE_PREQUISITES');?>
	</div>
<?php }?>
	<div class="tab-content" id="myTabContent">
		<div id="details" class="tab-pane active jt_event">
			<div class="row-fluid">
				<div class="col-lg-7 col-md-7 col-sm-6 col-xs-12 span7"> <!--Event 	-->
					<div class="image">
						<img itemprop="image" class="event_img" src="<?php echo $imagePath;?>">
					</div>
					<hr>
					<div class="event_name">
						<h4><strong><?php echo JText::_('PLG_TJEVENT_EVENT_TITLE') . ' : ' . $event->title;?></strong></h4>
					</div>
					<div class="venue">
						<h5><?php echo JText::_('PLG_TJEVENT_VENUE_NAME') . ' : ' . $venueLocation;?></h5>
					</div>
					<div class="short_desc">
						<p><?php echo $event->short_description;?></p>
					</div>
					<div class="long_desc">
						<p><?php echo $event->long_description;?></p>
					</div>
				</div>
				<div class="col-lg-5 col-md-5 col-sm-6 col-xs-12 span5">
				<?php
				if ($event->online_events != 1 && isset($EventAddress))
				{
					$this->item->event_address = $EventAddress;
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
						/*Load Jticketing default_location.php*/
						ob_start();
						$locationView = JPATH_BASE . '/components/com_jticketing/views/event/tmpl/default_location.php';
						include $locationView;
						$locationView = ob_get_contents();
						ob_end_clean();
						echo $locationView;
					}
				} ?>
					<div class="box-style"> <!-- Event timing-->
						<div class="panel panel-default">
							<div class="panel-heading"><b><?php echo JText::_("PLG_TJEVENT_EVENT_TIME");?></b>
							</div>
							<div class="ticket_body">
								<div>
									<i class="fa fa-calendar" aria-hidden="true"></i>
									<?php echo JHtml::date($event->startdate,'j F Y'); ?>
									<b> To </b><?php echo JHtml::date($event->enddate,'j F Y'); ?>
								</div>
								<div>
									<i class="fa fa-clock-o" aria-hidden="true"></i>
									<?php echo JHtml::date($event->startdate,'g:i A'); ?>
									<b>  To  </b><?php echo JHtml::date($event->enddate,'g:i A'); ?>
								</div>
								<div>
									<i class="fa fa-clock-o countdown-container counters" aria-hidden="true">
									</i>
									<span class="text-info startevent tj_hide_btn">
										<?php echo JText::_("PLG_TJEVENT_JTEVENTS_EVENT_STARTSIN");?>
									</span>
									<span class="text-danger endevent tj_hide_btn">
										<?php echo JText::_("PLG_TJEVENT_JTEVENTS_EVENT_ENDSIN");?>
									</span>
									<span class="text-danger event-ended tj_hide_btn">
										<?php echo JText::_("PLG_TJEVENT_JTEVENTS_EVENT_ENDED");?>
									</span>
									<span class="countertime">
										<span id='countdown_timer'></span>
										<span class="text-success" id='reverse_timer'></span>
									</span>
								</div>
							</div>
						</div>
					</div><!-- Event timing End -->
				<div class="box-style">
					<div class="panel panel-default">
						<div class="panel-heading"><!--Event start from -->
							<b><?php echo JText::_("PLG_TJEVENT_BOOKING_DATE");?></b>
						</div>
						<div class="ticket_body">
							<div class="booking_date">
								<i class="fa fa-calendar" aria-hidden="true"></i>
								<?php echo JHtml::date($event->booking_start_date,'j F Y'); ?>
								<b>  To  </b>
								<?php echo JHtml::date($event->booking_end_date,'j F Y'); ?>
							</div>
						</div>
				<?php
					if (isset($ticketTypes))
					{ ?>
						<div class="ticket_details"><b><?php echo JText::_("PLG_TJEVENT_TICKET_DETAILS");?></b></div>
							<table class="table table-responsive">
						<?php	if (!empty($ticketTypes))
								{ ?>
								<tbody>
									<tr>
										<td><strong><?php echo JText::_("PLG_TJEVENT_TICKET_TITLE");?></strong></td>
										<td><strong><?php echo JText::_("PLG_TJEVENT_TICKET_PRICE");?></strong></td>
										<td><strong><?php echo JText::_("PLG_TJEVENT_TICKET_AVAILABLE");?></strong></td>
									</tr>
								<?php
									foreach($ticketTypes as $ticketType)
									{ ?>
									<tr>
										<td><?php echo $ticketType->title;?></td>
										<td><span><?php echo $ticketType->price;?></span></td>
										<?php
										if ($ticketType->unlimited_seats == 1)
										{	?>
											<td><?php echo JText::_("PLG_TJEVENT_UNLIMITED_TICKET");?></td>
								<?php	}else{
												if ($ticketType->count > 0)
												{	?>
													<td><?php echo $ticketType->count. "/" . $ticketType->available;?></td>
										<?php	}
												else
												{	?>
													<td><?php echo JText::_("PLG_TJEVENT_TICKET_SOLD_OUT");?></td>
								<?php			}
										}
										?>
								   </tr>
							  <?php } ?>
								</tbody>
							<?php
								}
							if (isset($eventData['buy_button_link']) && $eventData['isboughtEvent'] == '' || isset($eventData['adobe_connect']['url']))
								{
								?>
								<tfoot>
									<tr>
										<td colspan="3" class="center adobe_btn">
										<?php

										$isEventExpired = (strtotime($event->enddate) - strtotime($currentDate));

										if (($isEventExpired > 0) && !empty($eventData['buy_button_link']) && $eventData['isboughtEvent'] == '')
										{
										?>
											<a class="btn btn-success"
											href="<?php echo htmlspecialchars($eventData['buy_button_link']);?>">
												<span class="lesson_attempt_action"><?php echo JText::_("PLG_TJEVENT_BUY");?> </span>
											</a>
								<?php	}
										if (isset($eventData['adobe_connect']['url']))
										{
											if (is_array($eventData['adobe_connect']['url']))
											{	?>
												<div class="row" id="view_mult_res">
													<a class="btn btn-success event_btn"  href="#">
														<span class="editlinktip " >
															<?php echo $eventData['adobe_connect']['text'];?>
														</span>
													</a>
												</div>
												<div id="multi_recordings">
										<?php	foreach ($eventData['adobe_connect']['url'] as $key => $recording)
												{	?>
												<div class="row recording_btn">
													<a class="btn btn-success event_btn" target="_blank"
														href="<?php echo htmlspecialchars($recording); ?>">
														<span class="editlinktip " >
															<?php echo JText::sprintf("PLG_TJEVENT_VIEW_RECORDING", $key+1);?>
														</span>
													</a>
												</div>
									<?php		}	?>
												</div>
								<?php		}
											else
											{	?>
											<a class="btn btn-success event_btn" target="_blank"
												href="<?php echo htmlspecialchars($eventData['adobe_connect']['url']); ?>">
												<span class="editlinktip " >
													<?php echo $eventData['adobe_connect']['text'];?>
												</span>
											</a>
								<?php		}
								}	?>
										</td>
									</tr>
								</tfoot>
							<?php }?>
							</table>
			<?php	} ?>
						</div><!--end panel-default-->
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>

techjoomla.jQuery(document).ready(function() {
	jQuery('.endevent, .event-ended, #multi_recordings').hide();
	jQuery('.startevent').show();
	if (jQuery('.event_btn').length)
	{
		<?php if($event->created_by != JFactory::getUser()->id)
		{?>
			jQuery('.event_btn').hide();
			var meetingBtn = '<a rel="popover" class="btn adobe-hidden-btn"><?php echo JText::_("PLG_TJEVENT_MEETING_BUTTON") ?></a>';
			jQuery('.adobe_btn').append(meetingBtn);
			<?php
		}?>
	}
	jQuery('[rel="popover"]').on('click', function (e) {
		jQuery('[rel="popover"]').not(this).popover('hide');
	});

	jQuery('[rel="popover"]').popover({
		html: true,
		trigger: 'click',
		placement: 'top',
		content: function () {
			return '<button type="button" id="close" class="close" onclick="popup_close(this);">&times;</button><div class="tj-popover"><div class="tj-content"><?php echo JText::sprintf("PLG_TJEVENT_MEETING_ACCESS", $accessToEvent) ?></div></div>';
		}
	});

	jQuery('#countdown_timer').countdown({
			until:'<?php echo strtotime($event->startdate) - strtotime($currentDate); ?>',
			compact: true,
			onTick: watchCountdown
	});
});

/*Popover close button*/
function popup_close(btn)
{
	jQuery(btn).closest('.popover').hide();
}

function watchCountdown(periods)
{
	jQuery('#countdown_timer').css('color','#468847');
		var accessToEvent = "<?php echo $accessToEvent;?>";

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
	until: '<?php echo strtotime($event->enddate) - strtotime($currentDate); ?>',
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

jQuery("#view_mult_res").on( "click", function() {
	jQuery(this).hide();
	jQuery("#multi_recordings").show();
});
</script>

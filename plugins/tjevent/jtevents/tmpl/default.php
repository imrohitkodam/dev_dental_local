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
?>

<link rel="stylesheet" type="text/css"  href="<?php echo Uri::root(true) . '/plugins/tjevent/' .
$this->_name . '/' . $this->_name . '/assets/css/jtevents.css';?>"></link>

<div class="tjlms-wrapper">
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
				<div class="row bg-white">

					<div class="col-xs-12 col-sm-8 pl-0 pr-0 pb-10 leftimg bg-pinimage">
						<div itemprop="image" class="event_img" style="background:url('<?php echo $imagePath;?>') no-repeat; height: 100%; background-size: cover; background-position: center;">
						</div>
					</div><!-- End of div1 -->

					<div class="col-xs-12 col-sm-4 px-20">
						<div class="event_name">
							<h3><strong><?php echo $event->title;?></strong></h3>
						</div>

						<div class="venue">
							<h5 class="font-700 "><?php echo Text::_('PLG_TJEVENT_VENUE_NAME') . ' : ' . $venueLocation;?></h5>
						</div>

						<div class="short_desc">
							<p><?php echo $event->short_description;?></p>
						</div>

						<div class="long_desc">
							<p><?php echo $event->long_description;?></p>
						</div>

						<!-- Event timing -->
						<div class="box-style border-0">
							<div class="panel panel-default">
								<div class="panel-heading hidden"><b><?php echo Text::_("PLG_TJEVENT_EVENT_TIME");?></b>
								</div>

								<div class="ticket_body">
									<div>
										<i class="fa fa-calendar" aria-hidden="true"></i>
										<?php echo HTMLHelper::date($event->startdate, 'j F Y'); ?>
										<b> To </b>
										<?php echo HTMLHelper::date($event->enddate, 'j F Y'); ?>
									</div>

									<div>
										<i class="fa fa-clock-o" aria-hidden="true"></i>
										<?php echo HTMLHelper::date($event->startdate, 'g:i A'); ?>
										<b> To </b>
										<?php echo HTMLHelper::date($event->enddate, 'g:i A'); ?>
									</div>

									<div>
										<i class="fa fa-clock-o countdown-container counters" aria-hidden="true">
										</i>

										<span class="text-info font-bold startevent pr-10 tj_hide_btn">
											<?php echo Text::_("PLG_TJEVENT_JTEVENTS_EVENT_STARTSIN");?>
										</span>

										<span class="text-danger font-bold endevent pr-10 tj_hide_btn">
											<?php echo Text::_("PLG_TJEVENT_JTEVENTS_EVENT_ENDSIN");?>
										</span>

										<span class="text-danger font-bold event-ended tj_hide_btn">
											<?php echo Text::_("PLG_TJEVENT_JTEVENTS_EVENT_ENDED");?>
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
								<div class="panel-heading border-top-0 border-right-0 border-left-0"><!--Event start from -->
									<b><?php echo Text::_("PLG_TJEVENT_BOOKING_DATE");?></b>
								</div>

								<?php
								$booking_start_date = ($event->booking_start_date != '0000-00-00 00:00:00') ?
								$event->booking_start_date : $event->created;

								$booking_end_date = ($event->booking_end_date != '0000-00-00 00:00:00') ?
								$event->booking_end_date : $event->enddate;
								?>
								<div class="ticket_body p-10">
									<div class="booking_date">
										<i class="fa fa-calendar" aria-hidden="true"></i>
										<?php echo HTMLHelper::date($booking_start_date, 'j F Y'); ?>
										<b> To </b>
										<?php echo HTMLHelper::date($booking_end_date, 'j F Y'); ?>
									</div>

									<div>
										<i class="fa fa-clock-o" aria-hidden="true"></i>
										<?php echo HTMLHelper::date($booking_start_date, 'g:i A'); ?>
										<b> To </b>
										<?php echo HTMLHelper::date($booking_end_date, 'g:i A'); ?>
									</div>
								</div>
								<?php
									if (isset($ticketTypes))
									{
										?>
										<div class="ticket_details  p-5 text-center"><b><?php echo Text::_("PLG_TJEVENT_TICKET_DETAILS");?></b></div>
											<table class="table table-responsive">
											<?php
											if (!empty($ticketTypes))
												{
													?>
												<tbody>
													<tr>
														<td><strong><?php echo Text::_("PLG_TJEVENT_TICKET_TITLE");?></strong></td>

														<td><strong><?php echo Text::_("PLG_TJEVENT_TICKET_PRICE");?></strong></td>

														<td><strong><?php echo Text::_("PLG_TJEVENT_TICKET_AVAILABLE");?></strong></td>
													</tr>
												<?php
													foreach ($ticketTypes as $ticketType)
													{
														if ($ticketType->id)
														{
														?>
														<tr>
															<td><?php echo $ticketType->title;?></td>

															<td><span><?php echo $ticketType->price;?></span></td>
															<?php

															if ($ticketType->unlimited_seats == 1)
															{
																?>
																<td><?php echo Text::_("PLG_TJEVENT_UNLIMITED_TICKET");?></td>
																<?php
															}
															else
															{
																if ($ticketType->count > 0)
																{
																	?>
																	<td><?php echo $ticketType->count . "/" . $ticketType->available;?></td>
																	<?php
																}
																else
																{
																	?>
																	<td><?php echo Text::_("PLG_TJEVENT_TICKET_SOLD_OUT");?></td>
																	<?php
																}
															}
														}
														?>
												  		 </tr>
											  		<?php
													}
													?>
												</tbody>
												<?php
											}
												?>

												<tfoot>
													<tr>
														<td colspan="3" class="center adobe_btn">
														<?php
															$layout = new FileLayout('actions', JPATH_ROOT . '/components/com_jticketing/layouts/event');
															echo $layout->render($eventInfo);?>
														</td>
													</tr>
												</tfoot>
											</table>
										<?php
									}
										?>
							</div><!--end panel-default-->
						</div>
					</div><!-- End of div2 -->
				</div><!--End row white-bg -->
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
<script>
	var event_till         = "<?php echo strtotime($event->startdate) - strtotime($currentDate); ?>"
	var event_count_till   = "<?php echo strtotime($event->enddate) - strtotime($currentDate); ?>";
	var accessToEvent      = "<?php echo $accessToEvent;?>";
	var baseurl            = "<?php echo Uri::root();?>";
	var eventid            = "<?php echo $event->id;?>";
	var onlineEvent        = "<?php echo $event->online_events;?>";
	var jticketing_baseurl = "<?php echo Uri::root();?>";
	var recording_error    = "<?php echo Text::_('COM_JTICKETING_NO_RECORDING_FOUND');?>";
	var recording_name     = "<?php echo Text::_('COM_JTICKETING_RECORDING_NAME');?>";
	var event_id           = "<?php echo $event->id;?>";
	var currentDate        ="<?php echo $currentDate;?>";
	var startDate          = "<?php echo $startDate;?>";
	var endDate            = "<?php echo $endDate;?>";
	var onlineEvent        = "<?php echo $event->online_events;?>";

	jtSite.event.initEventDetailJs();

</script>

<script type="text/javascript" src="<?php echo Uri::root() . '/plugins/tjevent/' .
$this->_name . '/' . $this->_name . '/assets/js/default.js';?>"></script>

<?php
/**
 * @version    SVN: <svn_id>
 * @package    JTicketing
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2017 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

$jticketingMainHelper = new jticketingmainhelper;
$jticketingTimeHelper = new jticketingTimeHelper;
$integration = $this->params['integration'];
$pin_width = $this->params['pin_width'];
$pin_padding = $this->params['pin_padding'];

?>
<style type="text/css">
	@media (min-width: 480px){
		#jtwrap .pin {
			width: <?php echo $pin_width . 'px'; ?> ;
		}
	}
</style>
<?php
foreach ($this->items as $eventData)
{
?>
<div class="col-sm-3 col-xs-12 pin mb-15">
	<div class="pin__cover border-gray">
		<div class="pin__img">
		<?php
			$event = JT::event($eventData->id);
			$eventDetailUrl = $event->getUrl();

			if ($integration == 4)
			{
				$imagePath = '/media/com_easysocial/avatars/event/' . $eventData->id . '/';
			}

			if ($eventData->image)
			{
				$imagePath = $eventData->image->media_l;
			}
			else
			{
				$imagePath = JRoute::_(JUri::base() . 'media/com_jticketing/images/default-event-image.png');
			}
		?>
		<a class="d-block bg-center bg-cover bg-repn responsiveembed responsive-embed-16by9" href="<?php echo $eventDetailUrl; ?>"
			title="<?php echo $this->escape($eventData->title);?>"
		style="background-image:url('<?php echo $imagePath; ?>');">
		</a>
	  </div>

	  <div class="pin__ticket mr-5 px-5 absolute bg-faded">

			<a href="<?php echo $eventDetailUrl;?>"
				title="<?php echo $this->escape($eventData->title);?>">
			<?php
				if (($eventData->eventPriceMaxValue == $eventData->eventPriceMinValue)
					AND (($eventData->eventPriceMaxValue == 0) AND ($eventData->eventPriceMinValue == 0)))
				{
				?>
					<strong><?php echo strtoupper(JText::_('COM_JTICKETING_ONLY_FREE_TICKET_TYPE'));?></strong>
				<?php
				}
				elseif (($eventData->eventPriceMaxValue == $eventData->eventPriceMinValue)
					AND (($eventData->eventPriceMaxValue != 0) AND ($eventData->eventPriceMinValue != 0)))
				{
				?>
					<strong><?php echo $this->utilities->getFormattedPrice($eventData->eventPriceMaxValue);?></strong>
				<?php
				}
				elseif (($eventData->eventPriceMaxValue == 1) AND ($eventData->eventPriceMinValue == -1))
				{
				?>
					<strong>
						<?php echo '';?>
					</strong>
				<?php
				}
				else
				{
				?>
					<strong>
						<?php
							echo $this->utilities->getFormattedPrice($eventData->eventPriceMinValue);
							echo ' - ';
							echo $this->utilities->getFormattedPrice($eventData->eventPriceMaxValue);
						?>
					</strong>
				<?php
				}
			?>
			</a>
		</div>

		<div class="pin__info p-10 bg-faded">
			<ul class="list-unstyled">
				<li class="pb-5">
					<div>
						<i class="fa fa-calendar mr-5" aria-hidden="true"></i>
					<?php
						echo $this->utilities->getFormatedDate($eventData->startdate);
					?>
					</div>
				</li>
				<li class="pb-5 text-truncate">
					<?php
					$online = JUri::base() . 'media/com_jticketing/images/online.png';

					if ($eventData->online_events)
					{
					?>
						<img src="<?php echo $online; ?>"
						class="img-circle d-inline-block" alt="<?php echo JText::_('COM_JTK_FILTER_SELECT_EVENT_ONLINE')?>"
						title="<?php echo JText::sprintf('COM_JTICKETING_ONLINE_EVENT', $this->escape($eventData->title));?>">
					<?php
					}?>
					<b>
						<a href="<?php echo $eventDetailUrl;?>"
							title="<?php echo $this->escape($eventData->title);?>">
							<?php echo $this->escape($eventData->title);?>
							<?php
							if ($eventData->featured == 1)
							{
							?>
								<span>
								<i class="fa fa-star pull-right" aria-hidden="true"
								title="<?php echo JText::sprintf('COM_JTICKETING_FEATURED_EVENT', $this->escape($eventData->title));?>"></i>
								</span>
							<?php
							}
							?>
						</a>
					</b>
				</li>
				<li>
					<i class="fa fa-map-marker mr-5" aria-hidden="true"></i>
					<?php
					if (strlen($eventData->location) > 20)
					{
						echo substr($this->escape($eventData->location), 0, 20) . '...';
					}
					else
					{
						echo $this->escape($eventData->location);
					}
					?>
				</li>
			</ul>
		</div>
   </div>
</div>
<?php
$currTime = JFactory::getDate()->toSql();
}?>
<div></div>

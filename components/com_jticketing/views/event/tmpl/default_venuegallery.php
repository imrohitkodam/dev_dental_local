<?php
/**
 * @package     JTicketing
 * @subpackage  com_jticketing
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;

JHtml::_('behavior.modal', 'a.modal');

$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root(true) . '/media/com_jticketing/vendors/css/magnific-popup.css');
$document->addScript(JUri::root(true) . '/media/com_jticketing/vendors/js/jquery.magnific-popup.min.js');

if (count($this->item->venueVideoData) > 0)
{
	?>
	<div class="row my-15">
		<div class="col-xs-12 col-sm-9 videosText">
			<h5><?php echo Text::_('COM_JTICKETING_VENUE_VIDEOS');?></h5>
		</div>
	</div>
	<div class="row my-15">
		<div class="col-xs-12 col-sm-9 videosText">
			<h5><?php echo JText::_('COM_JTICKETING_VENUE_VIDEOS');?></h5>
		</div>
		<?php
		if (count($this->item->venueVideoData) > 0 && count($this->item->venueImageData) > 0)
		{
			?>
			<div class="col-xs-12 col-sm-3 gallary-filters">
				<select id="venue_gallary_filter">
					<option value="0"><?php echo JText::_('COM_JTICKETING_VENUE_TYPE');?></option>
					<option value="1"><?php echo JText::_('COM_JTICKETING_VENUE_VIDEOS');?></option>
					<option value="2"><?php echo JText::_('COM_JTICKETING_VENUE_IMAGES');?></option>
				</select>
			</div>
			<?php
		}?>
	</div>
	<div id="venue_videos">
		<?php
		if (!empty($this->item->venueVideoData))
		{
			?>
			<div id="venueVideo" class="row jtVideo">
				<div class="media" id="jt_video_gallery">
					<?php
					foreach ($this->item->venueVideoData as $venueVideo)
					{
						$venueVideoType = substr($venueVideo->type, 6);
						$videoId  = JticketingMediaHelper::videoId($venueVideoType, $venueVideo->media);
						$srclink = "https://www.youtube.com/embed/" . $videoId;
						$thumbSrc = JticketingMediaHelper::videoThumbnail($venueVideoType, $videoId);
						?>
						<div class="col-md-3 col-sm-4 col-xs-6 jt_gallery_image_item bg-center bg-faded text-center bg-cover bg-repn">
							<a rel="{handler: 'iframe', size: {x: 600, y: 600}}" href="<?php echo $srclink; ?>" class="modal d-block relative ">
								<img src="<?php echo JUri::root(true) . '/media/com_jticketing/images/play_icon.png';?>"class="play_icon center-xy absolute"/>
								<img src="<?php echo $thumbSrc; ?>" width="100%"/>
							</a>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<?php
		}
		?>
	</div>
	<?php
}

if (count($this->item->venueImageData) > 0)
{
	?>
	<div id="venue_images" class="mt-20">
		<div class="row">
			<div class="col-xs-12 imagesText">
				<h5><?php echo Text::_('COM_JTICKETING_VENUE_IMAGES');?></h5>
			</div>
		</div>

		<?php
		if (!empty($this->item->venueImageData))
		{
			?>
			<div id="venueImages" class="row">
				<div class="media" id="jt_image_gallery">
					<div class="popup-gallery-venue">
						<?php
						foreach ($this->item->venueImageData as $venueImage)
						{
							$img_path = $venueImage->media;
							?>
							<div class="col-md-3 col-sm-4 col-xs-6 mb-15 jt_image_item">
								<a href="<?php echo $img_path;?>" title="" class="" >
									<div class="jt-image-gallery-inner bg-center bg-faded text-center bg-cover bg-repn" style="background-image: url('<?php echo $img_path;?>');">
									</div>
								</a>
							</div>
							<?php
						}
						?>
					</div>
				</div>
			</div>
			<?php
		}
		?>
	</div>
	<?php
}
?>
<script type="text/javascript">
	jQuery(document).ready(function()
	{
	    jtSite.event.eventImgPopup('popup-gallery-venue');
		jtSite.venue.onChangefun();
	});
</script>

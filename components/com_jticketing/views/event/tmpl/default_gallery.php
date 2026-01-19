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
JHtml::_('behavior.modal', 'a.modal');

$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root(true) . '/media/com_jticketing/vendors/css/magnific-popup.css');
$document->addScript(JUri::root(true) . '/media/com_jticketing/vendors/js/jquery.magnific-popup.min.js');

if (isset($this->item->gallery))
{
	$eventVideoData = array();
	$eventImageData = array();

	for ($i = 0; $i <= count($this->item->gallery); $i++)
	{
		if (isset($this->item->gallery[$i]->type))
		{
			$eventContentType = substr($this->item->gallery[$i]->type, 0, 5);

			if ($eventContentType == 'image')
			{
				$eventImageData[$i] = $this->item->gallery[$i];
			}
			elseif ($eventContentType == 'video')
			{
				$eventVideoData[$i] = $this->item->gallery[$i];
			}
		}
	}

	if (count($eventVideoData) > 0)
	{
		?>
		<div class="row my-15">
			<div class="col-xs-12 col-sm-9 videosText">
				<h5><?php echo JText::_('COM_JTICKETING_EVENT_VIDEOS');?></h5>
			</div>
			<?php
			if (count($eventVideoData) > 0 && count($eventImageData) > 0)
			{
				?>
				<div class="col-xs-12 col-sm-3 gallary-filters">
					<select id="gallary_filter">
						<option value="0"><?php echo JText::_('COM_JTICKETING_EVENT_TYPE');?></option>
						<option value="1"><?php echo JText::_('COM_JTICKETING_EVENT_GALLERY_VIDEOS');?></option>
						<option value="2"><?php echo JText::_('COM_JTICKETING_EVENT_GALLERY_IMAGES');?></option>
					</select>
				</div>
				<?php
			}
			?>
		</div>
		<div id="videos">
			<?php
			if (!empty($eventVideoData))
			{
				?>
				<div id="eventVideo">
					<div class="row jtVideo">
						<div class="media" id="jt_video_gallery">
							<?php
							foreach ($eventVideoData as $eventVideo)
							{
								$eventVideoType = substr($eventVideo->type, 6);
								$videoId  = JticketingMediaHelper::videoId($eventVideoType, $eventVideo->media);
								$srclink = "https://www.youtube.com/embed/" . $videoId;
								$thumbSrc = JticketingMediaHelper::videoThumbnail($eventVideoType, $videoId);
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
				</div>
				<?php
			}
			?>
		</div>
		<?php
	}

	if (count($eventImageData) > 0)
	{
		?>
		<div id="images" class="mt-20">
			<div class="row">
				<div class="col-xs-12 imagesText">
					<h5><?php echo JText::_('COM_JTICKETING_EVENT_IMAGES');?></h5>
				</div>
			</div>
			<?php
			if (!empty($eventImageData))
			{
				?>
				<div id="eventImages">
					<div class="row">
						<div class="media" id="jt_image_gallery">
							<div class="popup-gallery-media">
								<?php
								foreach ($eventImageData as $eventImage)
								{
									$img_path = $eventImage->media;
									?>
									<div class="col-md-3 col-sm-4 col-xs-6 mb-15 jt_image_item">
										<a href="<?php echo $img_path;?>" title="" class="" >
											<div class="jt-image-gallery-inner bg-center bg-faded text-center bg-cover bg-repn" style="background-image: url('<?php echo $img_path;?>');">
											</div>
										</a>
									</div>
								<?php
								}?>
							</div>
						</div>
					</div>
				</div>
				<?php
			}
			?>
		</div>
		<?php
	}
}
?>
<script type="text/javascript">
	jQuery(document).ready(function()
	{
		jtSite.event.eventImgPopup('popup-gallery-media');
		jtSite.event.onChangefun();
	});
</script>

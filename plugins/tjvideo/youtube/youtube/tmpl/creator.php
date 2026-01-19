<?php
/**
 * @package Tjlms
 * @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     http://www.com
 */
defined('_JEXEC') or die('Restricted access');
	$source='';
	$subformat = $lesson->sub_format;

	if (!empty($subformat))
	{
		$subformat_source_options = explode('.', $subformat);
		$source_plugin = $subformat_source_options[0];
		$source_option = $subformat_source_options[1];

		if (!empty($source_option) && $source_plugin == 'youtube')
		{
			$source = $lesson->source;
			$path = JURI::root().'media/com_tjlms/lessons/'.$lesson->source;
			$filepath = $lesson->org_filename;
			$filename = basename($filepath);
	?>
			<div class="control-group">
				<div class="control-label"><label title="<?php echo JText::_("COM_TJLMS_SELECTED_VIDEO");?>"><?php echo JText::_("COM_TJLMS_SELECTED_VIDEO");?></label></div>
				<div  class="controls">
					<span><?php echo trim($source);?></span>
					<a class="btn btn-primary" onclick="previewlesson(this,'<?php echo $lesson_id?>')" title="<?php echo JText::_('COM_TJLMS_PREVIEW_LESSON_DESC');?>"><?php echo JText::_('COM_TJLMS_PREVIEW_LESSON');?></a>
				</div>
			</div>

	<?php } ?>
<?php } ?>

		<div class="control-label">
			<label title="<?php echo JText::_("COM_TJLMS_VIDEO_FORMAT_URL_OPTIONS");?>">
			<?php echo JText::_("COM_TJLMS_VIDEO_FORMAT_URL_OPTIONS");?>
			</label>
		</div>

		<div  class="controls">
			<input type="hidden" id="subformatoption" name="lesson_format[youtube][subformatoption]" value="url"/>
			<div id="video_textarea" >
				<textarea id="video_url" class="input-block-level"cols="50" rows="2"
						name="lesson_format[youtube][url]"><?php echo trim($source);?></textarea>
			</div>
		</div>
		<div class="control-group">
			<div  class="controls">
				<div class="help">
					<?php echo JText::_('PLG_TJVIDEO_YOUTUBE_URL_HELP');?>
				</div>
			</div>
		</div>

<script type="text/javascript">

	function validatevideoyoutube(formid,format,subformat,media_id)
	{
		var res = {check: 1, message: ""};

		var format_lesson_form = techjoomla.jQuery("#lesson-format-form_"+ formid);

		if(media_id == 0)
		{
			if (!techjoomla.jQuery("#lesson_format #" + format + " #video_url",format_lesson_form).val())
			{
				res.check = '0';
				res.message = "<?php echo JText::_('PLG_TJVIDEO_YOUTUBE_URL_MISSING');?>";
			}
			else
			{
				var checkURL = checkYoutubeurl(format, format_lesson_form);
				if (!checkURL)
				{
					res.check = '0';
					res.message = "<?php echo JText::_('PLG_TJVIDEO_YOUTUBE_URL_NOT_YOUTUBE');?>";
				}
			}
		}
		else
		{
			var checkURL = checkYoutubeurl(format, format_lesson_form);
			if (!checkURL)
			{
				res.check = '0';
				res.message = "<?php echo JText::_('PLG_TJVIDEO_YOUTUBE_URL_NOT_YOUTUBE');?>";
			}
		}

		return res;
	}

	function checkYoutubeurl(format, format_lesson_form)
	{
			var url = techjoomla.jQuery("#lesson_format #" + format + " #video_url",format_lesson_form).val();

			if(url)
			{
				var parser = document.createElement('a');
				parser.href = url;

				if(parser.hostname !== 'www.youtube.com')
				{
					return false;
				}
			}

			return true;
	}
</script>




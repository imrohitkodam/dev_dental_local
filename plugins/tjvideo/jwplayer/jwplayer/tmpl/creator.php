<?php
/**
 * @package Tjlms
 * @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     http://www.com
 */
defined('_JEXEC') or die('Restricted access');

	$subformat = $lesson->sub_format;

if (!empty($subformat))
{
		$subformat_source_options = explode('.', $subformat);
		$source_plugin = $subformat_source_options[0];
		$source_option = $subformat_source_options[1];
		$source = $lesson->source;

		if (!empty($source_option) && $source_plugin == 'jwplayer')
		{
			$path = JURI::root().'media/com_tjlms/lessons/'.$lesson->source;
			$filepath = $lesson->org_filename;
			$filename = basename($filepath);
	?>
			<div class="control-group">
				<div class="control-label"><label title="<?php echo JText::_("COM_TJLMS_SELECTED_VIDEO");?>"><?php echo JText::_("COM_TJLMS_SELECTED_VIDEO");?></label></div>
				<div  class="controls video_area">

						<a href="<?php echo $path ?>"><?php echo $filename ?></a>
						<a class="btn btn-primary" onclick="previewlesson(this,'<?php echo $lesson_id?>')" title="<?php echo JText::_('COM_TJLMS_PREVIEW_LESSON_DESC');?>"><?php echo JText::_('COM_TJLMS_PREVIEW_LESSON');?></a>
				</div>
			</div>

	<?php } ?>
<?php } ?>
<div class="control-group">
	<div class="control-label"><label title="<?php echo JText::_("COM_TJLMS_VIDEO_FORMAT_OPTIONS");?>"><?php echo JText::_("COM_TJLMS_VIDEO_FORMAT_OPTIONS");?></label></div>

	<div  class="controls">
		<div id="video_package">
			<div class="fileupload fileupload-new pull-left" data-provides="fileupload">
				<div class="input-append">
					<div class="uneditable-input span4">
						<span class="fileupload-preview">
						<?php echo
						JText::sprintf('COM_TJLMS_UPLOAD_FILE_WITH_EXTENSION', 'flv, mp4, mp3', $comp_params->get('lesson_upload_size', '0', 'INT'));?>

						</span>
					</div>
					<span class="btn btn-file">
						<span class="fileupload-new"><?php echo  JText::_("COM_TJLMS_BROWSE");?></span>
						<input type="file" id="video_upload"
								name="lesson_format[upload]"
								onchange="validate_file(this,'<?php echo $mod_id;?>','<?php echo $plg;?>')">
					</span>
				</div>
			</div>
			<div style="clear:both"></div>
			<div class="format_upload_error alert alert-error" style="display:none" ></div>
			<div class="format_upload_success alert alert-info" style="display:none"></div>
		</div>
		<input type="hidden" class="valid_extensions" value="flv,mp4,mp3"/>
		<input type="hidden" id="uploded_lesson_file" name="lesson_format[jwplayer][upload]" value=""/>
		<input type="hidden" id="subformatoption" name="lesson_format[jwplayer][subformatoption]" value="upload"/>

	</div>
</div>

<script type="text/javascript">

	/* Function to load the loading image. */
	function validatevideojwplayer(formid,format,subformat,media_id)
	{
		var res = {check: 1, message: "PLG_TJVIDEO_JWPLAYER_VAL_PASSES"};

		var val_passed = '0';
		if(media_id == 0)
		{
			var format_lesson_form = techjoomla.jQuery("#lesson-format-form_"+ formid);

			if (!techjoomla.jQuery("#lesson_format #" + format + " #uploded_lesson_file",format_lesson_form).val())
			{
				res.check = '0';
				res.message = "<?php echo JText::_('PLG_TJVIDEO_JWPLAYER_FILE_MISSING');?>";
			}
		}
		return res;
	}

</script>


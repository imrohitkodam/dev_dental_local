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

		if (!empty($source_option) && $source_plugin == 'boxapi')
		{
			$path = JURI::root().'media/com_tjlms/lessons/'.$lesson->source;
			$filepath = $lesson->org_filename;
			$filename = basename($filepath);
?>
		<div class="control-group">
			<div class="control-label"><?php echo JText::_("COM_TJLMS_UPLOADED_FORMAT_FILE");?></div>
			<div  class="controls">

				<a href="<?php echo $path ?>"><?php echo $filename ?></a>

					<a class="btn btn-primary" onclick="previewlesson(this,'<?php echo $lesson_id?>')" title="<?php echo JText::_('COM_TJLMS_PREVIEW_LESSON_DESC');?>"><?php echo JText::_('COM_TJLMS_PREVIEW_LESSON');?></a>

			</div>
		</div>
	<?php	}  ?>
<?php	}  ?>

	<div class="control-group">
		<div class="control-label"><label title="<?php echo JText::_('COM_TJLMS_UPLOAD_FORMAT');?>"><?php echo JText::_("COM_TJLMS_UPLOAD_FORMAT") ?></label></div>

		<div  class="controls">
			<!--<input type="hidden"
				id="lesson_format<?php echo $plugin_name?>document_source"
				name="lesson_format[<?php echo $plugin_name?>][document_source]"
				value="upload"/>-->
			<div class="document_upload">
				<div class="fileupload fileupload-new pull-left" data-provides="fileupload">
					<div class="input-append">
						<div class="uneditable-input span4">
							<span class="fileupload-preview">
								<?php echo JText::sprintf('COM_TJLMS_UPLOAD_FILE_WITH_EXTENSION', 'pdf, doc, docx, ppt, pptx', $comp_params->get('lesson_upload_size', '0', 'INT'));?>
							</span>
						</div>
						<span class="btn btn-file">
							<span class="fileupload-new"><?php echo JText::_("COM_TJLMS_BROWSE");?></span>
							<input type="file"
									id="document_upload"
									name="lesson_format[<?php echo $plugin_name?>][document]"
									onchange="validate_file(this,'<?php echo $mod_id?>','<?php echo $plugin_name;?>')">
						</span>
					</div>
				</div>
				<div style="clear:both"></div>
				<div class="format_upload_error alert alert-error" style="display:none" ></div>
				<div class="format_upload_success alert alert-info" style="display:none"></div>
			</div>
			<input type="hidden" class="valid_extensions" value="pdf,doc,docx,ppt,pptx"/>
			<input type="hidden" id="uploded_lesson_file" name="uploded_lesson_file" value=""/>
			<input type="hidden" id="subformatoption" name="lesson_format[boxapi][subformatoption]" value="upload"/>
		</div>
	</div>


<script type="text/javascript">

	/* Function to load the loading image. */
	function validatedocumentboxapi(formid,format,subformat,media_id)
	{
		var res = {check: 1, message: "<?php echo JText::_('PLG_TJDOC_BOXAPI_VAL_PASSES');?>"};
		var main_format_form = techjoomla.jQuery("#lesson-format-form_"+ formid);

		if(media_id == 0)
		{
			if (!techjoomla.jQuery("#lesson-format-form_"+ formid + " #lesson_format #" + format + " #uploded_lesson_file").val())
			{
				var res = {check: 0, message: "<?php echo JText::_('PLG_TJDOC_BOXAPI_FILE_MISSING');?>"};
			}
		}
		return res;
	}

</script>


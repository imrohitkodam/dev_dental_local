<?php
/**
 * @package Tjlms
 * @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     http://www.com
 */
defined('_JEXEC') or die('Restricted access');


$lang_con_for_upload_formt_file = "PLG_TJSCORM_NATIVESCORM_UPLOAD_NEW_FORMAT";
$passing_score = '';
$grade_method = '' ;

$filename = "<div class='help-block'>" . JText::sprintf('COM_TJLMS_UPLOAD_FILE_WITH_EXTENSION','zip',$comp_params->get('lesson_upload_size','0','INT')) . "</div>";
$file_browse_lang = "PLG_TJSCORM_NATIVESCORM_BROWSE";
$edit = 0;
$subformat = $lesson->sub_format;

if (!empty($subformat))
{
	$subformat_source_options = explode('.', $subformat);
	$source_plugin = $subformat_source_options[0];
	$source_option = $subformat_source_options[1];

	if (!empty($source_option) && $source_plugin == 'nativescorm')
	{
		if ($scormLesson)
		{
			$source = $lesson->source;

			$path = JURI::root().'media/com_tjlms/lessons/'.$lesson->source;
			$filepath = $lesson->org_filename;
			$filename = basename($filepath);

			$edit = 1;
			$lang_con_for_upload_formt_file = "PLG_TJSCORM_NATIVESCORM_UPLOADED_FORMAT_FILE";
			$file_browse_lang = "PLG_TJSCORM_NATIVESCORM_CHANGE";

			$passing_score = $scormLesson->passing_score;
			$grade_method = $scormLesson->grademethod;
		}
	}
} ?>

<div class="control-group">
	<div class="control-label"><label title="<?php echo JText::_($lang_con_for_upload_formt_file); ?>"><?php echo JText::_($lang_con_for_upload_formt_file);?></label></div>
	<div class="controls scorm_subformat" id="scorm_subformat_scorm">
		<div class="fileupload fileupload-new pull-left" data-provides="fileupload">
			<div class="input-append">
				<div class="uneditable-input span4">
					<span class="fileupload-preview">
						<?php echo $filename; ?>
					</span>
				</div>
				<span class="btn btn-file">
					<span class="fileupload-new"><?php echo JText::_($file_browse_lang);?></span>
					<input type="file" id="scorm_upload" name="lesson_format[scorm]" onchange="validate_file(this,'<?php echo $mod_id;?>','nativescorm')";>
				</span>

				<?php if($edit == 1){ ?>
					<a class="btn" href="<?php echo $path;?>">
						<span><?php echo JText::_("PLG_TJSCORM_NATIVESCORM_DOWNLOAD");?></span>
					</a>
					<a class="btn btn-primary" onclick="previewlesson(this,'<?php echo $lesson_id?>')">
						<span><?php echo JText::_("PLG_TJSCORM_NATIVESCORM_PREVIEW");?></span>
					</a>
				<?php } ?>
			</div>

			<?php if($edit == 1){ ?>
			<div class="help">
				<?php echo JText::sprintf('COM_TJLMS_UPLOAD_FILE_WITH_EXTENSION','zip',$comp_params->get('lesson_upload_size','0','INT'));?>
			</div>
		<?php } ?>
		</div>
		<div style="clear:both"></div>
		<div class="format_upload_error alert alert-error" style="display:none" ></div>
		<div class="format_upload_success alert alert-info" style="display:none"></div>
		<input type="hidden" class="valid_extensions" value="zip"/>
	</div>
</div>

<!--div class="alert alert-info"><?php echo JText::_('PLG_TJSCORM_NATIVESCORM_MULTISCO_PARAMS_MSG') ?></div>

<div class="control-group">
	<div class="control-label">
		<label title="<?php echo JText::_('PLG_TJSCORM_NATIVESCORM_PASSING_SCORE')?>"><?php echo JText::_('PLG_TJSCORM_NATIVESCORM_PASSING_SCORE') ?></label>
	</div>
	<div class="controls">
		<input type="number" id="passing_score" name="lesson_format[nativescorm][passing_score]" value="<?php echo $passing_score; ?>" class="" aria-invalid="false">
	</div>
</div>

<div class="control-group">
	<div class="control-label">
		<label title="<?php echo JText::_('PLG_TJSCORM_NATIVESCORM_GRADE_METHOD')?>"><?php echo JText::_('PLG_TJSCORM_NATIVESCORM_GRADE_METHOD') ?></label>
	</div>
	<div class="controls">
		<?php
			$options[] = JHTML::_('select.option','0',JText::_('PLG_TJSCORM_NATIVESCORM_SELECT'));
			$options[] = JHTML::_('select.option','1',JText::_('PLG_TJSCORM_NATIVESCORM_NO_OF_LERAING_OBJECTS'));
			$options[] = JHTML::_('select.option','2',JText::_('PLG_TJSCORM_NATIVESCORM_HIGHEST_SCORE_AROSS_ALL'));
			$options[] = JHTML::_('select.option','3',JText::_('PLG_TJSCORM_NATIVESCORM_AVARAGE'));
			$options[] = JHTML::_('select.option','4',JText::_('PLG_TJSCORM_NATIVESCORM_SUM_OF_ALL'));
			echo  JHTML::_('select.genericlist', $options, 'lesson_format[nativescorm][grademethod]', 'class = "inputbox"', 'value','text', $grade_method);

			?>
	</div>
</div-->

<input type="hidden" id="subformatoption" name="lesson_format[nativescorm][subformatoption]" value="upload"/>
<input type="hidden" id="uploded_lesson_file" name="lesson_format[nativescorm][uploded_lesson_file]" value=""/>

<script type="text/javascript">

	/* Function to load the loading image. */
	function validatescormnativescorm(formid,format,subformat,media_id)
	{
		var res = {check: 1, message: ""};

		var format_lesson_form = techjoomla.jQuery("#lesson-format-form_"+ formid);

		var fileUploaded = techjoomla.jQuery("#lesson_format #" + format + " #uploded_lesson_file",format_lesson_form).val();

		var passingscore = techjoomla.jQuery("#lesson_format #" + format + " #passing_score",format_lesson_form).val();
		var grademethod = techjoomla.jQuery("#lesson_format #" + format + " #lesson_format_grademethod",format_lesson_form).val();

		if(media_id == 0)
		{
			if (!fileUploaded)
			{
				res.check = '0';
				res.message = "<?php echo JText::_('PLG_TJSCORM_NATIVESCORM_FILE_MISSING');?>";
			}
		}
		else
		{

			if (!fileUploaded)
			{
				res.check = 1;
			}
		}

		if(res.check == 1)
		{
			res.message = "<?php echo JText::_('PLG_TJSCORM_NATIVESCORM_VAL_PASSES');?>";
		}

		return res;
	}

</script>


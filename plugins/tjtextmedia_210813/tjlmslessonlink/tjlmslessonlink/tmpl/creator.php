<?php
/**
 * @package Tjlms
 * @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     http://www.com
 */
defined('_JEXEC') or die('Restricted access');

$source = (isset($lesson->source)) ? $lesson->source : '';
$preview_class = "tjlms_display_none";
$subformat = $lesson->sub_format;
$source_plugin = $source_option = '';

if (!empty($subformat))
{
	$subformat_source_options = explode('.', $subformat);
	$source_plugin = $subformat_source_options[0];
	$source_option = $subformat_source_options[1];
}
?>
<link rel="stylesheet" type="text/css"  href="<?php echo JUri::root(true). '/plugins/tjtextmedia/' . $this->_name . '/' . $this->_name .'/style/tjlmslessonlink.css';?>"></link>
<div class="control-group"></div>
<?php
if (!empty($source_option) && $source_plugin == 'tjlmslessonlink')
{
	$source = trim($lesson->source);
	$URL = $lesson->source;
	$URL_len = (strlen($URL) > 95 ? 'url_len' : '');
	?>
	<div class="control-group">
		<div class="control-label">
			<label title="<?php echo JText::_("PLG_TJTEXTMEDIA_LAL_SELECTED_URL");?>">
				<?php echo JText::_("PLG_TJTEXTMEDIA_LAL_SELECTED_URL_TITLE");?></label>
		</div>
		<div  class="controls">
			<span class="input-medium selected_url <?php echo $URL_len;?>" title="<?php echo $URL;?>"><?php echo $URL;?></span>
			<span class="input-append">
				<a class="btn btn-primary" onclick="previewlesson(this,'<?php echo $lesson->lesson_id;?>')"
					title="<?php echo JText::_('PLG_TJTEXTMEDIA_LAL_PREVIEW_TITLE');?>" role="button">
					<?php echo JText::_('PLG_TJTEXTMEDIA_LAL_PREVIEW');?>
				</a>
			</span>
		</div>
	</div>
<?php
}
?>
<input type="hidden" id="jform_request_id_id" class="required modal-value" name="jform[request][id]" value="" aria-required="true" required="required">
<div class="control-group">
	<div class="controls">
		<div class="alert alert-info">
			<span><?php echo JText::_('PLG_TJTEXTMEDIA_LAL_INVALID_URL_NOTE');?></span>
		</div>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<label id="jform_link-lbl" for="jform_link" class="hasTooltip"
			title="<?php echo JText::_('PLG_TJTEXTMEDIA_LAL_URL_TITLE');?>">
			<?php echo JText::_('PLG_TJTEXTMEDIA_LAL_URL');?>
		</label>
	</div>
	<div class="controls">
		<textarea id="tjlmslessonlink_url" cols="50" class="input-block-level"
			rows="2" name="lesson_format[tjlmslessonlink][url]"></textarea>
		<input type="hidden" id="subformatoption" name="lesson_format[tjlmslessonlink][subformatoption]" value="url"/>
	</div>
</div>

<script>
function isValidUrl(){
	var res = {check: 1, message: ""};
	var url=document.getElementById("tjlmslessonlink_url").value;
	var regexp = /(ftp|http|https):\/\/\w+(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;

	if (url.match(/\s/g)){
		document.getElementById("tjlmslessonlink_url").focus();
		return false;
	}
	else if (regexp.test(url) == 1)
	{
		return true;
	}
	else
	{
		document.getElementById("tjlmslessonlink_url").focus();
		return false;
	}
}
function validatetextmediatjlmslessonlink(formid,format,subformat,media_id){
	var res = {check: 1, message: ""};
	var format_lesson_form = techjoomla.jQuery("#lesson-format-form_"+ formid);
	var selectedURL, newURL;
	selectedURL = techjoomla.jQuery("#lesson_format #" + format + " .selected_url",format_lesson_form).text();
	newURL = techjoomla.jQuery("#lesson_format #" + format + " #tjlmslessonlink_url",format_lesson_form).val();

	if(selectedURL.trim() =='' && newURL.trim() =='')
	{
		res.check = '0';
		res.message = "<?php echo JText::_('PLG_TJTEXTMEDIA_LAL_URL_MISSING');?>";
		return res;
	}

	if (((newURL != '' && isValidUrl(newURL) == true)) || (selectedURL != '' && newURL == '') || (selectedURL != '' && newURL != '' && isValidUrl(newURL) == true))
	{
		return res;
	}

	res.check = '0';
	res.message = "<?php echo JText::_('PLG_TJTEXTMEDIA_LAL_ENTER_VALID_URL');?>";
	return res;
}
</script>

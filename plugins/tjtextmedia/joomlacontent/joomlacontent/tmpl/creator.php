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
$html_src = '';
$user_id = JFactory::getUser()->id;

$subformat = $lesson->sub_format;
$source_plugin = $source_option = '';

/*Check subformat is empty or not*/
if (!empty($subformat))
{
	$subformat_source_options = explode('.', $subformat);
	$source_plugin = $subformat_source_options[0];
	$source_option = $subformat_source_options[1];
}
$newArticleLink = JURI::base() . 'index.php?option=com_content&view=article&layout=edit';
?>
<div class="control-group"></div>
<?php
if ($articleCnt == 0 && empty($subformat))
{?>
<div class="alert">
	<span><?php echo JText::sprintf('PLG_TJTEXTMEDIA_JC_CREATE_NEW_ARTICLE', $newArticleLink);?></span>
</div>
<?php
}
else
{
?>
<div class="control-group">
	<div class="control-label">
		<label for="jform_request_id_id" title="<?php echo JText::_("PLG_TJTEXTMEDIA_JC_SELECT_ARTICLE_TITLE");?>" >
		<?php echo JText::_("PLG_TJTEXTMEDIA_JC_SELECT_ARTICLE");?> <span class="star">&nbsp;*</span> </label>
	</div>
	<div class="controls">
		<span class="input-append">
		<?php
		if (!empty($source_option) && $source_plugin == 'joomlacontent')
		{
				$source = trim($lesson->source);
				$path = $lesson->source;
				$params = json_decode($lesson->params);
				$previewArticle = $this->articleIsPublished($params->contentid);
		?>
			<input type="text" class="input-large" id="selected_article" value="<?php echo $params->contentnm;?>" disabled="disabled">
			<a href="#modalArticlejform_request_id" id="select_article_id" class="btn" role="button" data-toggle="modal"
				title="<?php echo JText::_('PLG_TJTEXTMEDIA_JC_CHANGE_ARTICLE');?>"
				data-original-title="Select article">
				<?php echo JText::_("PLG_TJTEXTMEDIA_JC_CHANGE");?>
			</a>
	<?php   if (is_int($previewArticle))
			{	?>
				<a class="btn btn-primary" onclick="previewlesson(this,'<?php echo $lesson->lesson_id;?>')"
					title="<?php echo JText::_('PLG_TJTEXTMEDIA_JC_PREVIEW_TITLE');?>" role="button" >
					<?php echo JText::_('PLG_TJTEXTMEDIA_JC_PREVIEW');?>
				</a>
			<?php
			}
			else
			{	?>
				<a class="btn btn-disabled" title="<?php echo $previewArticle;?>">
					<i rel="popover" class="icon-lock" ></i><span class="lesson_attempt_action">
						<?php echo JText::_('PLG_TJTEXTMEDIA_JC_PREVIEW');?>
					</span>
				</a>
<?php		}
		?>
<?php	}else{ ?>

			<input type="text" class="input-large" id="jform_request_id_name" value="Select an Article" disabled="disabled">
			<a href="#modalArticlejform_request_id" id="#select_article_id" class="btn hasTooltip " role="button"
				data-toggle="modal" title="<?php echo JText::_('PLG_TJTEXTMEDIA_JC_SELECT_ARTICLE');?>"
				data-original-title="Select article">
				<?php echo JText::_("PLG_TJTEXTMEDIA_JC_SELECT");?>
			</a>
<?php	}
		?>

		</span>
	</div>
</div>
<!--	Modal	-->
<div id="modalArticlejform_request_id" tabindex="-1" class="modal hide fade" style="display: none;" aria-hidden="true">
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
	<h3><?php echo JText::_("PLG_TJTEXTMEDIA_JC_SELECT_ARTICLE");?></h3>
</div>
	<div class="modal-body">
		<iframe class="iframe"
			src="index.php?option=com_content&view=articles&layout=modal&tmpl=component&function=validArticle&filter_published=1"
			name="Select article" height="400px" width="800px">
		</iframe>
	</div>
</div>
	<input type="hidden" id="subformatoption" name="lesson_format[joomlacontent][subformatoption]" value="url"/>
	<input type="hidden" id="joomlacontent_url" name="lesson_format[joomlacontent][url]">
	<input type="hidden" id="joomlacontent_params" name="lesson_format[joomlacontent][params]" />
<?php
}
?>

<script type="text/javascript" language="javascript">

	jQuery(document).ready(function()
	{
		validArticle=function(id,title,catid,uk1,url,uk2,uk3)
		{
			var source = {contentid: id, contentnm: title};
			var jsonString = JSON.stringify(source);

			/*	Set Dynamic value to textarea after selection of article */
			techjoomla.jQuery("#joomlacontent_params").val(jsonString);
			document.getElementById("joomlacontent_url").value =url;
			jQuery('#jform_request_id_name').val(title);
			jQuery('#selected_article').val(title);
			jQuery('#modalArticlejform_request_id').modal('hide');
			SqueezeBox.close();
		}
    });

	function validatetextmediajoomlacontent(formid,format,subformat,media_id)
	{
		var res = {check: 1, message: ""} ,format_lesson_form ,newContent = oldContent = '';
		format_lesson_form = techjoomla.jQuery("#lesson-format-form_"+ formid);
		newContent = techjoomla.jQuery("#lesson_format #" + format + " #jform_request_id_name",format_lesson_form).val()
		oldContent = techjoomla.jQuery("#lesson_format #" + format + " #selected_article",format_lesson_form).val()

		/*Check both newContent and oldContent are empty then return error media id if it is zero than return error*/
		if( (oldContent == undefined || oldContent == '' ) && newContent == "Select an Article" )
		{
			res.check = '0';
			res.message = "<?php echo JText::_('PLG_TJTEXTMEDIA_JC_URL_MISSING');?>";
		}

		return res;
	}
</script>

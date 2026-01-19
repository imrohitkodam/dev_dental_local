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

if (!empty($subformat))
{
	$subformat_source_options = explode('.', $subformat);
	$source_plugin = $subformat_source_options[0];
	$source_option = $subformat_source_options[1];
}

if (!empty($source_option) && $source_plugin == 'nativeeditor')
{

	$source = $lesson->source;
	$preview_class = "";
		$html_src = JURI::root() . "index.php?option=com_tjlms&view=lesson&tmpl=component&lesson_id=" . $lesson_id . "&mode=preview&attempt=1&fs=1";

}
else
{
?>

	<div class="tjlms_html_belonging_before_upload" align="left">
		<a class="btn btn-primary article modal" onclick="openNativebuilder(this,'add')"><?php echo JText::_("PLG_TEXTMEDIA_NATIVEBUILDR_LAUNCH_BUILDER");?></a>
	</div>
<?php } ?>


<div class="control-group tjlms_html_lesson_preview tjlms_html_belonging_after_upload <?php echo $preview_class;?>">
	<input type="hidden" id="textmedia_source" value="source"/>
	<iframe width="100%" height="400px" src="<?php echo $html_src ;?>"></iframe>
	<div class="tjlms_text_center">
		<a class="btn btn-success tjlms_html_belonging_after_upload <?php echo $preview_class;?>"
		onclick="openNativebuilder(this,'edit')"><?php echo JText::_("PLG_TEXTMEDIA_NATIVEBUILDR_HTML_CONTENT");?></a>
	</div>
</div>

<script type="text/javascript">
/*open htmlcontentbuilder*/
function openNativebuilder(thislink,action)
{
	var format_form	=	techjoomla.jQuery(thislink).closest(".lesson-format-form");
	var format_form_id	=	techjoomla.jQuery(format_form).attr("id");
	var form_id	=	format_form_id.replace("lesson-format-form_","")

	var lesson_id	=	techjoomla.jQuery("#lesson_id",format_form).val();



	var content_link = "<?php echo JUri::root();?>"+"administrator/index.php?option=com_tjlms&view=modules&layout=formathelper&plgType=tjtextmedia&plgName=nativeeditor&plgtask=getpluginHtml&sub_layout=build&callType=1&tmpl=component&creator_id="+<?php echo $user_id?>+"&form_id="+ form_id +"&lesson_id=" + lesson_id +"&action="+ action;

	/*var content_link = root_url+
						"index.php?option=com_tjlms&view=lesson&form_id="+ form_id +"&lesson_id=" + lesson_id +
						"&action="+ action +"&layout=default_html&sub_layout=creator&pluginToTrigger=' . $plugin_name . '&user_id=' . $user_id . '&tmpl=component" ;-*/

	var wwidth = techjoomla.jQuery(window).width()-10;
	var wheight = techjoomla.jQuery(window).height()-10;

	SqueezeBox.open(content_link, {
			handler: "iframe",
			size: {x: wwidth, y: wheight},
			closable:false,
			onOpen:function() {
				techjoomla.jQuery("#sbox-btn-close").hide();
			},
			onClose:function() {
				console.log("close");
			}
		});
}

function validatetextmedianativeeditor(formid,format,subformat,media_id)
{
	var res = {check: 1, message: ""};

	var format_lesson_form = techjoomla.jQuery("#lesson-format-form_"+ formid);

	if(media_id == 0)
	{
		if (!techjoomla.jQuery("#lesson_format #" + format + " #lesson_format_id",format_lesson_form).val())
		{
			res.check = '0';
			res.message = "<?php echo JText::_('PLG_TJTEXTMEDIA_NATIVEEDITOR_SOURCE_MISSING');?>";
		}
	}

	return res;
}
</script>

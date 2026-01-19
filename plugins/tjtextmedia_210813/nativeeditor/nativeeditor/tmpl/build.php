<?php
/**
 * @version    SVN: <svn_id>
 * @package    Com_Tjlms
 * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

defined('_JEXEC') or die('Restricted access');

$html = '';
$input       = JFactory::getApplication()->input;
$conf        = JFactory::getConfig();
$editor_name = $conf->get('editor');
$lesson_id   = $input->get('lesson_id', '0', 'INT');
$form_id     = $input->get('form_id', '0', 'STRING');

$media_id = 0;

if ($lesson_id)
{
	if (isset($vars['media_id']))
	{
		$media_id = $vars['media_id'];

		// $this->lesson_data->media_id;
	}
}

//if ($input->get('action', '', 'string') == 'edit' || $input->get('action', '', 'string') == 'add')
//{
	if (isset($vars['source']))
	{
		$html = $vars['source'];

		// $this->lesson_typedata->source;
	}
//}

// Print_r($html); die;
?>

<div class="<?php echo COM_TJLMS_WRAPPER_DIV;?>">
<form id="nativeeditor" method="post" >
	<div id="tjlmscontainer">
		<div class="htmltoolbar" >
			<div class="left">
				<button onclick="save('save')" class="btn btn-small btn-primary"> <?php
echo JText::_("COM_TJLMS_SAVE");
?> </button>
				<button onclick="save('saveclose')" class="btn btn-small btn-success"> <?php
echo JText::_("COM_TJLMS_SAVE_CLOSE");
?> </button>
				<button onclick="closepopup()" class="btn btn-small btn-danger"> <?php
echo JText::_("COM_TJLMS_CLOSE");
?> </button>
			</div>
		</div>
		<div id="contentarea" class="container">
			<?php
if (!$html)
{
	ob_start();
	include $vars['template'];
	$html = ob_get_contents();
	ob_end_clean();
}
?>
			   <?php
$editor = JFactory::getEditor();
$params = array( 'smilies'=> '0' ,
                 'style'  => '1' ,
                 'layer'  => '0' ,
                 'table'  => '0' ,
                 'clear_entities'=>'0'
                 );
echo $editor->display("contentbuilder", $html, 670, 600, 60, 20, true, null,null,null,$params);
?>
		   <input type="hidden" id="currentmedia_id" name="currentmedia_id" value="<?php
echo $media_id;
?> " />
			<input type="hidden" id="user_id" value="<?php
echo $vars['creator_id'];
?>" />
			<input type="hidden" id="lesson_id" value="<?php
echo $lesson_id;
?>" />
			<input type="hidden" id="media_id" value="<?php
echo $media_id;
?>" />

			<input type="hidden" id="actionToPerform" value="" />
		</div>
	</div>
</form>
</div>


<script type="text/javascript">

	/* Cancel Function to close modal popup */
	function closepopup()
	{
		window.parent.SqueezeBox.close();
	}

	/*  Save , Save and close functionality is done here */
	function save(action) {
		/* Save the action ..Used later in ajax */
		techjoomla.jQuery('#actionToPerform').val(action);

		/* Loading image to be shown during saving functioality is done completely */
		loadingImage();
		/* Get Content */
		var editor="<?php echo $editor_name; ?>";

		if(editor=='tinymce' ||  editor=='jce')
		{
			var sHTML = techjoomla.jQuery("iframe").contents().find("body#tinymce").html();
		}
		else if (editor == 'none' )
		{
			var sHTML = techjoomla.jQuery('textarea[name="contentbuilder"]').val();
		}
		else
		{
			var sHTML = techjoomla.jQuery("iframe").contents().find("body").html(); //cke_show_borders
		}

		/* Get media id */
		var media_id = techjoomla.jQuery('#currentmedia_id').val();

		/* Save Content */
		techjoomla.jQuery.ajax({
			url: "<?php echo JUri::root();?>"+"index.php?option=com_tjlms&task=callSysPlgin&plgType=tjtextmedia&plgName=<?php
echo $vars['plgname'];
?>&plgtask=saveHtmlContent",
			type: "POST",
			data: {
				user_id: <?php echo $vars['creator_id'];?>,
				lesson_id: <?php echo $lesson_id;?>,
				media_id: media_id,
				htmlcontent: sHTML
			},
			dataType: "JSON",
			async:false,
			success: function(data) {

				/* pass the media ID to parent window. Used in lesson saving */
				//window.parent.techjoomla.jQuery("input[name=media_id]").val(data);
				window.parent.techjoomla.jQuery("#lesson-format-form_<?php
echo $form_id;
?> #lesson_format_id").val(data);
				window.parent.techjoomla.jQuery("#lesson-format-form_<?php
echo $form_id;
?> .tjlms_html_belonging_before_upload").hide();
				window.parent.techjoomla.jQuery("#lesson-format-form_<?php
echo $form_id;
?> .tjlms_html_belonging_after_upload").show();
				window.parent.techjoomla.jQuery("#lesson-format-form_<?php
echo $form_id;
?> .tjlms_html_lesson_preview iframe").attr('src', root_url+ "index.php?option=com_tjlms&view=lesson&tmpl=component&lesson_id=<?php
echo $lesson_id;
?>&mode=preview&attempt=1&fs=1");

				var actionToPerform = techjoomla.jQuery('#actionToPerform').val();

				if (actionToPerform == 'saveclose')
				{

					window.parent.SqueezeBox.close();
					//window.parent.document.location.reload(true);
				}
				else
				{
					alert('Saved successfully');
					techjoomla.jQuery("#currentmedia_id").val(data);
				}

			},
			error: function(jqXHR, textStatus, errorThrown)
			{
				status.setMsg(jqXHR.responseText,'alert-error');
			}
		});
	}

	/* Function to load the loading image. */
	function loadingImage()
	{
		techjoomla.jQuery('<div id="appsloading"></div>')
		.css("background", "rgba(255, 255, 255, .8) url('"+root_url+"components/com_tjlms/assets/images/ajax.gif') 50% 15% no-repeat")
		.css("top", techjoomla.jQuery('#tjlmscontainer').position().top - techjoomla.jQuery(window).scrollTop())
		//.css("left", techjoomla.jQuery('#contentarea').position().left - techjoomla.jQuery(window).scrollLeft())
		.css("width", techjoomla.jQuery('#tjlmscontainer').width())
		.css("height", techjoomla.jQuery('#tjlmscontainer').height())
		.css("position", "fixed")
		.css("z-index", "1000")
		.css("opacity", "0.80")
		.css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity = 80)")
		.css("filter", "alpha(opacity = 80)")
		.appendTo('#tjlmscontainer');
	}

	/* Function to close the loading image. */
	function hideImage()
	{
		techjoomla.jQuery('#appsloading').remove();
	}

</script>

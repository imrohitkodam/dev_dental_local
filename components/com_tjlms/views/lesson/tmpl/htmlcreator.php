<?php
/**
 * @package Tjlms
 * @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     http://www.techjoomla.com
 */
defined('_JEXEC') or die('Restricted access');

include_once JPATH_ROOT.DS.'administrator/components/com_tjlms/js_defines.php';

$html = '';


// Load CSS and JS for content builder
$document = JFactory::getDocument();
$document->addScript(JUri::root(true).'/components/com_tjlms/assets/htmlbuilder/scripts/jquery-ui.min.js');
$document->addScript(JUri::root(true).'/components/com_tjlms/assets/htmlbuilder/scripts/contentbuilder.js');
$document->addScript(JUri::root(true).'/components/com_tjlms/assets/htmlbuilder/scripts/saveimages.js');
$document->addStyleSheet(JUri::root(true).'/components/com_tjlms/assets/htmlbuilder/assets/default/content.css');
$document->addStyleSheet(JUri::root(true).'/components/com_tjlms/assets/htmlbuilder/scripts/contentbuilder.css');
// Sniffet file path
$sniffetFilePath = JUri::root(true).'/components/com_tjlms/assets/htmlbuilder/assets/default/snippets.html';

$input = JFactory::getApplication()->input;

$lesson_id = $input->get('lesson_id','0','INT');
$form_id	=	$input->get('form_id','0','STRING');
$user_id = JFactory::getUser()->id;
$media_id = 0;

if ($lesson_id)
{
	$media_id = $this->lesson_data->media_id;
}
if($input->get('action','','string') == 'edit')
{
	if (isset ($this->lesson_typedata->source))
	{
		$html = $this->lesson_typedata->source;
	}
}

//print_r($html); die;
?>

<div class="<?php echo COM_TJLMS_WRAPPER_DIV; ?>">

	<div id="tjlmscontainer">
		<div class="htmltoolbar" >
		<div class="left">
			<button onclick="save('save')" class="btn btn-midium btn-primary"> <?php echo JText::_("COM_TJLMS_SAVE");?> </button>
			<button onclick="save('saveclose')" class="btn btn-midium btn-success"> <?php echo JText::_("COM_TJLMS_SAVE_CLOSE");?> </button>
			<button onclick="closepopup()" class="btn btn-midium btn-danger"> <?php echo JText::_("COM_TJLMS_CLOSE");?> </button>
		</div>
	</div>
		<div id="contentarea" class="container">
			<?php if ($html): ?>
				<?php echo $html; ?>
			<?php else:
				echo $this->loadTemplate('htmlcreator_template');
			?>
			<?php endif; ?>

			<input type="hidden" id="currentmedia_id" name="currentmedia_id" value="<?php echo $media_id; ?> " />
			<input type="hidden" id="actionToPerform" value="" />
		</div>
	</div>
</div>


<script type="text/javascript">

	techjoomla.jQuery(document).ready(function () {

		techjoomla.jQuery("#contentarea").contentbuilder({
			zoom: 0.85,
			snippetFile: '<?php echo $sniffetFilePath; ?>'
		});

		/* To get the sniffet toolbar auto open */
		techjoomla.jQuery( "#lnkToolOpen" ).trigger( "click" );

	});

	techjoomla.jQuery(window).scroll(function(e){

		/* To get the action toolbar fixed at top position */
		$el = techjoomla.jQuery('.htmltoolbar');

		if (techjoomla.jQuery(this).scrollTop() > 50 && $el.css('position') != 'fixed'){
			techjoomla.jQuery('.htmltoolbar').css({'position': 'fixed', 'top': '0px'});
		}
		if (techjoomla.jQuery(this).scrollTop() < 50 && $el.css('position') == 'fixed')
		{
			techjoomla.jQuery('.htmltoolbar').css({'position': 'static', 'top': '0px'});
		}
	});

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

		/* Save Images */
		techjoomla.jQuery("#contentarea").saveimages({
			handler: 'index.php?option=com_tjlms&task=lesson.saveHtmlImages',
			onComplete: function () {

				/* Get Content */
				var sHTML = techjoomla.jQuery('#contentarea').data('contentbuilder').html();

				/* Get media id */
				var media_id = techjoomla.jQuery('#currentmedia_id').val();

				/* Save Content */
				techjoomla.jQuery.ajax({
					type: "POST",
					url: "index.php?option=com_tjlms&task=lesson.saveHtmlContent",
					data: {
						user_id: <?php echo $user_id; ?>,
						lesson_id: <?php echo $lesson_id; ?>,
						media_id: media_id,
						htmlcontent: sHTML
					},
					dataType: "JSON",
					async:false,
					success: function(data) {

						/* pass the media ID to parent window. Used in lesson saving */
						//window.parent.techjoomla.jQuery("input[name=media_id]").val(data);
						window.parent.techjoomla.jQuery("#lesson-format-form_<?php echo $form_id ; ?> #lesson_format_id").val(data);
						window.parent.techjoomla.jQuery("#lesson-format-form_<?php echo $form_id; ?> .tjlms_html_belonging_before_upload").hide();
						window.parent.techjoomla.jQuery("#lesson-format-form_<?php echo $form_id; ?> .tjlms_html_belonging_after_upload").show();
						window.parent.techjoomla.jQuery("#lesson-format-form_<?php echo $form_id; ?> .tjlms_html_lesson_preview iframe").attr('src', root_url+ "index.php?option=com_tjlms&view=lesson&tmpl=component&lesson_id=<?php echo $lesson_id; ?>&mode=preview&attempt=1&fs=1");

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
					error: function(){
						console.log("something is wrong..");
					}

				});

				/* Close the hidding image once functionality is complete. */
				hideImage();

			}
		});

		techjoomla.jQuery("#contentarea").data('saveimages').save();

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

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

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.pane');
jimport( 'joomla.html.html.behavior' );

JHtml::_('behavior.keepalive');

$doc =JFactory::getDocument();
$doc->setMetaData( 'viewport', 'width=device-width, initial-scale=1' );
$jinput = JFactory::getApplication()->input;
$jinput->set('tmpl', 'component');

$close = $jinput->get('close', '', 'INT');

// If invalid url, throw error
if ($this->inValidUrl == 1)
{
	?>
		<div class="alert alert-danger">
			<span><?php echo JText::_('COM_TJLMS_LESSON_INVALID_URL');?></span>
		</div>
	<?php
	return;
}

// Get lesson data
$lesson_data = $this->lesson_data;

if ((strtotime($lesson_data->start_date) > strtotime(JFactory::getDate())) && $this->mode != 'preview')
{ ?>
	<div class="alert alert-warning"><?php echo JText::_('JNOTPUBLISHEDYET'); ?></div>
	<?php
	 return false;
}

if ((strtotime($lesson_data->end_date) < strtotime(JFactory::getDate())) && $this->mode != 'preview' && $lesson_data->end_date != JFactory::getDbo()->getNullDate())
{ ?>
			<span class="label label-warning"><?php echo JText::_('JEXPIRED'); ?></span>
<?php
}

// If invalid url, throw error
if ($this->usercanAccess['access'] == 0)
{
	?>
		<div class="alert alert-danger">
			<span><?php echo $this->usercanAccess['msg'];	?></span>
		</div>
	<?php
	return;
}

$lesson_url = $this->tjlmshelperObj->tjlmsRoute("index.php?option=com_tjlms&view=lesson&lesson_id=" . $lesson_data->id . "&tmpl=component&lessonscreen=1",false);

$params = JComponentHelper::getParams('com_tjlms');

// Jlike toolbar position
$show_toolbar_at_top = $params->get('tjlms_toolbar_option','1');

$jlike_toolbar_class = "jliketoolbartop";
$lesson_container_toolbar_class = "container-bottom";

if ($show_toolbar_at_top == 0)
{
	$jlike_toolbar_class = "jliketoolbarBottom";
	$lesson_container_toolbar_class = "container-top";
}

$toolbar_content_class = 'lesson-right-panel';

// Get jlike toolbar
$jlike_toolbar_file = $this->tjlmshelperObj->getViewpath('com_tjlms', 'lesson','jlike_toolbar');
ob_start();
include($jlike_toolbar_file);
$toolbar_html = ob_get_contents();
ob_end_clean();

// Get jlike toolbar content
$jlike_toolbar_content_file = $this->tjlmshelperObj->getViewpath('com_tjlms', 'lesson','jlike_toolbar_content');
ob_start();
include($jlike_toolbar_content_file);
$toolbar_content_html = ob_get_contents();
ob_end_clean();
?>

<?php $resumeWindowClass = ' '; ?>

<?php if($this->askforinput	== 1):
		$resumeWindowClass = 'resumeWindowPage';
endif; ?>

<!-- Container div-->
<div class="<?php echo COM_TJLMS_WRAPPER_DIV; ?> row-fluid com_tjlms_content">

	<?php if ($this->mode == 'preview' && $close != '0') { ?>
		<div>
			<button type="button" class="close" onclick="closePopup(1);"; data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
		</div>
<?php } ?>

<div id="tjlms-lesson-content" class="tjlms-lesson-content row-fluid <?php echo COM_TJLMS_WRAPPER_DIV; ?> <?php echo $resumeWindowClass . '  ' .  $lesson_container_toolbar_class; ?>">



	<!-- If playlist is enable-->
	<?php if ($this->showPlaylist == 1 && $this->mode != 'preview') : ?>

		<div class="lesson-left-panel">
			<?php
				echo $this->loadTemplate('playlist');
			?>
		</div>

		<?php $lessonMainContainerClass = 'span12';	?>

	<?php else: ?>
		<?php $lessonMainContainerClass = ' ';	?>
	<?php endif; ?>

	<!-- main container changes -->
	<div id="lesson-main-container" class="lesson-toggle-main lesson-toggle-transition expanded <?php echo $lessonMainContainerClass; ?>">

		<?php if($this->askforinput	== 1): ?>
			<div id="resumeWindow" >
				<div class="well" id="askforattempt">
					<i class="icon-remove resumewindowclose pull-right" onclick = "closePopup('<?php echo $this->launch_lesson_full_screen;?>','<?php echo $this->courseDetailsUrl;?>');"></i>
					<div id="">
						<span class="help-block"><?php echo JText::_('COM_TJLMS_INCOMPLETE_LAST_ATTEMPT_MSG'); ?>
						<?php
						if($lesson_data->format!='scorm' && $lesson_data->format!='tjscorm')
						{
							$lang_constant_toshow	=	"COM_TJLMS_INCOMPLETE_LAST_ATTEMPT_STATUS_".$lesson_data->format;
							 echo JText::sprintf($lang_constant_toshow, $this->lastattempttracking_data->current_position, $lesson_data->name, $this->lastattempttracking_data->total_content);
						}
						?>
						</span>
					</div>
					<div class="clearfix"></div>

					<div class="row-fluid">

						<input type="button" name="new" value="<?php echo JText::_('COM_TJLMS_NEW_ATTEMPT') ?>" class="btn  resumebtncolor btn-medium span6" onclick="askforaction('start','<?php echo $lesson_data->id; ?>','<?php echo $lesson_url?>','<?php echo $this->attempt; ?>','<?php echo $lesson_data->format; ?>');">

						<input type="button" id="old" name="old" value="<?php echo JText::_('COM_TJLMS_CONTINUE_OLD') ?>" class="btn resumebtncolor btn-medium span6" onclick="askforaction('resume','<?php echo $lesson_data->id; ?>','<?php echo $lesson_url?>','<?php echo $this->attempt; ?>','<?php echo $lesson_data->format; ?>');">

					</div>
				</div><!--askforattempt ENDS-->
			</div><!-- resumeWindow ENDS -->

		<!-- If resume window... return from here-->
		<?php else: ?>
		<!-- Get lesson view along with jliketooblbar-->
			<!--JLIKE TOOLBAR-->
			<?php if($this->mode != 'preview' && $lesson_data->format != 'tmtQuiz'): ?>
						<?php echo $toolbar_html; ?>
			<?php endif; ?>
			<!--JLIKE TOOLBAR ENDS-->

			<div class="main-lesson tjlms-lesson-player">
				<!-- Lesson format-->
				<?php echo $this->loadTemplate(strtolower($lesson_data->format));	?>
			</div>

			<div class="right-panel span4 right-panel-hidden <?php echo $toolbar_content_class; ?>">
				<!-- If toolbar content position is at the bottom-->
				<?php if($this->mode != 'preview' && $lesson_data->format != 'tmtQuiz'): ?>
						<?php echo $toolbar_content_html; ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>


</div><!--Container div ENDS-->

</div>
<script>
techjoomla.jQuery(document).ready(function(){
		SetHeight();
		<?php if($this->askforinput	!= 1){	?>
			loadingImage("tjlms-lesson-content", "1");
		<?php } ?>
	});

techjoomla.jQuery(window).load(function (){

	// hide the scroll of the parent window if lesson is opened in lightbox
	<?php if($this->launch_lesson_full_screen == 1 && $this->mode != 'preview') { ?>
		window.parent.document.body.style.overflow="hidden";
	<?php } ?>

	if(techjoomla.jQuery(window).width() < 767)
	{
		techjoomla.jQuery('.lesson-left-panel').insertAfter("#lesson-main-container");
		techjoomla.jQuery('.playlist-container').removeClass("playlist-hidden");
	}

	techjoomla.jQuery( ".closeBtn , .resumewindowclose" ).click(function()
	{
		if (typeof lessonStartTime != 'undefined')
		{
			var lessonStoptime = new Date();
			var timespentonLesson = lessonStoptime - lessonStartTime;
			var timeinseconds = Math.round(timespentonLesson / 1000);
			plugdataObject["time_spent"] = timeinseconds;
			updateData(plugdataObject);
		}

		closePopup("<?php echo $this->launch_lesson_full_screen;?>","<?php echo $this->courseDetailsUrl;?>");
	});

	techjoomla.jQuery("#toolbar_expander").click(function()
	{
		techjoomla.jQuery('#jlikeToolbar').toggleClass('toolbar-expanded');


		if (techjoomla.jQuery(".toolbar_buttons.active").length > 0)
		{

			techjoomla.jQuery(".toolbar_buttons.active").each(function() {
				techjoomla.jQuery(this).trigger('click');
			});
		}
	});

	techjoomla.jQuery( ".toolbar_buttons" ).not( ".closeBtn" ).click(function()
	{
		if(techjoomla.jQuery(this).hasClass('active'))
		{
			techjoomla.jQuery(this).removeClass('active');
			toggleJlikePanel();
			return;
		}

		techjoomla.jQuery('.toolbar_buttons').removeClass('active');
		techjoomla.jQuery('.lesson-right-panel-child').hide();
		techjoomla.jQuery(this).addClass('active');

		var toolbarbutton	= techjoomla.jQuery(this).attr('id');
		techjoomla.jQuery('.main-lesson').addClass('span8');
		techjoomla.jQuery('.right-panel').removeClass('right-panel-hidden');
		techjoomla.jQuery('#'+toolbarbutton +'Div').show();
		techjoomla.jQuery('#'+toolbarbutton +'Div .catch-error').hide().html('');


		if(techjoomla.jQuery(window).width() < 767)
		{
			techjoomla.jQuery('.lesson-right-panel').insertBefore( ".tjlms-lesson-player" );
		}
	});
});
</script>

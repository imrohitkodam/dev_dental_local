<?php
/**
 * @version    SVN: <svn_id>
 * @package    Tjlms
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
/*layout for Image & text ads only (ie. title & img & decrip)
this will be the default layout for the module/zone
*/
jimport('joomla.html.html');
jimport('joomla.utilities.string');

?>
<?php
$donotshowtrackvar	=	"";
if($this->trackCourse == 0)
	$donotshowtrackvar	=	"style='display:none'";
?>

<script>
jQuery(document).ready(function() {
	/*var moduleId = <?php echo $this->openModuleId; ?>;*/
	var width = jQuery(window).width();
	var height = jQuery(window).height();
	jQuery('a.attempt_report').attr('rel','{handler: "iframe", size: {x: '+(width-(width*0.10))+', y: '+(height-(height*0.10))+'}}');

	/*if (moduleId){
		toggleModuleAccordion(moduleId);
	}*/
});
</script>
<div class="tjlms-course-toc pt-10">
	<h4> <?php echo JText::_("COM_TJLMS_TOC_HEAD_CAPTION");?></h4>
	<hr class="tjlms-hr-dark mt-10">

<?php if($this->lesson_count == 0) : ?>

	<div class="alert alert-warning">
		<?php	echo JText::_('TJLMS_NO_LESSON_PRESENT'); ?>
	</div>

<?php endif; ?>

<?php $modules_data = $this->module_data; ?>

	<div class="panel-group" id="accordion">

<?php foreach ($modules_data as $module_data): ?>

	<?php	if ($this->modules_present > 1 && !empty($module_data->lessons)): ?>

		<div class="panel panel-default border-0" id="modlist_<?php	echo $module_data->id;?>">
			<div class="cursor-pointer panel-heading collapsed border-0" data-jstoggle="collapse" data-target="#collapse_<?php echo $module_data->id;?>" aria-expanded="false">
				<h5 class="panel-title accordion-toggle">
					<a class="d-inline-block">
						<i class="fa fa-book" aria-hidden="true"></i>
						<span><?php echo $module_data->name;	?></span>
					</a>
				</h5>
			</div>
			<div id="collapse_<?php	echo $module_data->id;?>" class="panel-collapse collapse">
				<div class="panel-body lessons-module">
	<?php endif; ?>
			<?php

				$report_link =	'index.php?option=com_tjlms&view=reports&layout=attempts&tmpl=component&course_id=' . $this->course_info->id;
			?>

			<?php foreach($module_data->lessons as $m_index => $m_lesson): ?>

				<?php
					$icon = JUri::root(true) . '/media/com_tjlms/images/default/icons/';
					$multi_scorm	=	0;
					$hovertext = $m_lesson->format;
					$hovertitle = " title='" . JText::_('COM_TJLMS_LAUNCH_LESSON_TOOLTIP') . "'";
					$lessonTitleClass = 'col-xs-9';

					if ($hovertext == 'tmtQuiz'):
						$hovertext = 'quiz';
						$hovertitle=" title='" . JText::_("COM_TJLMS_LAUNCH_QUIZ_TOOLTIP") . "'";

					endif;

					// Check id uploaded scorm is multi scorm
					if(isset($m_lesson->scorm_toc_tree) && !empty($m_lesson->scorm_toc_tree)):
						$multi_scorm	=	1;
					endif;

					if ($multi_scorm != 1):

						/*LANUCH button
						 * hovertitle  = POpover content
						 * disabled = if user has no access disable this
						 * active_btn_class = styling
						 * onclick =  Javscript
						 * lock_icon = for prerequisites
						 * */
						$disabled = $lock_icon = $launchButton = '';
						$active_btn_class = 'btn-small btn-primary tjlms-btn-flat';

						$lesson_url = $this->tjlmshelperObj->tjlmsRoute("index.php?option=com_tjlms&view=lesson&lesson_id=" . $m_lesson->id . "&tmpl=component", false);

						$onclick=	"open_lessonforattempt('" . addslashes(htmlspecialchars($lesson_url)) . "','" . $this->launch_lesson_full_screen ."');";

						$usercanAccess = $this->tjlmsLessonHelper->usercanAccess($m_lesson, $this->course_info, $this->oluser_id);

						if ($usercanAccess['access'] == 1)
						{
							$hovertitle = '';
							$active_btn_class = 'btn-small btn-primary';

							if ($m_lesson->format != "tmtQuiz")
							{
								$plg_type = 'tj' . $m_lesson->format;
								$format_subformat = !empty($m_lesson->sub_format) ? explode('.', $m_lesson->sub_format) : '';
								$plg_name = isset($format_subformat[0])?$format_subformat[0]:'';

								JPluginHelper::importPlugin($plg_type);
								$dispatcher = JDispatcher::getInstance();
								$launchButtonArray = $dispatcher->trigger('get' . $plg_name . 'LaunchButtonHtml', array($m_lesson));

								if (!$plg_name)
								{
									$hovertitle	=	" rel='popover' data-original-content='" . htmlentities(JText::_('COM_TJLMS_PLUGIN_DISABLED'), ENT_QUOTES) . " ' ";
									$onclick="";
									$active_btn_class = 'btn-small btn-disabled bg-grey';
									$lock_icon="<i class='fa fa-lock' aria-hidden='true'></i>";
								}
								elseif (!empty($launchButtonArray) && !empty($launchButtonArray[0]))
								{
									$launchButton = $launchButtonArray[0];
								}
							}
						}
						else
						{
							$hovertitle	=	" rel='popover' data-original-content='" . htmlentities($usercanAccess['msg'],ENT_QUOTES) . " ' ";

							$onclick="";
							$active_btn_class = 'btn-small btn-disabled bg-grey';
							$lock_icon="<i class='fa fa-lock' aria-hidden='true'></i>";
						}

						?>

				<?php endif; ?>

				<?php if (!empty($m_lesson->statusdetails)) : ?>

						<?php $lessonTitleClass = 'col-xs-12';?>

						<?php
							$attempts_done_by_available = $m_lesson->attemptsdonebyuser;

							if ($m_lesson->no_of_attempts > 0):

								$attempts_done_by_available .= " / " . $m_lesson->no_of_attempts;

							else:

								$attempts_done_by_available .= " / " . JText::_("COM_TJLMS_LABEL_UNLIMITED_ATTEMPTS");
								$m_lesson->no_of_attempts = JText::_('COM_TJLMS_LABEL_UNLIMITED_ATTEMPTS');

							endif;

							if ($m_lesson->attemptsdonebyuser > 0) :

								$reportLink = $this->tjlmshelperObj->tjlmsRoute($report_link . '&lesson_id=' . $m_lesson->id, false);

								$attemptool = JText::sprintf("COM_TJLMS_ATTEMPTS_DONE_TOOLTIP", $m_lesson->completed_atttempts, $m_lesson->no_of_attempts);

								$popover_con = "<div>Completed attempts:".$m_lesson->completed_atttempts."</div><div>Total attempt:".$m_lesson->attemptsdonebyuser."</div>";


								$detailed_attempts_report_link = "<a class='tjmodal attempt_report' href='" . $reportLink . "' bpl='popover' data-placement='right' data-original-content='". htmlentities($popover_con, ENT_QUOTES) ."'>". $attempts_done_by_available ." </a>";

								$statusattpt = $detailed_attempts_report_link;

							else:

								$statusattpt = $attempts_done_by_available;

							endif;
						?>

				<?php endif; ?>

				<?php $completionClass = 'label-default';?>
				<?php if ($m_lesson->status == 'completed' || $m_lesson->status == 'passed' ): ?>
					<?php $completionClass = 'label-success';?>
				<?php endif;?>

				<?php if ($m_lesson->status == 'incomplete'): ?>
					<?php $completionClass = 'label-warning';?>
				<?php endif;?>

				<?php if ($m_lesson->status == 'failed'): ?>
					<?php $completionClass = 'label-danger';?>
				<?php endif;?>

					<div id="<?php echo $m_lesson->alias; ?>" class="container-fluid">

						<div class="row">

							<?php if ($m_index != 0):?>
								<hr>
							<?php endif; ?>

							  <?php
									if ($launchButton && isset($launchButton['html'])):

										echo $launchButton['html'];

										if ($launchButton['supress_lms_launch'] == 0):
											$tjlms_launch = 1;
										endif;

										else:

											$tjlms_launch = 1;
									endif;
							 ?>

							<div class="tjlms_toc__lesson-title <?php echo $lessonTitleClass; ?>">

								<img class="d-inline" alt="<?php echo $m_lesson->format; ?>" title="<?php echo ucfirst($hovertext); ?>" src="<?php echo JUri::root(true).'/media/com_tjlms/images/default/icons/'.$m_lesson->format.'.png';?>"/>

								<?php	echo ucfirst($m_lesson->title);?>
								<?php if( $m_lesson->consider_marks)
										{?>
											<span><img style="height: 25px;" title="Required to obtain a certificate." src="<?php echo JUri::root(true).'/templates/yoo_avenue/images/certificate.png';?>"></span>
								<?php 	}?>
								<span class="label <?php echo $completionClass;?> ml-10">
									<?php echo JText::_("COM_TJLMS_LESSON_STATUS_" . strtoupper($m_lesson->status)); ?>
								</span>

							</div>

					<?php if ($tjlms_launch == 1 && empty($m_lesson->statusdetails)): ?>
							<div class="col-xs-3">
								<?php if($m_lesson->format != "event" || ($m_lesson->format == "event" && empty($this->oluser_id))){ ?>
								<button <?php echo $hovertitle; ?> class="br-0 btn <?php echo $active_btn_class; ?>" onclick="<?php echo $onclick?>"><?php echo $lock_icon; ?>
									<small class="lesson_attempt_action hidden-xs hidden-sm">
									<?php echo JText::_("COM_TJLMS_LAUNCH"); ?>
									</small>
									<span class="glyphicon glyphicon-play <?php echo ($lock_icon) ? 'hidden' : 'visible-sm visible-xs';?>" aria-hidden="true"></span>
								</button>
								<?php }?>
							</div>

					<?php endif; ?>
						<!--tjlms_toc__lesson-title-->
						</div>

					<?php if( $this->checkifuserenroled == '' ||  $this->checkifuserenroled == 0): ?>

						<small class="row">
							<div class="long_desc">

								<?php
									if(strlen($m_lesson->description) > 75 )
									{
										echo nl2br($this->tjlmshelperObj->html_substr(htmlentities($m_lesson->description), 0, 75 )).'<a href="javascript:" class="r-more">...More</a>';
									}
									else
									{
										echo nl2br(htmlentities($m_lesson->description));
									}
								?>

							</div>
							<div class="long_desc_extend" style="display:none;">
								<?php
									echo nl2br(htmlentities($m_lesson->description)).'<a href="javascript:" class="r-less">...Less</a>';
								?>
							</div>
						</small><!--media-content-->

					<?php endif; ?>

					<?php if (!empty($m_lesson->statusdetails)): ?>

						<div class="row mt-10">
							<div class="col-xs-9 text-muted small">
								<div>
									<span><b><?php echo JText::_("COM_TJLMS_USER_STARTED_LESSON_ON");?></b>&nbsp;
										<?php echo $m_lesson->statusdetails->started_on; ?>
									</span>

									<span class="hidden-xs"><b><?php echo JText::_("COM_TJLMS_USER_LAST_ACCESSED_LESSON_ON");?></b>&nbsp;
										<?php echo $m_lesson->statusdetails->last_accessed_on; ?>
									</span>
								</div>
								<div>
									<span><b><?php echo JText::_("COM_TJLMS_USER_TOTAL_TIME_ON_LESSON");?></b>
										&nbsp;<?php echo $m_lesson->statusdetails->total_time_spent; ?>
									</span>
									<?php if (!($m_lesson->format == 'feedback')) : ?>
										<span class="hidden-xs"><b><?php echo JText::_("COM_TJLMS_TOC_HEAD_SCORE");?></b>
											&nbsp;<?php echo $m_lesson->score; ?>
										</span>
									<?php endif; ?>
									<br class="visible-xs">
									<span><b><?php echo JText::_("ATTEMPTS");?></b>
										&nbsp;<?php echo $statusattpt;?>
									</span>
								</div>
							</div>

						<?php if ($tjlms_launch == 1): ?>

							<div class="col-xs-3">
								<?php if($m_lesson->format != "event" || ($m_lesson->format == "event" && empty($this->oluser_id))){ ?>
								<button <?php echo $hovertitle; ?> class="br-0 btn <?php echo $active_btn_class; ?>" onclick="<?php echo $onclick?>"><?php echo $lock_icon; ?>
									<small class="lesson_attempt_action hidden-sm hidden-xs">
										<?php echo JText::_("COM_TJLMS_LAUNCH"); ?>
									</small>
									<span class="glyphicon glyphicon-play <?php echo ($lock_icon) ? 'hidden' : 'visible-sm visible-xs';?>" aria-hidden="true"></span>
								</button>
								<?php }?>
							</div>

						<?php endif;?>
						</div>

					<?php endif; ?>

					</div>
			<?php
			endforeach;


				if ($this->modules_present > 1 && !empty($module_data->lessons)): ?>
				</div>
			</div>
		</div>
		<hr class="mt-5 mb-5">
<?php endif; ?>
<?php endforeach;?>

	</div>

</div>

<script>
	jQuery(window).load(function () {

	jQuery('[rel="popover"]').on('click', function (e) {
		jQuery('[rel="popover"]').not(this).popover('hide');
	});

	jQuery('[rel="popover"]').popover({
		html: true,
		trigger: 'click',
		//container: this,
		placement: 'left',
		content: function () {
			return '<button type="button" id="close" class="close" onclick="popup_close(this);">&times;</button><div class="tjlms-toc-popover"><div class="tjlms-toc-content">'+jQuery(this).attr('data-original-content')+'</div></div>';
		}
	});
});

function popup_close(btn)
{
	var div = jQuery(btn).closest('.popover').hide();
}
</script>

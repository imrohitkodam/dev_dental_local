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

// No direct access.
defined('_JEXEC') or die;
?>
<div class="<?php echo COM_TJLMS_WRAPPER_DIV; ?>">
	<div class="playlist">
		<div class=" progressDiv">
			<div class="progress-pie-chart" data-percent="<?php echo $this->progress_in_percent; ?>"><!--Pie Chart -->
				<div class="ppc-progress">
					<div class="ppc-progress-fill"></div>
				</div>
				<div class="ppc-percents">
				<div class="pcc-percents-wrapper">
					<span>%</span>
				</div>
				</div>
			</div><!--End Chart -->
		</div>



		<?php $modules_data = $this->module_data; ?>

		<?php foreach ($modules_data as $module_data)
		{ ?>

			<?php if ($this->modules_present > 1)
			{ ?>

				<div class="playlist-module-name">
					<span><?php echo $module_data->name;	?></span>
					<div class="collapse-icon-containner">
						<b class="collapse-icon"></b>
					</div>
				</div>

			<?php
			}	?>


			<?php if (!$module_data->lessons)
			{	?>

				<div class="alert alert-warning lesson_title"><?php	echo JText::_('TJLMS_NO_LESSON_PRESENT');	?></div>

			<?php
			}
			else
			{	?>

				<?php $lessondetails_link =	'index.php?option=com_tjlms&view=lesson&layout=details&tmpl=component'; ?>
				<?php $report_link =	'index.php?option=com_tjlms&view=reports&layout=attempts&tmpl=component'; ?>

				<?php foreach ($module_data->lessons as $m_lesson)
				{

					$lessondetails_link .= "&lesson_id=" . $m_lesson->id;

					// Check id uploaded scorm is multi scorm
					$multi_scorm = 0;

					if (isset($m_lesson->scorm_toc_tree) && !empty($m_lesson->scorm_toc_tree))
					{
						$multi_scorm	=	1;
					}
						?>

					<div id="lessonlist_<?php echo $m_lesson->id; ?>" class="tjlms_lesson tjlms_lesson_<?php echo $module_data->id?>">
						<?php $hovertext = $m_lesson->format; 	?>

						<?php if ($hovertext == 'tmtQuiz')
							{
								$hovertext = 'Quiz';
							} ?>

							<?php
							$hovertitle = JText::_('COM_TJLMS_LAUNCH_TOOLTIP');

								if($multi_scorm != 1)
								{

									$disabled = '';
									$active_btn_class = ' tjlms-btn-small btn-yelloish-green';

									$lesson_url = JRoute::_("index.php?option=com_tjlms&view=lesson&lesson_id=" . $m_lesson->id . "&tmpl=component&Itemid=".$itemId,false);

									/*$onclick=	"open_lessonforattempt('" . $this->course_id . "','" . $m_lesson->id . "','"  . $m_lesson->format . "','" . $m_lesson->attemptsdonebyuser ."','" . $m_lesson->no_of_attempts. "','".$this->launch_lesson_full_screen ."');";*/

									$onclick=	"open_lessonforattempt('" . addslashes($lesson_url) . "','" . $this->launch_lesson_full_screen ."');";
									$lock_icon	=	'';

									if($m_lesson->eligible_toaceess != 1)
									{
										$disabled	=	'disabled';
										$active_btn_class = 'tjlms-btn-small btn-disabled';
										$onclick="";
										$lock_icon="<i class='fa fa-lock pull-right'></i>";
										//$hovertitle	=	JText::_("COM_TJLMS_NOT_COMPLETED_PREREQUISITES_TOOLTIP");
										$m_lesson->eligibilty_lessons = implode(',', $m_lesson->eligibilty_lessons);
										$hovertitle	=	JText::sprintf( 'COM_TJLMS_NOT_COMPLETED_PREREQUISITES_TOOLTIP', $m_lesson->eligibilty_lessons );
									}
									if( $m_lesson->no_of_attempts > 0 && ( $m_lesson->attemptsdonebyuser >= $m_lesson->no_of_attempts && $m_lesson->completed_last_attempt	== '1' ) ){
										$disabled	=	'disabled';
										$active_btn_class = 'tjlms-btn-small btn-disabled';
										$onclick="";
										$hovertitle	=	JText::_("COM_TJLMS_ATTEMPTS_EXHAUSTED_TOOLTIP");
									}
									if($m_lesson->format == 'tmtQuiz' && !$this->oluser_id){
										$disabled	=	'disabled';
										$active_btn_class = 'tjlms-btn-small btn-disabled';
										$hovertitle	=	JText::_("COM_TJLMS_GUEST_NOATTEMPT_QUIZ");
									}
									if ($this->checkifuserenroled != 1  && $this->usercanAccess == 0){
										$disabled	=	'disabled';
										$active_btn_class = 'tjlms-btn-small btn-disabled';
										$hovertitle	= '';
									}

									if ($m_lesson->free_lesson == 1)
									{
										$disabled	=	'';
										$active_btn_class = 'tjlms-btn-small btn-yelloish-green';
									}


									if (strtotime($m_lesson->start_date) > strtotime(JFactory::getDate()) && $m_lesson->end_date != '0000-00-00 00:00:00')
									{

										if($m_lesson->format == "tmtQuiz")
										{
											$hovertitle	=	JText::_("COM_TJLMS_QUIZ_NOT_PUBLISHED_YET");
										}
										else
										{
											$hovertitle	=	JText::_("COM_TJLMS_LESSON_NOT_PUBLISHED_YET");
										}
										$disabled	=	'disabled';
										$active_btn_class = 'tjlms-btn-small btn-disabled';
										$lock_icon="<i class='fa fa-lock pull-right'></i>";
										$onclick="";
									}
									else if ((strtotime($m_lesson->end_date) < strtotime(JFactory::getDate())) && $m_lesson->end_date != '0000-00-00 00:00:00')
									{
										if($m_lesson->format == "tmtQuiz")
										{
											$hovertitle	=	JText::_("COM_TJLMS_QUIZ_EXPIRED");
										}
										else
										{
											$hovertitle	=	JText::_("COM_TJLMS_LESSON_EXPIRED");
										}
										$disabled	=	'disabled';
										$active_btn_class = 'tjlms-btn-small btn-disabled';
										$lock_icon="<i class='fa fa-lock pull-right'></i>";
										$onclick="";
									}

									?>


									<div title="<?php echo $hovertitle; ?>" id="" class="tutorial-nav-node clearfix progress-container"  onclick="<?php echo $onclick?>">
											<div class="subway-icon">
												<div class="pipe"></div>
												<div class="pipe completed"></div>
												<div class="status">
												</div>
											</div>

											<span id="title_<?php echo $m_lesson->id;	?>" class="lesson_title progress-title"><?php	echo ucfirst($m_lesson->name);?>

												<?php echo $lock_icon; ?>
											</span>
									</div>
								<?php
								}	?>
					</div>
				<?php
				}	?>

			<?php
			} ?>

		<?php
		}	?>
	</div>
</div>

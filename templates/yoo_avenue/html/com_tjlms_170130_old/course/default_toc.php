<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
/*layout for Image & text ads only (ie. title & img & decrip)
this will be the default layout for the module/zone
*/
JHTML::_('behavior.modal');
jimport( 'joomla.html.html' );
?>
<?php
$donotshowtrackvar	=	"";
if($this->trackCourse == 0)
	$donotshowtrackvar	=	"style='display:none'";
?>

<script>

techjoomla.jQuery(document).ready(function() {
	var width = techjoomla.jQuery(window).width();
	var height = techjoomla.jQuery(window).height();
	techjoomla.jQuery('a.attempt_report').attr('rel','{handler: "iframe", size: {x: '+(width-(width*0.10))+', y: '+(height-(height*0.10))+'}}');

});
</script>
<div id="no-more-tables">
	<table class="table table-bordered tjlms_course_toc_listing no-margin no-padding unstyled_list tjlms-table" width="100%">
		<caption><h5> <?php echo JText::_("COM_TJLMS_TOC_HEAD_CAPTION");?></h5></caption>
		<thead>
			<tr>
				<?php

					$headNameWidth = '30%';
					$headStatusWidth = '35%';
					$headScoreWidth = '15%';
					$headActionWidth = '20%';

					if ($donotshowtrackvar)
					{
						$headNameWidth = '80%';
						$headActionWidth = '20%';
					}
				?>
				<th  width="<?php echo $headNameWidth; ?>"><?php echo JText::_("COM_TJLMS_TOC_HEAD_NAME");?></th>
				<th  width="<?php echo $headStatusWidth; ?>" <?php echo $donotshowtrackvar;?> ><?php echo JText::_("COM_TJLMS_TOC_HEAD_STATUS");?></th>
				<th  width="<?php echo $headScoreWidth; ?>" <?php echo $donotshowtrackvar;?>><?php echo JText::_("COM_TJLMS_TOC_HEAD_SCORE");?></th>
				<th  width="<?php echo $headActionWidth; ?>" ><?php echo JText::_("COM_TJLMS_TOC_HEAD_ACTION");?></th>
			</tr>
		</thead>
<?php if($this->lesson_count == 0) { ?>

		<tr class="tjlms_lesson_<?php echo $module_data->id?>"><td colspan=4><div class="alert alert-warning"><?php	echo JText::_('TJLMS_NO_LESSON_PRESENT');	?></div></td></tr>
	</table>
</div>

<?php return; } ?>

<?php $modules_data = $this->module_data; ?>

<?php
	foreach ($modules_data as $module_data)
	{
		// IF only one module present in the course do not show it
		if($this->modules_present > 1)
		{ ?>

			<tr id="modlist_<?php	echo	$module_data->id;	?>"  class="tjlms_module">
				<td  colspan="4" class="tjlms_section_title open"  title="<?php echo Jtext::_('TJLMS_EXPAND_MODULE')?>">
					<div class="tjlms_section_title_container">
						<span><?php echo $module_data->name;	?></span>
						<div class="collapse-icon-containner">
							<b class="collapse-icon"></b>
						</div>
					</div>
				</td>
			</tr>

<?php   } ?>

<?php if (!$module_data->lessons)
		{
?>
			<tr class="tjlms_lesson_<?php echo $module_data->id?>">
				<td colspan=4>
					<div class="alert alert-warning">
						<?php	echo JText::_('TJLMS_NO_LESSON_PRESENT');	?>
					</div>
				</td>
			</tr>

<?php   }
		else
		{
			$lessondetails_link =	'index.php?option=com_tjlms&view=lesson&layout=details&tmpl=component';
			$report_link =	'index.php?option=com_tjlms&view=reports&layout=attempts&tmpl=component&course_id=' . $this->course_info->id;

			foreach($module_data->lessons as $m_lesson)
			{
				$img_src = JUri::root(true) . '/media/com_tjlms/images/default/icons/';

				switch ($m_lesson->status)
				{
					case 'completed':
						$img_name = "completed.png";
						break;
					case 'passed':
						$img_name = "passed.png";
						break;
					case 'started':
						$img_name = "started.png";
						break;
					case 'incomplete':
						$img_name = "incomplete.png";
						break;
					case 'failed':
						$img_name = "failed_less.png";
						break;
					default:
						$img_name = "not_viewed.png";
						break;
				}
					$lessondetails_link .= "&lesson_id=".$m_lesson->id;

					// Check id uploaded scorm is multi scorm
					$multi_scorm	=	0;
					if(isset($m_lesson->scorm_toc_tree) && !empty($m_lesson->scorm_toc_tree))
					{
						$multi_scorm	=	1;
					}
?>
			<tr id="lessonlist_<?php echo $m_lesson->id; ?>" class="tjlms_lesson tjlms_lesson_<?php echo $module_data->id;?>">
<?php
					//$status_desc	=	$m_lesson->status_desc;

					// TO show lesson type on hover of the icons
					$hovertext = $m_lesson->format;
					if ($hovertext == 'tmtQuiz')
					{
						$hovertext = 'quiz';
					}
?>
					<td>
						<div class="tjlms_lesson-title">

							<img alt="<?php echo $m_lesson->format; ?>" title="<?php echo ucfirst($hovertext); ?>" src="<?php echo JUri::root(true).'/media/com_tjlms/images/default/icons/'.$m_lesson->format.'.png';?>"/>

							<?php	echo ucfirst($m_lesson->name);
							
							if( $m_lesson->consider_marks)
							{
							?>
								<span><img style="height: 25px;" title="Required to obtain a certificate." src="<?php echo JUri::root(true).'/templates/yoo_avenue/images/certificate.png';?>"></span>
							<?php 
							}
							//IF user is not enrolled for course show lesson description?>
							<?php if( $this->checkifuserenroled == '' ||  $this->checkifuserenroled == 0)
									{
								?>
										<div class="media-content">
											<div class="long_desc">
												<?php
													if(strlen($m_lesson->description) > 75 )
													{
														echo nl2br($this->tjlmshelperObj->html_substr($m_lesson->description, 0, 75 )).'<a href="javascript:" class="r-more">...More</a>';
													}
													else
													{
														echo nl2br($m_lesson->description);
													}
												?>
											</div>
											<div class="long_desc_extend" style="display:none;">
												<?php
													echo nl2br($m_lesson->description).'<a href="javascript:" class="r-less">...Less</a>';
												?>
											</div>
										</div><!--media-content-->
							<?php } ?>
						</div>
					</td>

					<td class="tjlms_lesson_status with-data-title" <?php echo $donotshowtrackvar;?> data-title="<?php echo JText::_("COM_TJLMS_TOC_HEAD_STATUS");?>" >
					<?php if (!empty($m_lesson->statusdetails))
					{
					?>
						<div><?php echo JText::_("COM_TJLMS_USER_STARTED_LESSON_ON") . ': ' . JFactory::getDate($m_lesson->statusdetails->started_on)->Format('jS F Y'); ?></div>
						<div><?php echo JText::_("COM_TJLMS_USER_LAST_ACCESSED_LESSON_ON") . ': '.JFactory::getDate($m_lesson->statusdetails->last_accessed_on)->Format('jS F Y');?></div>
						<div><?php if (JFactory::getDate($m_lesson->statusdetails->total_time_spent)->format('i') == '00')
						{
							$min = 0;
						}
						else
						{
							$min = JFactory::getDate($m_lesson->statusdetails->total_time_spent)->format('i');
						}

						if (JFactory::getDate($m_lesson->statusdetails->total_time_spent)->format('s') == '00')
						{
							$sec = 0;
						}
						else
						{
							$sec = JFactory::getDate($m_lesson->statusdetails->total_time_spent)->format('s');
						}

						if (JFactory::getDate($m_lesson->statusdetails->total_time_spent)->format('H') != '00')
						{
							$timetaken = JText::_("COM_TJLMS_USER_TOTAL_TIME_ON_LESSON") . ': ' . JFactory::getDate($m_lesson->statusdetails->total_time_spent)->format('H');
							$timetaken .= ' hours ' . $min . ' min ' . $sec . ' secs' . "\n";
						}
						else
						{
							$timetaken = JText::_("COM_TJLMS_USER_TOTAL_TIME_ON_LESSON") . ': ' . $min . ' min ' . $sec . ' secs' . "\n";
						}
						echo $timetaken;
						?>
						</div>
						<div>
						<?php
										$attempts_done_by_available = $m_lesson->attemptsdonebyuser;

						if ($m_lesson->no_of_attempts > 0)
						{
							$attempts_done_by_available .= " / " . $m_lesson->no_of_attempts;
						}
						else
						{
							$attempts_done_by_available .= " / " . JText::_("COM_TJLMS_LABEL_UNLIMITED_ATTEMPTS");
						}

						if ($m_lesson->attemptsdonebyuser > 0)
						{
							$report_link .= '&lesson_id=' . $m_lesson->id;

							if ($m_lesson->no_of_attempts == 0)
							{
								$m_lesson->no_of_attempts = JText::_('COM_TJLMS_LABEL_UNLIMITED_ATTEMPTS');
							}

							$attemptool = JText::sprintf("COM_TJLMS_ATTEMPTS_DONE_TOOLTIP", $m_lesson->completed_atttempts, $m_lesson->no_of_attempts);



							$popover_con = "<div>Completed attempts:".$m_lesson->completed_atttempts."</div><div>Total attempt:".$m_lesson->attemptsdonebyuser."</div>";


							$detailed_attempts_report_link = "<a class='modal attempt_report' href='" . $this->tjlmshelperObj->tjlmsRoute($report_link, false)."' bpl='popover' data-placement='right' data-original-content='". $popover_con."'>". $attempts_done_by_available ." </a>";

							echo JText::_("COM_TJLMS_LESSON_VIEWS") . ': ' . $statusattpt = $detailed_attempts_report_link;
						}
						else
						{
							echo $statusattpt = $attempts_done_by_available;
						}
						?>
						</div>

						<div>
							<?php echo JText::_("COM_TJLMS_LESSON_STATUS") . ': ' . ucfirst($m_lesson->status); ?>
						</div>
					<?php
					}
					else
					{
						echo JText::_("COM_TJLMS_LESSON_NOT_ACCESSED");
					}
					if ($m_lesson->format == 'event')
					{
						$event = $m_lesson->eventdetails;
						echo '<br/>' . $event['timer'];
					}
					?>

					</td>

					<td class="tjlmscenter" <?php echo $donotshowtrackvar;?> data-title="<?php echo JText::_("COM_TJLMS_TOC_HEAD_SCORE");?>">
						<span><?php echo $m_lesson->score; ?></span>
					</td>


					<td class="tjlmscenter" data-title="<?php echo JText::_("COM_TJLMS_TOC_HEAD_ACTION");?>">

					<!-- If lesson  is not  multisco then only show launch button-->

						<?php

						if ($multi_scorm != 1)
						{
							/*LANUCH button
							 * hovertitle  = POpover content
							 * disabled = if user has no access disable this
							 * active_btn_class = styling
							 * onclick =  Javscript
							 * lock_icon = for prerequisites
							 * */
							$hovertitle = " title='" . JText::_('COM_TJLMS_LAUNCH_LESSON_TOOLTIP') . "'";

							if($m_lesson->format == "tmtQuiz")
							{
								$hovertitle=" title='" . JText::_("COM_TJLMS_LAUNCH_QUIZ_TOOLTIP") . "'";
							}

							$disabled = '';
							$active_btn_class = 'btn-small btn-primary tjlms-btn-flat';

							$lesson_url = $this->tjlmshelperObj->tjlmsRoute("index.php?option=com_tjlms&view=lesson&lesson_id=" . $m_lesson->id . "&tmpl=component", false);

							$onclick=	"open_lessonforattempt('" . addslashes(htmlspecialchars($lesson_url)) . "','" . $this->launch_lesson_full_screen ."');";
							$lock_icon	=	'';


							$usercanAccess = $this->tjlmsLessonHelper->usercanAccess($m_lesson, $this->course_info, $this->oluser_id);

							if ($usercanAccess['access'] == 1)
							{
								$hovertitle = '';
								$active_btn_class = 'btn-small btn-primary';
							}
							else
							{
								$hovertitle	=	" rel='popover' data-original-content='" .$usercanAccess['msg'] . " ' ";

								$onclick="";
								$active_btn_class = 'btn-small btn-disabled';
								$lock_icon="<i " .$hovertitle. " class='icon-lock'></i>";
							}

							/*if(strtotime($m_lesson->start_date) <= strtotime(JHtml::date('now' , 'Y-m-d H:i:s', true)) || $m_lesson->start_date == '0000-00-00 00:00:00')
							{
								if ($m_lesson->free_lesson == 1)
								{
									$hovertitle = '';
									$active_btn_class = 'btn-small btn-primary';
								}
								else
								{
									if($m_lesson->eligible_toaceess != 1)
									{
										$active_btn_class = 'btn-small btn-disabled';
										$onclick="";
										$lock_icon="<i class='icon-lock'></i>";

										$m_lesson->eligibilty_lessons = implode(',', $m_lesson->eligibilty_lessons);
										if($m_lesson->format == "tmtQuiz")
										{
											$type=JText::_("COM_TJLMS_TYPE_QUIZ");
										}
										else
										{
											$type=JText::_("COM_TJLMS_TYPE_LESSON");
										}

										$hovertitle	=	' rel="popover" ' . 'data-original-content="' . JText::sprintf( 'COM_TJLMS_NOT_COMPLETED_PREREQUISITES_TOOLTIP', $type ,$m_lesson->eligibilty_lessons) . '"';
									}

									if( $m_lesson->no_of_attempts > 0 && ( $m_lesson->attemptsdonebyuser >= $m_lesson->no_of_attempts && $m_lesson->completed_last_attempt	== '1' ) )
									{
										$active_btn_class = 'btn-small btn-disabled';

										$onclick="";

										$hovertitle	=	" rel='popover' data-original-content='" . JText::_("COM_TJLMS_ATTEMPTS_EXHAUSTED_TOOLTIP") . " ' ";

									}

									if($m_lesson->format == 'tmtQuiz' && !$this->oluser_id)
									{
										$active_btn_class = 'btn-small btn-disabled';
										$onclick = "";
										$hovertitle	=	" rel='popover' data-original-content='" . JText::_("COM_TJLMS_GUEST_NOATTEMPT_QUIZ") . " ' ";
									}

									if ($this->checkifuserenroled != 1  && $this->usercanAccess == 0)
									{
										$onclick = "";
										$active_btn_class = 'btn-small btn-disabled';
										$hovertitle	=	" rel='popover' data-original-content='" . JText::_("COM_TJLMS_LOGIN_TO_ACCESS") . " ' ";
									}
								}

							}

							if (strtotime($m_lesson->start_date) > strtotime(JHtml::date('now' , 'Y-m-d H:i:s', true)) && $m_lesson->start_date != '0000-00-00 00:00:00')
							{

								$temp = "COM_TJLMS_NOT_PUBLISHED_YET_".strtoupper($m_lesson->format);
								$hovertitle	=	JText::_($temp);

								$hovertitle	=	" rel='popover' data-original-content='" . $hovertitle . " ' ";
								$active_btn_class = 'btn-small btn-disabled';

								$lock_icon="<i class='icon-lock'></i>";
								$onclick="";
							}
							elseif (strtotime($m_lesson->end_date) < strtotime(JHtml::date('now' , 'Y-m-d H:i:s', true)) && $m_lesson->end_date != '0000-00-00 00:00:00')
							{
								$temp = "COM_TJLMS_EXPIRED_".strtoupper($m_lesson->format);
								$hovertitle	=	JText::_($temp);

								$hovertitle	=	" rel='popover' data-original-content='" . $hovertitle . " ' ";
								$active_btn_class = 'btn-small btn-disabled';

								$lock_icon="<i class='icon-lock'></i>";
								$onclick="";
							}*/

							/* Validation for survey lesson */
							if ($m_lesson->format == 'survey')
							{
								if (!$this->oluser_id)
								{
									$active_btn_class = 'tjlms-btn-small btn-disabled';
									$onclick="";
									$hovertitle	=	" rel='popover' data-original-content='" . JText::_("COM_TJLMS_LOGIN_TO_ACCESS") . " ' ";
								}
							}
							/* Validation for survey lesson */

							/* Event lesson */
							if ($m_lesson->format == 'event')
							{
								if(JFactory::getUser()->id != 0 && $this->checkifuserenroled == 1 && $usercanAccess['access'] == 1)
								{
									echo $event['online_html'];
								}
								else
								{
										$active_btn_class = 'btn-disabled event-btn';
										$lock_icon="<i class='icon-lock'></i>";
										$onclick="";
										$hovertitle="";
										?>
										<button <?php echo $hovertitle; ?> class="btn <?php echo $active_btn_class; ?>" onclick="<?php echo $onclick?>"><?php echo $lock_icon; ?><span class="editlinktip hasTip"><?php echo JText::_("COM_TJLMS_TJEVENTS_ADOBECONNECT_ENTER_MEETINGS"); ?></span></button><?php
								}
							}
							/* Event lesson */
							?>
							<?php if ($m_lesson->format != 'event')
							{ ?>
							<button <?php echo $hovertitle; ?> class="btn <?php echo $active_btn_class; ?>" onclick="<?php echo $onclick?>"><?php echo $lock_icon; ?><span class="lesson_attempt_action"><?php echo JText::_("COM_TJLMS_LAUNCH"); ?></span></button>
							<?php } ?>
							<!--<button rel="popover" data-original-content="<?php echo $hovertitle; ?>" class="btn"  onclick="<?php echo $onclick?>"><?php echo $lock_icon; ?><span class="lesson_attempt_action"><?php echo JText::_("COM_TJLMS_LAUNCH"); ?></span></button>-->
					<?php } ?>

					</td>
				</tr>

				<?php if( ( $this->usercanAccess == 1  || ($this->course_info->type	==	1 && $m_lesson->free_lesson	==	1) ) && $multi_scorm == 1){ ?>

						<tr class="tjlms_lesson tjlms_lesson_scorm_toc tjlms_lesson_<?php echo $module_data->id?>">
							<td colspan=4>
								<?php
									$toc_tree	=	$m_lesson->scorm_toc_tree;

									$html_scorm_toc='';
									$layout = $this->tjlmshelperObj->getViewpath('com_tjlms','course','scorm_toc');
									ob_start();
									include($layout);
									$html_scorm_toc.= ob_get_contents();
									ob_end_clean();
									echo $html_scorm_toc;
								?>
							</td>
						</tr>

				<?php } ?>
				<?php } ?>
			<?php } ?>
	<?php } ?>
</table>
</div>

<script>
	jQuery(window).load(function () {
	jQuery('[rel="popover"]').popover({
		html: true,
		trigger: 'click',
		//container: this,
		placement: 'left',
		content: function () {
			return '<button type="button" id="close" class="close" onclick="popup_close();">&times;</button><div class="tjlms-toc-popover"><div class="tjlms-toc-content">'+jQuery(this).attr('data-original-content')+'</div></div>';
		}
	});
	jQuery('[bpl="popover"]').popover({
		html: true,
		trigger: 'hover',
		//container: this,
		placement: 'right',
		content: function () {
			return '<button type="button" id="close" class="close" onclick="popup_close();">&times;</button><div class="tjlms-toc-popover"><div class="tjlms-toc-content">'+jQuery(this).attr('data-original-content')+'</div></div>';
		}
	});
});

function popup_close()
{

	techjoomla.jQuery(".popover").remove();
}
</script>

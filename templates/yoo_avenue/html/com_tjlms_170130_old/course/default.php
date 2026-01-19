<?php
/**
 * @package TjLMS
 * @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     http://www.techjoomla.com
 */
	defined('_JEXEC') or die('Restricted access');
	JHTML::_('behavior.tooltip');
	JHTML::_('behavior.modal');

	$jinput            = JFactory::getApplication()->input;
	$course_id         = $jinput->get('id', '', 'INT');
	$db                = JFactory::getDBO();
	$enrolment_pending	= 0;
	if(!empty($this->course_info))
	{
		$course 			= $this->course_info;
	}
	$document			= JFactory::getDocument();
	$renderer			= $document->loadRenderer('module');
	$currency 			= $this->tjlmsparams->get('currency', '', 'STRING');
	$showbuy			= 1;
?>

<script>
	techjoomla.jQuery(document).ready(function() {
		techjoomla.jQuery('button').attr('disabled','disabled');
		techjoomla.jQuery('a').addClass('inactiveLink');

		if(techjoomla.jQuery(window).width() < 767)
		{
			techjoomla.jQuery('.enrollHtml').insertBefore( ".tjlms_course_toc" );
		}
	});

	techjoomla.jQuery(window).load(function() {
		techjoomla.jQuery('a').removeClass('inactiveLink');
		techjoomla.jQuery('button').removeAttr('disabled','disabled');
	});
</script>

<!--Wrapper DIV-->
<div class="<?php echo COM_TJLMS_WRAPPER_DIV; ?>">

	<?php if (empty($course)): ?>

			<!-- IF course state is not published-->
			<div class="alert alert-danger">
				<?php echo JText::_('COM_TJLMS_COURSE_DOES_NOT_EXISTS');?>
			</div>
		</div><!--Wrapper DIV Ends-->

		<?php return; ?>

	<?php endif; ?>

	<?php	if($course->state != 1): ?>

			<!-- IF course state is not published-->
			<div class="alert alert-danger">
				<?php echo JText::_('COM_TJLMS_COURSE_NOT_PUBLISHED');?>
			</div>
		</div><!--Wrapper DIV Ends-->

		<?php return; ?>

	<?php endif; ?>

	<!-- If a user is not authorized to view the course-->
	<?php if ($course->authorized == 0): ?>
			<?php if (!$this->oluser_id && $course->type == 1):	?>

						<div class="alert alert-danger">
							<?php echo JText::_('COM_TJLMS_LOGIN_MESSAGE');?>
						</div>

						<a href='<?php
							$uri = $_SERVER["REQUEST_URI"];
							$url = base64_encode($uri);
							echo 'index.php?option=com_users&view=login&return='.$url; ?>'>
							<div style="margin-left:auto;margin-right:auto;" class="control-group">
								<input id="LOGIN" class="btn btn-large btn-success validate" type="button" value="<?php echo JText::_('COM_TJLMS_LOGIN'); ?>">
							</div>
						</a>

			<?php else: ?>

				<div class="alert alert-danger"><?php	echo JText::_('TJLMS_NOT_AUTHORISED');	?></div>

			<?php endif; ?>

			</div><!--COM_TJLMS_WRAPPER_DIV ends-->

			<?php return;  ?>

	<?php endif; ?>


	<!--If course publish date is greater than current date then give unpublish message-->
	<?php if (strtotime($course->start_date) > strtotime(JFactory::getDate())) : ?>
		<div class="alert alert-warning"><?php echo JText::_('JNOTPUBLISHEDYET'); ?></div>

		</div><!--COM_TJLMS_WRAPPER_DIV ends-->
		<?php return; ?>

	<?php endif; ?>

	<div id="com_tjlms_course_content" class="com_tjlms_content com_tjlms_course_content">
		<?php
			if ($this->oluser->guest != 1)
			{
				if ($course->type == 1)
				{
					if (!empty($this->course_user_order_info) && ($this->course_user_order_info->status == 'C' || ($this->course_user_order_info->status == 'P' && $this->course_user_order_info->processor != '')))
					{
						if ($this->course_user_order_info->status == 'P')
						{
							?>
							<div class="alert alert-warning"><?php	echo JText::_('TJLMS_ORDER_PENDING_STATE');	?></div>
						<?php
						}
						elseif ($this->checkifuserenroled  === '0')
						{
							$enrolment_pending = 1 ;
							$showbuy = 0;
							?>

							<div class="alert alert-warning"><?php	echo JText::_('TJLMS_APPROVAL_REMAINING');	?></div>
						<?php
						}
						$showbuy = 0;
					}
					else
					{
						if (!empty($this->course_user_order_info) && $this->course_user_order_info->processor != '' && $this->course_user_order_info->status != 'I')
						{
							switch ($this->course_user_order_info->status)
							{
								case 'D':
									$orderStatus = JText::_('LMS_PSTATUS_DECLINED');
								break;
								case 'E':
									$orderStatus = JText::_('LMS_PSTATUS_FAILED');
								break;
								case 'UR':
									$orderStatus = JText::_('LMS_PSTATUS_UNDERREVIW');
								break;
								case 'RF':
									$orderStatus = JText::_('LMS_PSTATUS_REFUNDED');
								break;
								case 'CRV':
									$orderStatus = JText::_('LMS_PSTATUS_CANCEL_REVERSED');
								break;
								case 'RV':
									$orderStatus = JText::_('LMS_PSTATUS_REVERSED');
								break;
							}

							$showbuy = 1;

							?>
							<div class="alert alert-warning"><?php	echo JText::sprintf('TJLMS_ORDER_STATE', $orderStatus);	?></div>
							<?php
						}

						if (isset($this->course_user_order_info->status) && $this->course_user_order_info->status == 'I')
						{
							$showbuy = 1;
						}
					}

					// Check if subscription is expired
					if ($this->checkifuserenroled  == -2)
					{
						$enrolment_pending = 1 ; ?>

						<div class="alert alert-warning"><?php	echo JText::_('COM_TJLMS_SUBS_EXPIRED');	?></div>
					<?php
					}
					else if ($this->checkifuserenroled && (!isset($this->course_user_order_info->status) || empty($this->course_user_order_info->status)))
					{
						$enrolment_pending = 1;
						$this->usercanAccess = 0;
						$showbuy = 1;

						?>
						<div class="alert alert-warning"><?php	echo JText::_('COM_TJLMS_ADMIN_APPROVE_BUY_COURSE');	?></div>
					<?php
					}
				}
				else
				{
					if ($this->checkifuserenroled === '0')
					{
						$enrolment_pending = 1 ;
						$showbuy = 0;
						?>

						<div class="alert alert-warning"><?php	echo JText::_('TJLMS_APPROVAL_REMAINING');	?></div>
					<?php
					}
				}
			}
		?>

		<!-- Course details -->
		<div class="row-fluid">
			<?php
				$course_blocksHTML = '';
				$renderer	= $document->loadRenderer('module');
				$modules = JModuleHelper::getModules( 'tjlms_course_blocks' );

				ob_start();

				foreach ($modules as $module)
				{
					$attribs['style'] = 'xhtml';
					$course_blocksHTML .=  $renderer->render($module, $attribs);
				}

				ob_get_clean();

				$rightdiv_class = 'span8';
				$leftdiv_class = 'span4';

				// If user is not Enrolledn for the course show enrol or Buy now buttons
				if (!empty($this->checkifuserenroled) && empty($course_blocksHTML))
				{
					$rightdiv_class = 'span12';
					$leftdiv_class = '';
				}
			?>

			<div class="<?php echo $rightdiv_class; ?>">
				<!--Course image and desc -->

				<?php
					echo $this->loadTemplate('header');
				?>
				<!--Course image and desc ends -->

				<!--Course additional field block -->
				<?php
					if ($this->extraData) :
						echo $this->loadTemplate('extrafields');
					endif;
                ?>
				<!--Course additional field block ends-->

				<!--course TOC-->
				<div id="tjlms_course_toc" class="row-fluid tjlms_course_toc">
					<?php
							echo $this->loadTemplate('toc');
					?>
				</div><!--tjlms_course_toc ends-->

				<?php
					if (!empty($this->onAftercourseContent))
					{
						?>
						<hr class="hr hr-condensed">
						<div class="courseComment">
							<?php
								echo $this->onAftercourseContent;
							?>
						</div>
					<?php
					}
				?>
			</div><!--span8 rightdiv_class ends-->

			<!--Get the left panel - modules one-->
			<?php if (!empty($leftdiv_class))
			{ ?>

				<div class="<?php echo $leftdiv_class; ?>">
					<div class="enrollHtml">
						<?php if ($course->type == 1)
						{
							if ($this->usercanAccess == 0 && $showbuy == 1)
						{
							?>
								<div class="panel-heading">
									<img alt="course_progress" src="<?php echo JUri::root(true).'/media/com_tjlms/images/default/icons/info.png'; ?>" />
									<span class="course_block_title"><?php echo JText::_('COM_TJLMS_SUB_PLAN_INFO')?></span>
								</div>
							<?php

							$query = $db->getQuery(true);
							$query->select('*, p.title AS plan_title');
							$query->from('`#__payplans_planapp` AS pa');
							$query->join('LEFT', '`#__payplans_plan` AS p ON p.plan_id = pa.plan_id');
							$query->join('LEFT', '`#__payplans_app` AS a ON a.app_id = pa.app_id');
							$query->where('a.type = "tjlms"');
							$query->where('a.published = 1');
							$query->where('p.published = 1');
							$db->setQuery($query);
							$planapp = $db->loadObjectList();

							?>
							<div class="panel-content tjlms_course_plan ">
								<table class="table table-condensed ">
									<!--<thead>
										<tr>
											<td class="plan-name course_block_title">
												<?php	echo JText::_('COM_TJLMS_PLANNAME'); ?>
											</td>
											<td class="plan-duration course_block_title">
												<?php	echo JText::_('COM_TJLMS_PLAN_DURATION');  ?>
											</td>
											<td class="plan-value course_block_title">
												<?php echo JText::_('COM_TJLMS_PLAN_AMOUNT');	?>
											</td>
											<td class="plan-buybtn course_block_title">
												&nbsp;
											</td>
										</tr>
									</thead>--><?php
									$isShowPlan = 0;

									require_once JPATH_SITE . '/components/com_payplans/helpers/plan.php';
									require_once JPATH_SITE . '/components/com_payplans/helpers/format.php';

									foreach ($planapp as $planAppData)
									{
										$decodeCoursePlans = json_decode($planAppData->app_params);
										$decodePlanDetails = json_decode($planAppData->details);

										foreach ($decodeCoursePlans as $tjlms_plans)
										{
											if (!empty($tjlms_plans))
											{
												$coursePlanIds = implode(", ",$tjlms_plans);

												$query = $db->getQuery(true);
												$query->select('course_id');
												$query->from('`#__tjlms_subscription_plans` AS sp');
												$query->where('sp.id IN (' . $coursePlanIds . ')');
												$db->setQuery($query);
												$courseIds = $db->loadColumn();

												if (in_array($course_id, $courseIds))
												{
													$uri = JRoute::_('index.php?option=com_payplans&view=plan&task=subscribe&plan_id='.$planAppData->plan_id);

													if ($this->oluser->guest == 1)
													{
														$url = base64_encode($uri);
														$buy_link = JRoute::_('index.php?option=com_users&view=login&return='.$url);
													}
													else
													{
														$buy_link = $uri;
													}
													?>
													<tr>
														<td class="plan-name course_block_title">
															<?php	echo JText::_('COM_TJLMS_PLANNAME'); ?>
														</td>
														<td class="plan_name" >
															<?php	echo $planAppData->plan_title;?>
														</td>
													</tr>
													<tr>
														<td class="plan-duration course_block_title">
															<?php	echo JText::_('COM_TJLMS_PLAN_DURATION');  ?>
														</td>
														<td class="plan_duration">
															<?php	$duration = PayplansHelperPlan::convertIntoTimeArray($decodePlanDetails->expiration); ?>
															<?php	echo PayplansHelperFormat::planTime($duration); ?>
														</td>
													</tr>
													<tr>
														<td class="plan-value course_block_title">
															<?php echo JText::_('COM_TJLMS_PLAN_AMOUNT');	?>
														</td>
														<td class="plan_value green tjlms-bold-text">
															<?php echo $this->tjlmshelperObj->getFromattedPrice($decodePlanDetails->price, $decodePlanDetails->currency);	?>
														</td>
													</tr>
													<tr>
														<td colspan="2" class="plan_pay_button center"><?php
															if ($this->usercanAccess == 0 && $showbuy == 1)
															{
															?>
															<a href="<?php echo $buy_link; ?>" class="btn btn-mini btn-primary tjlms-btn-flat MsgBtnDivContainner">
																<?php echo JText::_('COM_TJLMS_COURSE_BUY_NOW');?>
															</a>
															<?php
															}
															?>
														</td>
													</tr>
													<tr>
														<td colspan="2">
															<hr />
														</td>
													</tr>
													<?php
													$isShowPlan++;
												}
											}
										}
									}

									if ($isShowPlan == 0)
									{
										echo "<tr><td colspan='4'>" .  JText::_('COM_TJLMS_COURSE_NO_PLAN') . "</td></tr>" ;
									}

									?>
								</table>
							</div><?php
						}
						else
						{
							$query = $db->getQuery(true);
							$query->select('*, p.title AS plan_title');
							$query->from('`#__tjlms_payplanApp` AS pa');
							$query->join('LEFT', '`#__payplans_subscription` AS s ON s.subscription_id = pa.pp_subscription_id');
							$query->join('LEFT', '`#__payplans_plan` AS p ON p.plan_id = s.plan_id');
							$query->where('pa.course_id = ' . $course_id);
							$query->where('pa.user_id = ' . $this->oluser_id);
							$query->where('p.published = 1');
							$query->where('s.status != 1603');
							$query->order('pa.id DESC');
							$db->setQuery($query);
							$planDetails = $db->loadObject();

							if (strtotime($planDetails->subscription_date))
							{
								?><div class="alert alert-success"><?php
								echo JText::sprintf(
									'COM_TJLMS_COURSE_BUY_MESSAGE',
									$planDetails->plan_title,
									JHtml::date($planDetails->subscription_date, 'd-m-Y', false),
									JHtml::date($planDetails->expiration_date, 'd-m-Y', false)
								);?></div><?php
							}
						}
					}
					else
					{
						if($this->checkifuserenroled == '' && $this->oluser_id && $this->usercanAccess != 1)
						{
							?>
							<form method='POST' name='adminForm' id='adminForm' class="form-validate form-horizontal enrolmentform" action='' enctype="multipart/form-data">

								<div class="center">
									<button title="<?php echo JText::_('COM_TJLMS_ENROL_BTN_TOOLTIP');?> " class="btn btn-large btn-block btn-primary tjlms-btn-flat" type="button" id="free_course_button" onclick="enrollUser();" ><?php	echo JText::_('TJLMS_COURSE_ENROL')	?></button>
								</div>
								<input type="hidden" name="option" value="com_tjlms" />
								<input type="hidden" id="task" name="task" value="course.enrol" />
								<input type="hidden" name="view" value="course" />
								<input type="hidden" id="course_id" name="course_id" value="<?php echo $this->course_id;?>" />
								<input type="hidden" id="itemid" name="itemid" value="<?php echo $this->item_id ;?>" />
								<input type="hidden" name="boxchecked" value="" />
							</form>
					<?php
						}
					}
					?>
					</div>
					<!--Render all modules here-->
					<?php
						echo $course_blocksHTML;
					?>
				</div>
			<?php
			}	?>
		</div>
	</div>
</div>

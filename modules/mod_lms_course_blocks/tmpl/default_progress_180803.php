<?php
/**
 * @package     LMS_Shika
 * @subpackage  mod_lms_course_progress
 * @copyright   Copyright (C) 2009-2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link        http://www.techjoomla.com
 */
// No direct access.
defined('_JEXEC') or die;

?>

<?php
// If user is not guest then show enrol or Buy now buttons
if(($mod_data->oluser->guest != 1 && $mod_data->checkifuserenroled == 1))
{
	if($enrolment_pending == 0)
	{
		?>
	<div class="courseProgress couserBlock">
		<div class="panel-heading">
			<i class="fa fa-pie-chart fa-2x"></i>
			<span class="course_block_title"><?php echo JText::_('COM_TJLMS_COURSE_PROGRESS')?></span>
		</div>

		<div class="panel-content progressDiv">
			<div class="progress-pie-chart" data-percent="<?php echo $mod_data->progress_in_percent; ?>"><!--Pie Chart -->
				<div class="ppc-progress">
					<div class="ppc-progress-fill"></div>
				</div>
				<div class="ppc-percents">
				<div class="pcc-percents-wrapper">
					<span><?php echo round($mod_data->progress_in_percent); ?>%</span>
				</div>
				</div>
			</div><!--End Chart -->
			<?php if ($course->certificate_term != 0 && $isCertificateExists): ?>
					<?php if($mod_data->isExpired === true):?>
						<div class="cert_expired_title center"><strong><?php echo JText::_('MOD_LMS_COURSE_CERTIFICATES_EXPIRED') ;?></strong></div>
					<?php else:?>
					<a rel="{handler: 'iframe', size: {x: 800, y: 600}}" class="modal center" href="index.php?option=com_tjlms&view=certificate&tmpl=component&course_id=<?php echo $course->id; ?>&user_id=<?php echo $mod_data->oluser_id; ?>">
						<button class="btn btn-large btn-success tjlms-btn-flat">
							<?php echo JText::_('COM_TJLMS_GET_CERTIFICATE');?>
						</button>
					</a>
					<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
<?php
	}
} ?>


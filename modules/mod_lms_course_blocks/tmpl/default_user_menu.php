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

<?php if ($mod_data->oluser_id): ?>

	<div class="courseUserMenu">
		<div class="panel-heading">
			<img alt="enrolled_users" src="<?php echo $mod_data->course_icons_path.'enrolled-users.png'; ?>" />
			<span class="course_block_title"><?php echo JText::_('TJLMS_COURSE_RECOMMEND_FRIENDS')?></span>
		</div>
		<div class="panel-content row-fluid usermenu">
		<!--recommend mod_data course to a friend-->

				<?php if (!empty($mod_data->getuserRecommendedUsers)): ?>
					<?php
						foreach($mod_data->getuserRecommendedUsers as $index => $recommeduser)
						{
					?>
							<div class="span4 center">

								<?php if (empty($recommeduser->avatar)) : ?>
									<?php	$recommeduser->avatar = JUri::root(true).'/media/com_tjlms/images/default/user.png';	?>
								<?php endif;	?>

								<?php if (!empty($recommeduser->profileurl)) : ?>
									<a class="" target="_blank" href="<?php echo $recommeduser->profileurl?>" >
								<?php endif;	?>
										<img class="img-circle solid-border" title="<?php echo $recommeduser->username;?>" src="<?php echo $recommeduser->avatar;?>" />
								<?php if (!empty($recommeduser->profileurl)) : ?>
									</a>
								<?php endif;	?>

							</div>
						<?php
						}
					?>
					<div class="clearfix"></div>
					<hr class="hr hr-condensed">
				<?php endif; ?>


			<div class="recommendCourse lmsBtnDivContainner">
				<?php 	$recommend_link = JRoute::_( 'index.php?option=com_tjlms&view=course&layout=recommendcourse&tmpl=component&course_id=' . $mod_data->course_id);	?>
					<a title="<?php echo JText::_('MOD_TJLMS_RECOMMEND_USER_TOOLTIP'); ?>" id="recommenduser" rel="{handler: 'iframe', size: {x: 800, y: 600}}" class="btn btn-small btn-tjlms-green  modal tjlms-color-white sidebarbtn" href="<?php echo $recommend_link; ?>" ><?php	echo JText::_('TJLMS_COURSE_RECOMMEND_FRIENDS')	?>
					</a>
			</div>
		</div>
	</div>
<?php endif; ?>

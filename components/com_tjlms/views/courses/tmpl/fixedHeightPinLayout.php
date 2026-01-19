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
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');

$lang = JFactory::getLanguage();
$lang->load('com_tjlms', JPATH_SITE);

$small_image_height = $tjlmsparams->get('small_height', '128', 'INT');
$fixed_H_pin_desc_height = $tjlmsparams->get('fixed_H_pin_desc_height', '60', 'INT');
$fixed_H_pin_title_height = $tjlmsparams->get('fixed_H_pin_title_height', '40', 'INT');
$courseName = $data['title'];
$course_url = $this->tjlmsFrontendHelper->tjlmsRoute('index.php?option=com_tjlms&view=course&id=' . $data['id']);
?>

<script>

jQuery(document).ready(function(){
		jQuery('.fixedHeightPinLayout .fixHeightCourseImage').css('height','<?php echo $small_image_height;	?>');
		jQuery('.fixedHeightPinLayout .tjlms_pin_desc').css('height','<?php echo $fixed_H_pin_desc_height;	?>');
		jQuery('.fixedHeightPinLayout .title_fixed_width').css('height','<?php echo $fixed_H_pin_title_height;	?>');
	});

</script>

<div class="tjlms_pin_item fixedHeightPinLayout">

	<div class="thumbnail">

		<!--COURSE TITLE PART-->
		<div class="caption title_fixed_width tjlms_pin_caption">

			<strong >
				<a title="<?php echo htmlspecialchars($courseName); ?>" href="<?php echo  $course_url; ?>">
					<?php echo htmlspecialchars($courseName); ?>
				</a>
			</strong>
		</div><!--CAPTION ENDS-->

		<!--COURSE TITLE PART ENDS-->

		<!--COURSE IMAGE PART-->
		<div class="caption center fixHeightCourseImage">
			<a href="<?php echo  $course_url; ?>">
				<img class='tjlms_pin_image'
				src="<?php echo $courseImg;?>" alt="<?php echo  JText::_('TJLMS_IMG_NOT_FOUND') ?>" title="<?php echo htmlspecialchars($courseName); ?>" />
			</a>
		</div>
		<!--COURSE IMAGE PART ENDS-->

		<!--COURSE DESCRIPTION -->
		<div class="tjlms_pin_desc">
			<?php
				echo $data['short_desc'];
			?>
			<div class="fader">&nbsp;</div>
		</div>
		<!--COURSE DESCRIPTION ENDS-->

		<!--COURES SUBSPLANS DETAILS -->
		<div class="tjlms_pin_info center">
				<div class="row-fluid">
					<div class="span6">
						<span class="gray pull-right"><?php echo JText::_("COM_TJLMS_PRICE");?></span>
					</div>
					<div class="span6">
						<span class="green pull-left">
						<?php if($data['type'] == 0):
									echo JText::_("COM_TJLMS_COURSE_FREE");
							else:
									echo $comtjlmshelper->getFromattedPrice($data['price'], $this->currency);
							endif; ?>
						</span>
					</div>
				</div>
				<div class="clearfix"></div>
				<hr class="hr hr-condensed"/>
		</div>
		<!--COURES SUBSPLANS DETAILS ENDS-->

		<!--LIKES PART-->
		<div class="row-fluid tjlms_pin_info">
			<div class="pull-left center tjlms_pin_course_rating">
				<span title="<?php echo JText::_('COM_TJLMS_LIKES'); ?>">
					<i class="fa fa-thumbs-up"></i>
					<span class="likes-count ">
						<b><?php echo $data['likesforCourse']; ?></b>
					</span>
				</span>
			</div>

			<div class="pull-right center tjlms_pin_course_users">
				<span title="<?php echo JText::_('COM_TJLMS_STUDENT'); ?>">
					<i class="fa fa-user"></i>
					<span class="count">
						<b><?php echo $data['enrolled_users_cnt']; ?></b>
					</span>
				</span>
			</div>
		</div>
		<!--LIKES PART ENDS-->

		<div class="clearfix"></div>

	</div><!--THUMBNAIL ENDS-->

</div><!--WRAPPER DIV ENDS-->

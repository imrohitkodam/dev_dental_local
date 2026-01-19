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
$comparams = JComponentHelper::getParams( 'com_tjlms' );
$currency = $comparams->get('currency', '', 'STRING');
$allow_paid_courses = $comparams->get('allow_paid_courses', '0', 'INT');

$path = JPATH_SITE . DS . 'components' . DS . 'com_tjlms' . DS . 'helper' . DS . 'courses.php';

if (!class_exists('tjlmsCoursesHelper'))
{
	JLoader::register('tjlmsCoursesHelper', $path );
	JLoader::load('tjlmsCoursesHelper');
}
$tjlmsCoursesHelper = new tjlmsCoursesHelper;

$courseImg = $tjlmsCoursesHelper->getCourseImage($data, $this->course_images_size);
$course_url = $this->tjlmsFrontendHelper->tjlmsRoute('index.php?option=com_tjlms&view=course&id=' . $data['id']);
$courseName = $data['title'];
?>

<div class="tjlms_pin_item">

	<div class="thumbnail">

		<!--COURSE TITLE PART-->
		<div class="caption tjlms_pin_caption">

			<strong class="center">
				<a title="<?php echo $this->escape($courseName); ?>" href="<?php echo  $course_url; ?>">
					<?php echo $this->escape($courseName); ?>
				</a>
			</strong>

		</div><!--CAPTION ENDS-->
		<!--COURSE TITLE PART ENDS-->

		<!--COURSE IMAGE PART-->
		<div class="center">
			<a href="<?php echo  $course_url; ?>">
				<img class='tjlms_pin_image' src="<?php echo $courseImg;?>" alt="<?php echo  JText::_('TJLMS_IMG_NOT_FOUND') ?>" title="<?php echo $this->escape($courseName); ?>" />
			</a>
		</div>
		<!--COURSE IMAGE PART ENDS-->

		<!--COURSE DESCRIPTION -->
		<div class="tjlms_pin_desc">
			<?php

			$short_desc_char = $comparams->get('pin_short_desc_char', 50, 'INT');

			if(strlen($data['short_desc']) >= $short_desc_char)
				echo substr($data['short_desc'], 0, $short_desc_char).'...';
			else
				echo $data['short_desc'];
			?>
		</div>
		<!--COURSE DESCRIPTION ENDS-->

		<hr class="hr hr-condensed"/>

		<!--COURES SUBSPLANS DETAILS -->
		<div class="tjlms_pin_info center row-fluid">
			<div class="span6 hidden-phone hidden-tablet textright">
				<span class="gray"><?php echo JText::_("COM_TJLMS_PRICE"); echo ': '?></span>
			</div>
			<div class="span6 textleft">
				<span class="green">
				<?php if($data['type'] == 0):
							echo JText::_("COM_TJLMS_COURSE_FREE");
					else:
							echo $comtjlmshelper->getFromattedPrice($data['price'], $currency);
					endif; ?>
				</span>
			</div>
		</div>
		<!--COURES SUBSPLANS DETAILS ENDS-->

		<hr class="hr hr-condensed"/>

		<!--LIKES PART-->
		<div class="row-fluid tjlms_pin_info">
			<div class="pull-left center tjlms_pin_course_rating">
				<span class=" " title="<?php echo JText::_('COM_TJLMS_LIKES'); ?>">
					<i class="fa fa-thumbs-up"></i>
					<!--<span class="hidden-phone hidden-tablet"><?php echo JText::_('COM_TJLMS_LIKES'); ?></span>-->
					<span class="likes-count ">
						<b><?php echo $data['likesforCourse']; ?></b>
					</span>
				</span>
			</div>

			<div class="pull-right center tjlms_pin_course_users">
				<span class="  " title="<?php echo JText::_('COM_TJLMS_STUDENT'); ?>">
					<i class="fa fa-user"></i>
					<!--<span class="hidden-phone hidden-tablet"><?php echo JText::_('COM_TJLMS_STUDENT'); ?></span>-->
					<span class="count">
						<b><?php echo $data['enrolled_users_cnt']; ?></b>
					</span>
				</span>
			</div>
		</div>
		<!--LIKES PART ENDS-->

		<div class="clearfix"></div>

	</div><!--THUMBNAIL ENDS-->
<div class="clearfix"></div>
</div><!--WRAPPER DIV ENDS-->
<div class="clearfix"></div>
<script>
	jQuery(window).load(function () {
			if (window.matchMedia('(max-width: 800px)').matches){
			 /* remove textleft class for price */
			 jQuery('.tjlms_pin_info .span6').removeClass('textleft');
		   }

	});
</script>

<?php
/**
* @version		1.0.0 jgive $
* @package		jgive
* @copyright	Copyright © 2012 - All rights reserved.
* @license		GNU/GPL
* @author		TechJoomla
* @author mail	extensions@techjoomla.com
* @website		http://techjoomla.com
*/
// no direct access
defined('_JEXEC') or die('Restricted access');

if(JVERSION>=3.0)
{
	JHtml::_('bootstrap.tooltip');
	JHtml::_('behavior.multiselect');
	//JHtml::_('formbehavior.chosen', 'select');
}
$courses=$this->items;

?>
<div id="container">

		<?php
		foreach($courses as $course)
		{
			?>
			<!--tjlms_pin_item-->
			<div class="tjlms_list_item">

				<?php
					$course_url =	$this->tjlmsFrontendHelper->tjlmsRoute('index.php?option=com_tjlms&view=course&id='.$course->id, false);

					// converting to array
					$course_arr = (array)$course;
					$courseImg	= $this->tjlmsCoursesHelper->getCourseImage($course_arr,'S_');
				?>
				<!--thumbnail-->
				<div class="thumbnail tjlms_list_layout_element " style="padding:0px;">

					<!-- Image-->
					<div class="tjlms_list_image pull-left">
						<a href="<?php echo $course_url; ?>">
							<img alt="200x150" class="tjlms_thmb_style" src="<?php echo $courseImg;?>">
						</a>
					</div>
					<!-- Image-->

					<div class="tjlms_list_layout_element_details">
					<!--caption -->
						<div class="tjlms_list_caption">
								<a href="<?php echo $course_url; ?>">
								  <?php
									if(strlen($course->title)>=100)
										echo substr($course->title,0,100).'...';
									else
										echo $course->title;
									?>
								</a>
						</div>
						<!--caption -->

						<!--course description -->
						<div class="tjlms_list_desc">
								  <?php
								  // Durgesh commented for tjlms plugin
									/*	if(strlen($course->short_desc)>=100)
										echo substr($course->short_desc,0,100).'...';
									else  */
										echo $course->short_desc;
									?>
						</div>
						<!--course description -->
						<hr >
						<!--coures details -->
						<div class="tjlms_list_info center">
								<span class="tjlms_pin_course_price textleft span3">
										<span class="gray"><?php echo JText::_("COM_TJLMS_PRICE");?></span>

										<span class="green">
										<?php
										if($course->type==0)
											echo JText::_("COM_TJLMS_COURSE_FREE");
										else
											echo "£".$course->price;
										?>
										</span>
								</span>
								<span class="tjlms_list_course_rating span4">
									<span class=" " title="<?php echo JText::_('COM_TJLMS_LIKES'); ?>">
										<i class="fa fa-thumbs-up"></i>
										<!--<span class="hidden-phone hidden-tablet"><?php echo JText::_('COM_TJLMS_LIKES'); ?></span>-->
										<span class="likes-count">
											<b><?php echo $course->likesforCourse; ?></b>
										</span>
									</span>
								</span>
								<span class="tjlms_list_course_users textright span4">
									<span class="  " title="<?php echo JText::_('COM_TJLMS_STUDENT'); ?>">
										<!--<span class="hidden-phone hidden-tablet"><?php echo JText::_('COM_TJLMS_STUDENT'); ?></span>-->
										<span class="count">
											<i class="icon-user"></i>
											<?php echo $course->enrolled_users_cnt; ?>
										</span>
									</span>
								</span>
								<div class="clearfix"></div>
						</div>
						<!--coures details -->
					</div>
				</div>
				<!--thumbnail-->
			</div>
			<!--tjlms_pin_item-->
		<?php
		}
			?>
</div>
<!--container -->


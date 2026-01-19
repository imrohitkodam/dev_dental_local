<?php
/**
 * @package     LMS_Shika
 * @subpackage  mod_lms_course_display
 * @copyright   Copyright (C) 2009-2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license     GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link        http://www.techjoomla.com
 */
// No direct access.
defined('_JEXEC') or die;
JHTML::_('behavior.modal');
$document =	JFactory::getDocument();
$document->addStylesheet(JUri::root(true) . '/modules/mod_lms_course_display/assets/css/thumbnail-slider.css');
$document->addStylesheet(JUri::root(true) . '/media/com_tjlms/font-awesome/css/font-awesome.css');
$document->addScript(JUri::root(true) . '/modules/mod_lms_course_display/assets/js/thumbnail-slider.js');
?>
<script>
 var xheight ='';
jQuery(window).load(function() {
	xheight = 180;
	contentSliderInit_<?php echo $module->id; ?>(0);

    var imgMaxHeight = -1;
	// set max height to lesson image
    jQuery('.tjlms_pin_image').each(function(){
		imgMaxHeight = imgMaxHeight > jQuery(this).height() ? imgMaxHeight : jQuery(this).height();
    });
		jQuery('.tjlms_lesson_image').css('height', imgMaxHeight);

	// set max height to lesson title
	var titlemaxHeight = -1;
	jQuery('.tjlms_pin_title').each(function(){
		titlemaxHeight = titlemaxHeight > jQuery(this).outerHeight(true) ? titlemaxHeight : jQuery(this).outerHeight(true);
    });
		jQuery('.tjlms_pin_title').css('height', titlemaxHeight);

	// set max height to lesson description
	var descmaxHeight = -1;
	jQuery('.jsslide .thumbnail .tjlms_pin_short_desc').each(function(){
		descmaxHeight = descmaxHeight > jQuery(this).outerHeight(true) ? descmaxHeight : jQuery(this).outerHeight(true);
    });

	jQuery('.tjlms_pin_short_desc').css('height', descmaxHeight);

	// Get the pin bottom height
	var bottomHeight = jQuery('.tjlms_pin_bottom').outerHeight(true);

	//xheight = imgMaxHeight + titlemaxHeight + descmaxHeight + bottomHeight + 100;


	xheight = jQuery('.jsslide .thumbnail').outerHeight(true);

	contentSliderInit_<?php echo $module->id; ?>(0);
});
</script>

<div class="com_tjlms_content tjlms-wrapper coursesModule" id="tj-contentslider-<?php echo $module->id;?>">
<!--toolbar-->
<div class="tj-contentslider-left tj-contentslide-left-img" title="<?php echo JText::_('Previous'); ?>">&nbsp;</div>

<div class="tj-contentslider-center-wrap " style="text-decoration: none;">
	<div  id="tj-contentslider-center-<?php  echo $module->id;?>" class="tj-contentslider-center" style="height:auto; !important" >
				<?php
					$i = 1;

					foreach ( $target_data as $ind => $eachCourse )
					{
						$courseImg	= $eachCourse->image;
						$course_url = $comtjlmshelper->tjlmsRoute('index.php?option=com_tjlms&view=course&id=' . $eachCourse->id);
						?>
						<div class="content_element tjslide1_<?php echo $eachCourse->cat_id; ?>">
							<div class="thumbnail">
								<div class="center tjlms_lesson_image">
									<a href="<?php echo  $course_url; ?>">
										<img class="tjlms_pin_image" src="<?php echo $courseImg;?>" alt="<?php echo  JText::_('TJLMS_IMG_NOT_FOUND') ?>"
										title="<?php echo htmlspecialchars($eachCourse->title); ?>">
									</a>
								</div>
								<div class="caption tjlms_pin_title">
									<strong>
										<a title="<?php echo htmlspecialchars($eachCourse->title); ?>"
										href="<?php echo $course_url; ?>">
										<?php echo htmlspecialchars($eachCourse->title); ?>
										</a>
									</strong>
								</div>
								<div class="caption tjlms_pin_short_desc">
									<?php $lesson_desc = (strlen($eachCourse->short_desc) < 100 ? $eachCourse->short_desc : substr($eachCourse->short_desc, 0, 100) . "..."); ?>
							<!--		<div class=" tjlms_pin_short_desc">
							-->			<?php echo htmlspecialchars($lesson_desc); ?>
							<!--		</div>
						-->		</div>
								<hr class="hr hr-condensed"/>
								<!-- AG 18-07-18
								<div class="tjlms_pin_bottom">
									<div class="row-fluid">
										<div class="span6">
											<div class="tjlms_pin_course_enrolled_users">
												<i class="fa fa-user"></i>
												<span class="count">
													<b><?php echo $eachCourse->enrolledUsersCount;?></b>
												</span>
											</div>
										</div>
										<div class="span6">
											<div class="tjlms_pin_course_price">
											  <?php if ($eachCourse->type == 0):
														echo JText::_("COM_TJLMS_COURSE_FREE");
													else :
														$price = $tjlmsCoursesHelper->getCourseLowestPrice($eachCourse);
														echo $comtjlmshelper->getFromattedPrice($price, $currency);
													endif; ?>
											</div>
										</div>
									</div>
								</div>
								-->
							</div>
						</div>
				<?php } ?>
	</div>
</div>
<div class="tj-contentslider-right tj-contentslide-right-img" title="<?php echo JText::_('Next'); ?>">&nbsp;</div>
</div>
<style>
	div.jsslide{padding:<?php echo $pinPadding; ?>px;}
	div.jsslide .thumbnail{margin-bottom:4px;}
</style>
<script type="text/javascript">
	//<!--[CDATA[

	function contentSliderInit_<?php echo $module->id;?> (cid) {
		cid = parseInt(cid);
		var containerID = 'tj-contentslider-<?php echo $module->id;?>';
		var container = $(containerID);
		container.getElements('.jsslide').each(function(el){
			el.dispose();
		});

		if(cid == 0) {
			var elems = $('tj-contentslider-center-<?php echo $module->id;?>').getElements('div[class*=content_element]');
		}else{
			var elems = $('tj-contentslider-center-<?php echo $module->id;?>').getElements('div[class*=tjslide2_'+cid+']');
		}
		var total = elems.length;

		var options={
			w: <?php echo $xwidth; ?>,
			h: xheight,
			num_elem:  <?php echo $displayLimit; ?>,
			mode: 'horizontal', //horizontal or vertical
			direction: 'left', //horizontal: left or right; vertical: up or down
			total: total,
			url: '<?php echo JURI::base(); ?>modules/mod_jacontentslider/mod_jacontentslider.php',
			wrapper:  container.getElement("div.tj-contentslider-center"),
			duration: <?php echo $animationtime; ?>,
			interval: <?php echo $delaytime; ?>,
			modid: <?php echo $module->id;?>,
			running: false,
			auto:0
		};
		var jscontentslider = new Tjlms_ContentSlider( options );

		for(i=0;i<elems.length;i++){
			jscontentslider.update (elems[i].innerHTML, i);
		}
		jscontentslider.setPos(null);
		if(jscontentslider.options.auto){
			jscontentslider.nextRun();
		}

		container.getElement(".tj-contentslide-left-img").onclick = function(){setDirection2<?php echo $module->id;?>('right', jscontentslider);};
		container.getElement(".tj-contentslide-right-img").onclick = function(){setDirection2<?php echo $module->id;?>('left', jscontentslider);};
		/**active tab**/
		if (container.getElement('.tj-button-control')) {
		container.getElement('.tj-button-control').getElements('a').each(function(el){
			var css = (el.getProperty('rel') == cid) ? 'active' : '';
			el.className = css;
		});
		}
	}
	/*window.addEvent( 'domready', function(){ contentSliderInit_<?php echo $module->id;?>(0); } );*/

	function setDirection2<?php echo $module->id;?>(direction, jscontentslider) {
		var oldDirection = jscontentslider.options.direction;

		jscontentslider.options.direction = direction;
		jscontentslider.options.interval = 100;
		jscontentslider.options.auto = 1;
		jscontentslider.nextRun();
		jscontentslider.options.auto = <?php echo $auto; ?>;
		jscontentslider.options.interval = <?php echo $delaytime; ?>;

		setTimeout(function(){
			jscontentslider.options.direction = oldDirection;
		}, 510);
	}
	//]]-->
</script>

<script>
if (typeof jQuery != 'undefined') {
(function($) {
$(document).ready(function(){
$('.carousel').each(function(index, element) {
$(this)[index].slide = null;
});
});
})(jQuery);
}
</script>

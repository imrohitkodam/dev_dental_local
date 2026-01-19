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
	$document = JFactory::getDocument();
	$course = $this->course_info;

?>
<div class="tjlms-course-header">
	<div class="row-fluid">

		<div class="tjlms-course-header-child course-img">
			<img  class="img-polaroid" alt="100x64" style="" src="<?php echo $course->image;?>" id="<?php echo 'img'.$course->id;?>" />
		</div>

		<div class="tjlms-course-header-child course-right-block">
			<div>
				<h4 class="media-heading"><?php	echo $course->title;	?></h4>
				<?php
				if (!empty($this->courseRating))
				{
					echo $this->courseRating;
				}

				?>
			</div>

				<span class="help-block"><?php  echo JText::_('TJLMS_COURSE_NAME'). implode(" > ", $this->course_categories); ?></span>

				<?php if ($this->enable_tags == 1 && !empty($course->tags->itemTags)) : ?>

					<div class="row-fluid">

							<?php $course->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
							<?php echo $course->tagLayout->render($course->tags->itemTags); ?>

					</div>

				<?php endif; ?>

				<?php
				if($this->tjlmsparams->get('social_sharing'))
				{
					?>
					<div class="row-fluid">
					<?php
					if($this->tjlmsparams->get('social_shring_type')=='addthis')
					{
						$pid = $this->tjlmsparams->get('addthis_publishid');
						if(!empty($pid))
						{
							$add_this_js='http://s7.addthis.com/js/300/addthis_widget.js';
							$document->addScript($add_this_js);

							$add_this_share='
							<!-- AddThis Button BEGIN -->
							<div class="addthis_toolbox addthis_default_style">
							<a class="addthis_button_facebook_like" fb:like:layout="button_count" class="addthis_button" addthis:url="'.$this->courseDetailsUrl.'"></a>
							<a class="addthis_button_google_plusone" g:plusone:size="medium" class="addthis_button" addthis:url="'.$this->courseDetailsUrl.'"></a>
							<a class="addthis_button_tweet" class="addthis_button" addthis:url="'.$this->courseDetailsUrl.'"></a>
							<a class="addthis_button_pinterest_pinit" class="addthis_button" addthis:url="'.$this->courseDetailsUrl.'"></a>
							<a class="addthis_counter addthis_pill_style" class="addthis_button" addthis:url="'.$this->courseDetailsUrl.'"></a>
							</div>
							<script type="text/javascript">
								var addthis_config ={ pubid: "'.$pid.'"};
							</script>
							<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid="'.$pid.'"></script>
							<!-- AddThis Button END -->' ;

							//$integrationsHelper=new integrationsHelper();
							//$integrationsHelper->loadScriptOnce($add_this_js);
							//output all social sharing buttons
							echo' <div id="rr" style="">
								<div class="social_share_container">
								<div class="social_share_container_inner">'.
									$add_this_share.
								'</div>
							</div>
							</div>
							';
						}
					}
					else
					{
						$native_share = JUri::root(true) . '/components/com_tjlms/assets/js/native_share.js';
						echo "<script type='text/javascript' src='".$native_share."'></script>";
						echo '<div id="fb-root"></div>';
						echo '<div class="tjlms_horizontal_social_buttons">';
						echo '<div class="pull-left">
								&nbsp;<div class="fb-like" data-href="'.$this->courseDetailsUrl.'" data-layout="button_count" data-action="like" data-show-faces="true" data-share="true"></div>
							</div>';
						echo '<div class="pull-left">
								&nbsp; <div class="g-plus" data-action="share" data-annotation="bubble" data-href="'.$this->courseDetailsUrl.'"></div>
							</div>';
						echo '<div class="pull-left">
								&nbsp; <a href="https://twitter.com/share" class="twitter-share-button" data-url="'.$this->courseDetailsUrl.'" data-counturl="'.$this->courseDetailsUrl.'"  data-lang="en">Tweet</a>
							</div>';
						echo '</div>
							<div class="clearfix"></div>';
					}
					?>
					</div>
					<?php
				}
				?>
		</div>
	</div>

	<div class="tjlms-course-after_title row-fluid">
		<?php
			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('content');
			$jlikeresult =$dispatcher->trigger('OnAftercourseTitle',array('com_tjlms.course',$this->course_id,$course->title));

			$desc_class = '';
			if (!empty($jlikeresult[0]))
			{
				$desc_class = "tjlms-dotted-rightborder span7";
			}
		?>
		<!--<div class="long_desc <?php echo $desc_class;?>" style="word-wrap: break-word;">
			<?php //echo $course->description;

			if ($course->description)
			{
				if(strlen($course->description) > 150 )
					echo $this->tjlmshelperObj->html_substr($course->description, 0, 150 ).'<a href="javascript:" class="r-more">...More</a>';
				else
					echo $course->description;
			}
			else
			{
				echo $course->short_desc;
			}
			?>
		</div>
		<div class="long_desc_extend no-margin <?php echo $desc_class;?>" style="display:none;">
			<?php
				echo $course->description.'<a href="javascript:" class="r-less">...Less</a>';
			?>
		</div>
-->
		<?php if (!empty($jlikeresult[0])) { ?>

				<div class="tjlms-extra-info span5">
					<div class="tjcourse-likes">
							<?php	echo $jlikeresult[0]; ?>
					</div>
					<div class="clearfix"></div>
				</div>

		<?php }  ?>
	</div>
</div><!-- rowfluid-->




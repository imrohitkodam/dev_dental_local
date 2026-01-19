<?php
/**
 * @package TjLMS
 * @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     http://www.techjoomla.com
 */
	defined('_JEXEC') or die('Restricted access');

	JHTML::_('behavior.tooltip');
	$document = JFactory::getDocument();
	$course = $this->course_info;
	$descShown = 0;

	$dispatcher = JDispatcher::getInstance();
	JPluginHelper::importPlugin('content');
	$jlikeresult =$dispatcher->trigger('OnAftercourseTitle',array('com_tjlms.course',$this->course_id,$course->title));
?>
<div class="tjlms-course-header">
	<div class="container-fluid mb-20">
		<div class="row">

			<div class="col-md-4 tjlms-course-header__img d-table-cell">
				<img alt="100x64" class="w-100" style="" src="<?php echo $course->image;?>" id="<?php echo 'img'.$course->id;?>" />
			</div>
			<div class="col-md-8 tjlms-course-header__info d-table-cell valign-top pl-5">
				<h3 class="mt-0 hidden-xs"><?php	echo $course->title;	?></h3>
				<strong class="visible-xs"><?php	echo $course->title;	?></strong>
				<div class="small hidden-xs"><?php  echo JText::_('TJLMS_COURSE_NAME'). implode(" > ", $this->course_categories); ?></div>

		<?php if (!empty($jlikeresult[0])): ?>

				<div class="tjcourse-likes" id="jlike-container">
						<?php	echo $jlikeresult[0]; ?>
				</div>

		<?php  endif;	?>

		<?php if($this->tjlmsparams->get('social_sharing')) :?>

				<div class="container-fluid hidden-xs">
					<div class="row">
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
				</div>

		<?php endif; ?>
			</div>

		</div>
	</div>

	<?php if ($this->enable_tags == 1 && !empty($course->tags->itemTags)) : ?>
	<div class="container-fluid">
		<div class="row">
			<?php $course->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
			<?php echo $course->tagLayout->render($course->tags->itemTags); ?>
		</div>
	</div>

	<?php endif; ?>

	<div class="container-fluid">
		<div class="row tjlms-course-header__desc">

			<div class="long_desc" style="word-wrap: break-word;">
				<?php
				if ($course->description)
				{
					echo $course->description;
					//~ if(strlen(strip_tags($course->description)) > 150 )
						//~ echo $this->tjlmshelperObj->html_substr($course->description, 0, 150 ).'<a href="javascript:" class="r-more">' . JText::_("COM_TJLMS_TOC_COURSE_DESC_MORE") . '</a>';
					//~ else
						//~ echo $this->tjlmshelperObj->html_substr($course->description, 0);
				}
				//~ else
				//~ {
					//~ if(strlen($course->short_desc) > 150 )
						//~ echo $this->tjlmshelperObj->html_substr($course->short_desc, 0, 150 ).'<a href="javascript:" class="r-more">' . JText::_("COM_TJLMS_TOC_COURSE_DESC_MORE") . '</a>';
					//~ else
						//~ echo $this->tjlmshelperObj->html_substr($course->short_desc, 0);
				//~ }
				?>
			</div>
			<div class="long_desc_extend no-margin" style="display:none;">
					<?php
					//~ if (!empty($course->description))
					//~ {
						//~ echo $course->description.'<a href="javascript:" class="r-less">' . JText::_("COM_TJLMS_TOC_COURSE_DESC_LESS") . '</a>';
					//~ }
					//~ else
					//~ {
						//~ echo $course->short_desc.'<a href="javascript:" class="r-less">' . JText::_("COM_TJLMS_TOC_COURSE_DESC_LESS") . '</a>';
					//~ }
					?>
				</div>

		</div>
	</div>
</div>



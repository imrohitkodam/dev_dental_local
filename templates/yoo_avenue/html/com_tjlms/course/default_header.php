<?php
/**
 * @package     Shika
 * @subpackage  com_tjlms
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Plugin\PluginHelper;

HTMLHelper::_('behavior.tooltip');
$document = Factory::getDocument();
$course   = $this->item;

$descShown = 0;

$dispatcher = JDispatcher::getInstance();
PluginHelper::importPlugin('content');

$jlikeresult = $dispatcher->trigger('OnAftercourseTitle', array('com_tjlms.course', $this->course_id, $course->title));

?>
<div class="tjlms-course-header">
	<div class="container-fluid mb-20">
		<div class="row">

			<!--Course message and desc -->

			<?php
				echo $this->loadTemplate('message');
			?>
			<!--Course message and desc ends -->

			<div class="tjlms-course-header__img d-table-cell">
				<img itemprop="image" alt="<?php echo $course->title;?>" src="<?php echo $course->image;?>" id="<?php echo 'img'.$course->id;?>" />
			</div>
			<div class="tjlms-course-header__info d-table-cell valign-top pl-15">
				<h1 itemprop="name" class="mt-0">
					<?php echo $this->escape($course->title);	?>
				</h1>
				<div class="small hidden-xs"><?php  echo Text::_('TJLMS_COURSE_NAME'). implode(" > ", $this->course_categories); ?></div>

		<?php if (!empty($jlikeresult[0])): ?>

				<div class="tjcourse-likes" id="jlike-container">
						<?php	echo $jlikeresult[0]; ?>
				</div>

		<?php  endif;	?>

		<?php
			if (($this->item->userOrder->status == "C" || $this->item->userEnrollment->id) && !empty($this->item->toc))
			{
				echo $this->loadTemplate('resume');

			}
		?>

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

						echo '<div class="pull-left"><div class="fb-share-button"
								data-href="'.$this->courseDetailsUrl.'"
								data-layout="button_count">
							  </div></div>';

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

	<?php if ($this->tjlmsparams->get('enable_tags') == 1 && !empty($course->course_tags->itemTags)) : ?>
	<div class="row">
		<div class="col-xs-12">
			<div class="mt-15">
				<?php
					$course->tagLayout = new FileLayout('course.tags', JPATH_SITE . '/components/com_tjlms/layouts');
					echo $course->tagLayout->render($course->course_tags->itemTags);
				?>
			</div>
		</div>
	</div>

	<?php endif; ?>

	<div class="container-fluid">
		<div itemprop="description" class="row tjlms-course-header__desc">

			<div class="long_desc text-break">
				<?php
				if ($course->description)
				{
					echo $course->description;
				}
				?>
			</div>
		</div>
	</div>
</div>

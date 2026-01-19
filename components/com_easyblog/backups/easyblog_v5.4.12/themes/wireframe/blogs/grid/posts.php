<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php $index = 0; ?>
<?php if ($posts) { ?>
	<?php foreach ($posts as $post) { ?>
	<div class="eb-blog-grid__item eb-blog-grid__item--<?php echo $gridLayout; ?>" <?php echo $index == 0 ? 'data-eb-posts-section data-url="' . $currentPageLink . '"' : ''; ?>>
		<div class="eb-blog-grid__content">

			<?php if ($this->params->get('grid_show_cover', true)) { ?>
				<?php if (EB::image()->isImage($post->getImage())) { ?>
					<div class="eb-blog-grid__thumb">
						<a class="eb-blog-grid-image" href="<?php echo $post->getPermalink(); ?>" style="background-image: url('<?php echo $post->getImage(EB::getCoverSize('cover_size'));?>');">
							<?php if ($post->isFeatured()) { ?>
							<span class="eb-blog-grid-label">
								<i class="fa fa-bookmark"></i>
							</span>
							<?php } ?>
						</a>
					</div>
				<?php } else { ?>
					<?php echo EB::media()->renderVideoPlayer($post->getImage(), array('width' => '260','height' => '200','ratio' => '','muted' => false,'autoplay' => false,'loop' => false), false); ?>
				<?php } ?>
			<?php } ?>

			<div class="eb-blog-grid__title">
				<a href="<?php echo $post->getPermalink(); ?>"><?php echo $post->title; ?></a>
			</div>

			<!-- Grid meta -->
			<div class="eb-blog-grid__meta eb-blog-grid__meta--text">

				<?php if ($this->params->get('grid_show_author_avatar', false)) { ?>
				<div class="">
					<a href="<?php echo $post->getAuthorPermaLink(); ?>" class="eb-avatar-sm ">
						<img class="" src="<?php echo $post->getAuthor()->getAvatar(); ?>" alt="<?php echo $post->getAuthorName(); ?>">
					</a>
				</div>

				<?php } ?>

				<?php if ($this->params->get('grid_show_author', true)) { ?>
				<div class="eb-blog-grid-author">
					<a href="<?php echo $post->getAuthorPermalink(); ?>"><?php echo $post->getAuthorName(); ?></a>
				</div>
				<?php } ?>

				<?php if ($this->params->get('grid_show_category', true)) { ?>
				<div class="eb-blog-grid-category">
					<a href="<?php echo $post->getPrimaryCategory()->getPermalink();?>"><?php echo JText::_($post->getPrimaryCategory()->title);?></a>
				</div>
				<?php } ?>
			</div>
			<?php if ($this->params->get('grid_show_intro', true)) { ?>
			<div class="eb-blog-grid__body">
				<?php if ($this->config->get('layout_dropcaps')) { ?>
				<p class="has-drop-cap">
				<?php } ?>
					<?php echo $post->getIntro(true, $gridTruncation, 'intro', null, array('forceTruncateByChars' => true, 'forceCharsLimit' => $this->params->get('grid_content_limit', 350))); ?>
				<?php if ($this->config->get('layout_dropcaps')) { ?>
				</p>
				<?php } ?>
			</div>
			<?php } ?>
			<?php if ($this->params->get('grid_show_readmore', false)) { ?>
			<div class="eb-post-more mt-20">
				<a class="btn btn-default" href="<?php echo $post->getPermalink();?>"><?php echo JText::_('COM_EASYBLOG_CONTINUE_READING');?></a>
			</div>
			<?php } ?>
			<?php if ($this->params->get('grid_show_date', true)) { ?>
			<div class="eb-blog-grid__foot">
				<time class="eb-blog-grid-meta-date">
					<?php echo $post->getDisplayDate($this->params->get('grid_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC1')); ?>
				</time>
			</div>
			<?php } ?>
		</div>
	</div>
	<?php $index++; ?>
	<?php } ?>
<?php } ?>
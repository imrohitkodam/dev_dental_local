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
<?php if ($this->params->get('tag_header', true) && ($this->params->get('tag_title', true) || $this->params->get('tag_subscribe_rss', true))) { ?>
<div class="eb-tag">

	<?php if ($this->params->get('tag_title', true)) { ?>
	<div class="eb-tag-head">
		<h2 class="eb-tag-name reset-heading">
			<a href="<?php echo $tag->getPermalink();?>">
				<span class="col-cell"><i class="fa fa-tag muted"></i></span>
				<span class="col-cell"><?php echo $tag->getTitle();?></span>
			</a>
		</h2>
	</div>
	<?php } ?>

	<?php if ($this->params->get('tag_subscribe_rss', true)) { ?>
	<div class="eb-tag-bio">
		<?php if ($this->config->get('main_rss')) { ?>
			<span class="eb-tag-rss">
				<i class="fa fa-rss-square"></i>
				<a href="<?php echo $tag->getRssLink();?>" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS_TAGS', true); ?>" class="link-rss" target="_blank">
					<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS_TAGS'); ?>
				</a>
			</span>
		<?php } ?>
	</div>
	<?php } ?>

</div>
<?php } ?>

<div class="eb-blog-grids">
	<?php if ($posts) { ?>
		<div class="eb-blog-grid">
				<?php foreach ($posts as $post) { ?>
				<div class="eb-blog-grid__item eb-blog-grid__item--<?php echo $gridLayout; ?>">
					<div class="eb-blog-grid__content">
						<div class="eb-blog-grid__thumb">
						<?php if (EB::image()->isImage($post->getImage())) { ?>
							<a class="eb-blog-grid-image" href="<?php echo $post->getPermalink(); ?>" style="background-image: url('<?php echo $post->getImage('medium');?>');">
								<!-- Featured label -->
								<?php if ($post->isFeatured()) { ?>
								<span class="eb-blog-grid-label">
									<i class="fa fa-bookmark"></i>
								</span>
								<?php } ?>
							</a>
						<?php } else { ?>
							<?php echo EB::media()->renderVideoPlayer($post->getImage(), array('width' => '260','height' => '200','ratio' => '','muted' => false,'autoplay' => false,'loop' => false), false); ?>
						<?php } ?>

						</div>
						<div class="eb-blog-grid__title">
							<a href="<?php echo $post->getPermalink(); ?>"><?php echo $post->title; ?></a>
						</div>

						<!-- Grid meta -->
						<div class="eb-blog-grid__meta eb-blog-grid__meta--text">
							<?php if ($this->params->get('post_author', true)) { ?>
							<div class="eb-blog-grid-author">
								<a href="<?php echo $post->getAuthorPermalink(); ?>"><?php echo $post->getAuthorName(); ?></a>
							</div>
							<?php } ?>

							<?php if ($this->params->get('post_category', true)) { ?>
							<div class="eb-blog-grid-category">
								<a href="<?php echo $post->getPrimaryCategory()->getPermalink();?>"><?php echo JText::_($post->getPrimaryCategory()->title);?></a>
							</div>
							<?php } ?>
						</div>
						<div class="eb-blog-grid__body">
							<?php echo $post->getIntro(); ?>
						</div>
						<?php if ($this->params->get('post_date', true)) { ?>
						<div class="eb-blog-grid__foot">
							<time class="eb-blog-grid-meta-date">
								<?php echo $post->getDisplayDate()->format(JText::_('DATE_FORMAT_LC1')); ?>
							</time>
						</div>
						<?php } ?>

						<?php if ($post->hasReadmore() && $this->params->get('post_readmore', true)) { ?>
							<div class="eb-post-more mt-20">
								<a class="btn btn-default" href="<?php echo $post->getPermalink();?>"><?php echo JText::_('COM_EASYBLOG_CONTINUE_READING');?></a>
							</div>
						<?php } ?>
					</div>
				</div>
				<?php } ?>
		</div>
	<?php } else { ?>
		<div class="eb-empty">
			<i class="fa fa-info-circle"></i>
			<?php echo JText::_('COM_EASYBLOG_NO_BLOG_ENTRY');?>
		</div>
	<?php } ?>

	<?php if ($pagination) { ?>
		<?php echo $pagination->getPagesLinks();?>
	<?php } ?>
</div>

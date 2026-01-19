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
<?php if ($author->custom_css && $author->getAcl()->get('custom_css')) { ?>
<style type="text/css">
<?php echo $author->custom_css;?>
</style>
<?php } ?>

<?php if ($this->params->get('author_header', true)) { ?>
	<div class="eb-author" data-author-item data-id="<?php echo $author->id;?>">
		<?php echo $this->html('headers.author', $author, array(
																	'name' => $this->params->get('author_name', true),
																	'avatar' => $this->params->get('author_avatar', true),
																	'rss' => $author->id != $this->my->id,
																	'subscription' => $author->id != $this->my->id,
																	'twitter' => $this->params->get('author_twitter', true),
																	'website' => $this->params->get('author_website', true),
																	'biography' => $this->params->get('author_bio', true),
																	'featureAction' => false
															)
		); ?>
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
		<?php echo $pagination;?>
	<?php } ?>
</div>

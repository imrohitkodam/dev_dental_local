<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2020 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-cards__item mb-10" data-blog-posts-item data-id="<?php echo $post->id;?>" <?php echo $index == 0 ? 'data-eb-posts-section data-url="' . $currentPageLink . '"' : ''; ?>>
	<div class="eb-card <?php echo $this->isMobile() ? 'is-mobile' : '';?>">
		<?php echo $this->output('site/blogs/admin.tools', array('post' => $post, 'return' => $return)); ?>
		
		<?php echo $this->output('site/blogs/latest/card/post.cover', array('post' => $post)); ?>

		<div class="eb-card__content">
			<div class="eb-card__bd eb-card--border">
				<div class="eb-post-featured">
					<?php if ($post->isFeatured) { ?>
					<i class="fa fa-star" data-original-title="<?php echo JText::_('COM_EASYBLOG_POST_IS_FEATURED');?>" data-placement="bottom" data-eb-provide="tooltip"></i>
					<?php echo JText::_('COM_EASYBLOG_FEATURED_FEATURED');?>&nbsp;
					<?php } ?>
				</div>

				<?php if ($this->params->get('post_title', true)) { ?>
				<h2 class="eb-card__title">
					<a href="<?php echo $post->getPermalink();?>" class="text-inherit"><?php echo $post->title;?></a>
				</h2>
				<?php } ?>

				<?php if (in_array($post->getType(), array('photo', 'standard', 'twitter', 'email', 'link'))) { ?>
				<div class="eb-card__bd-content mt-20 type-<?php echo $post->posttype; ?>" >
					<?php echo $post->getIntro();?>
				</div>
				<?php } ?>

				<?php if ($post->hasReadmore() && $this->params->get('post_readmore', true)) { ?>
					<div class="eb-post-more mt-20">
						<a class="btn btn-default" href="<?php echo $post->getPermalink();?>"><?php echo JText::_('COM_EASYBLOG_CONTINUE_READING');?></a>
					</div>
				<?php } ?>

				<div class="eb-card__meta">
					<div class="eb-post-actions">

						<?php if ($this->params->get('post_hits', true)) { ?>
							<div class="col-cell eb-post-hits">
								<i class="fa fa-eye"></i>&nbsp; 
								<?php if ($this->isMobile()) { ?>
									<?php echo $post->hits;?>
								<?php } else { ?>
									<?php echo JText::sprintf('COM_EASYBLOG_POST_HITS', $post->hits);?>
								<?php } ?>
							</div>
						<?php } ?>

						<?php if ($post->getTotalComments() !== false && $this->params->get('post_comment_counter', true)) { ?>
							<div class="col-cell eb-post-comments">
								<a href="<?php echo $post->getCommentsPermalink();?>">
									<i class="fa fa-comment-o"></i>&nbsp; 

									<?php if ($this->isMobile()) { ?>
										<?php echo $post->getTotalComments();?>
									<?php } else { ?>
										<?php echo $this->getNouns('COM_EASYBLOG_COMMENT_COUNT', $post->getTotalComments(), true); ?>
									<?php } ?>
								</a>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>

			<div class="eb-card__ft">
				<div class="eb-card__ft-content eb-card--border">
					<div class="row-table">
						<div class="col-cell">
							<?php if ($this->params->get('post_date', true)) { ?>
							<time class="eb-meta-date" content="<?php echo $post->getDisplayDate($this->params->get('post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC4'));?>">
								<?php echo $post->getDisplayDate($this->params->get('post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC1')); ?>
							</time>
							<?php } ?>

							<?php if ($this->params->get('post_category', true) && $post->categories) { ?>
							<div>
								<div class="eb-post-category comma-seperator">
									<i class="fa fa-folder-open"></i>
									<?php foreach ($post->categories as $category) { ?>
									<span>
										<a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle();?></a>
									</span>
									<?php } ?>
								</div>
							</div>
							<?php } ?>
						</div>
						<div class="col-cell cell-tight">
							<?php if ($this->config->get('layout_avatar') && $this->params->get('post_author_avatar', true)) { ?>
							<div class="eb-post-author-avatar">
								<a href="<?php echo $post->getAuthorPermalink(); ?>" class="eb-avatar">
									<img src="<?php echo $post->creator->getAvatar();?>" width="50" height="50" alt="<?php echo $post->getAuthorName();?>" />
								</a>
							</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php echo $this->output('site/blogs/post.schema', array('post' => $post)); ?>
	</div>
</div>



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
<?php if ($featured && $this->params->get('featured_slider', true)) { ?>
<div class="eb-featured <?php echo $this->isMobile() ? 'is-mobile' : '';?>">
	<div id="eb-showcases" class="eb-showcases-card"  data-featured-posts>

		<?php if ($this->params->get('featured_bottom_navigation', true) && count($featured) > 1) { ?>
		<ol class="eb-showcase-indicators carousel-indicators reset-list text-center">
			<?php for ($i = 0; $i < count($featured); $i++) { ?>
				<li data-swiper-slide-pagination data-index="<?php echo $i; ?>" class="<?php echo $i == 0 ? 'active' : '';?>"></li>
			<?php } ?>
		</ol>
		<?php } ?>

		<div class="swiper-container" data-eb-featured-container>
			<div class="swiper-wrapper">
				<?php foreach ($featured as $post) { ?>

				<div class="swiper-slide">
					<div class="eb-card is-featured <?php echo $this->isMobile() ? 'is-mobile' : '';?>">
						<div class="eb-card__hd">
							<div class="embed-responsive embed-responsive-16by9">
								<div class="embed-responsive-item" style="
									background-image: url('<?php echo EB::image()->isImage($post->getImage()) ? $post->getImage(EB::getCoverSize('cover_featured_size')) : EB::getPlaceholderImage(false, 'video'); ?>');
									background-position: center;
								 ">
								</div>
							</div>
						</div>

						<div class="eb-card__content">

							<div class="eb-card__bd eb-card--border">
								<a href="<?php echo $post->getPermalink();?>">
									<h2 class="eb-card__title">
										<?php echo $post->title;?>
									</h2>
								</a>

								<div class="eb-card__bd-content">
									<?php echo $post->getIntro(false, true, 'intro', null, array('triggerPlugins' => false, 'forceTruncateByChars' => true, 'forceCharsLimit' => $this->params->get('featured_post_content_limit', 250) )); ?>
								</div>

								<?php if ($post->hasReadmore() && $this->params->get('post_readmore', true)) { ?>
									<div class="eb-post-more mt-20">
										<a class="btn btn-default" href="<?php echo $post->getPermalink();?>"><?php echo JText::_('COM_EASYBLOG_CONTINUE_READING');?></a>
									</div>
								<?php } ?>
							</div>

							<div class="eb-card__ft">
								<div class="eb-card__ft-content eb-card--border">
									<div class="row-table">
										<div class="col-cell">
											<?php if ($this->params->get('featured_post_date', true)) { ?>
											<time class="eb-meta-date" content="<?php echo $post->getDisplayDate($this->params->get('post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC4'));?>">
												<?php echo JText::sprintf('<time>' . $post->getDisplayDate($this->params->get('featured_post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC1')) . '</time>'); ?>
											</time>
											<?php } ?>

											<?php if ($this->params->get('featured_post_category', true)) { ?>
											<div>
												<div class="eb-post-category comma-seperator">
													<i class="fa fa-folder-open"></i>

													<?php foreach ($post->getCategories() as $category) { ?>
													<span>
														<a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle();?></a>
													</span>
													<?php } ?>
												</div>
											</div>
											<?php } ?>
										</div>
										<div class="col-cell cell-tight">
											<?php if ($this->params->get('featured_post_author_avatar', true)) { ?>
											<div class="eb-post-author-avatar">
												<a href="<?php echo $post->getAuthorPermalink(); ?>" class="eb-avatar">
													<img src="<?php echo $post->creator->getAvatar();?>" width="50" height="50" alt="<?php echo $this->html('string.escape', $post->getAuthorName());?>" />
												</a>
											</div>
											<?php } ?>
										</div>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<?php } ?>

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
<?php if ($featured && $this->params->get('featured_slider', true)) { ?>

<!-- <div uk-slider class="uk-position-relative uk-visible-toggle uk-light" > -->
<div class="uk-hidden uk-position-relative uk-visible-toggle uk-light uk-margin-bottom" tabindex="-1" uk-slider>
	<div class="uk-slider-items uk-grid uk-grid-match" uk-grid>
		<?php $i = 0; ?>
		<?php foreach ($featured as $post) { ?>
		<?php ++$i;?>
		<div class="uk-width-1-1 item<?php echo $i == 1 ? ' active' : '';?>">
			<div class="uk-panel">
				<a href="<?php echo $post->getPermalink();?>">

					<?php if ($post->image && $this->params->get('featured_post_image', true) || (!$post->image && $post->usePostImage() && $this->params->get('post_image', true))
							|| (!$post->image && !$post->usePostImage() && $this->params->get('post_image_placeholder', false) && $this->params->get('post_image', true))) { ?>

							<div class="uk-height-large uk-flex uk-flex-center uk-flex-middle uk-background-cover uk-light" data-src="<?php echo $post->getImage(EB::getCoverSize('cover_featured_size'), true, true, $this->config->get('cover_firstimage', 0));?>" uk-img>
							</div>


							<div class="uk-hidden eb-showcase-thumb eb-post-thumb<?php echo " is-" . $this->config->get('cover_featured_alignment');?>">
								<?php if (!$this->config->get('cover_featured_crop', false)) { ?>
									<a href="<?php echo $post->getPermalink();?>" class="eb-post-image"
										style="width: <?php echo $this->config->get('cover_featured_width') ? $this->config->get('cover_featured_width') : '300';?>px;"
									>
										<img src="<?php echo EB::image()->isImage($post->getImage()) ? $post->getImage(EB::getCoverSize('cover_featured_size')) : EB::getPlaceholderImage(false, 'video'); ?>" alt="<?php echo $this->escape($post->getImageTitle());?>" />
									</a>
								<?php } ?>

								<?php if ($this->config->get('cover_featured_crop', false)) { ?>
									<a href="<?php echo $post->getPermalink();?>" class="eb-post-image-cover"
										style="
											background-image: url('<?php echo EB::image()->isImage($post->getImage()) ? $post->getImage(EB::getCoverSize('cover_featured_size'), true, true, $this->config->get('cover_firstimage', 0)) : EB::getPlaceholderImage(false, 'video');?>');
											width: <?php echo $this->config->get('cover_featured_width') ? $this->config->get('cover_featured_width') : '300';?>px;
											height: <?php echo $this->config->get('cover_featured_height') ? $this->config->get('cover_featured_height') : '200';?>px;"
									></a>
								<?php } ?>
							</div>
					<?php } ?>

					<div class="uk-overlay uk-overlay-primary uk-position-bottom uk-text-center">
						<?php if ($this->params->get('featured_post_title', true)) { ?>
						<h2 class="uk-h3 uk-margin-remove">
							<a href="<?php echo $post->getPermalink();?>"><?php echo $post->title;?></a>
						</h2>

						<?php } ?>
						<p class="uk-margin-remove">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nostrum, nihil ratione non. Minima optio animi modi nihil voluptatem voluptas iste a, dolorum maxime delectus autem explicabo quas aliquam fugit error.</p>
					</div>
				</a>


			</div>
		</div>
		<?php } ?>
	</div>



	<a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slider-item="previous"></a>
	<a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slider-item="next"></a>

</div>

<div class="xuk-hidden eb-featured <?php echo $this->isMobile() ? 'is-mobile' : '';?>">
	<div id="eb-showcases" class="eb-showcases carousel slide mootools-noconflict"  data-featured-posts>
		<?php if ($this->params->get('featured_bottom_navigation', true) && count($featured) > 1) { ?>
			<ol class="eb-showcase-indicators carousel-indicators reset-list text-center">
				<?php for ($i = 0; $i < count($featured); $i++) { ?>
					<li data-target=".eb-showcases" data-bp-slide-to="<?php echo $i;?>" class="<?php echo $i == 0 ? 'active' : '';?>"></li>
				<?php } ?>
			</ol>
		<?php } ?>

		<div class="carousel-inner">
			<?php $i = 0; ?>
			<?php foreach ($featured as $post) { ?>
			<?php ++$i;?>
			<div class="item<?php echo $i == 1 ? ' active' : '';?>">
				<div class="eb-showcase">
					<?php if ($post->image && $this->params->get('featured_post_image', true) || (!$post->image && $post->usePostImage() && $this->params->get('post_image', true))
							|| (!$post->image && !$post->usePostImage() && $this->params->get('post_image_placeholder', false) && $this->params->get('post_image', true))) { ?>

							<div class="eb-showcase-thumb eb-post-thumb<?php echo " is-" . $this->config->get('cover_featured_alignment');?>">
								<?php if (!$this->config->get('cover_featured_crop', false)) { ?>
									<a href="<?php echo $post->getPermalink();?>" class="eb-post-image"
										style="width: <?php echo $this->config->get('cover_featured_width') ? $this->config->get('cover_featured_width') : '300';?>px;"
									>
										<img src="<?php echo EB::image()->isImage($post->getImage()) ? $post->getImage(EB::getCoverSize('cover_featured_size')) : EB::getPlaceholderImage(false, 'video'); ?>" alt="<?php echo $this->escape($post->getImageTitle());?>" />
									</a>
								<?php } ?>

								<?php if ($this->config->get('cover_featured_crop', false)) { ?>
									<a href="<?php echo $post->getPermalink();?>" class="eb-post-image-cover"
										style="
											background-image: url('<?php echo $post->getImage(EB::getCoverSize('cover_featured_size'), true, true, $this->config->get('cover_firstimage', 0));?>');
											width: <?php echo $this->config->get('cover_featured_width') ? $this->config->get('cover_featured_width') : '300';?>px;
											height: <?php echo $this->config->get('cover_featured_height') ? $this->config->get('cover_featured_height') : '200';?>px;"
									></a>
								<?php } ?>
							</div>
					<?php } ?>

					<div class="eb-showcase-content">
						<?php if ($this->params->get('featured_post_author_avatar', true)) { ?>
							<!--TODO: pull-left & pull-right settings for author avatar-->
							<a href="<?php echo $post->getAuthorPermalink(); ?>" class="eb-avatar pull-right">
								<img src="<?php echo $post->creator->getAvatar();?>" width="30" height="30" alt="<?php echo $this->html('string.escape', $post->getAuthorName());?>" />
							</a>
						<?php } ?>

						<?php if ($this->params->get('featured_post_title', true)) { ?>
						<h2 class="eb-showcase-title reset-heading">
							<a href="<?php echo $post->getPermalink();?>"><?php echo $post->title;?></a>
						</h2>
						<?php } ?>

						<div class="eb-showcase-meta">
							<?php if ($this->params->get('featured_post_author', true)) { ?>
							<div class="eb-post-author">
								<i class="fa fa-user"></i>
								<span>
									<a href="<?php echo $post->getAuthorPermalink(); ?>">
										<?php echo $post->getAuthorName(); ?>
									</a>
								</span>
							</div>
							<?php } ?>

							<?php if ($this->params->get('featured_post_date', true)) { ?>
							<div class="eb-post-date">
								<i class="fa fa-clock-o"></i>
								<?php echo JText::sprintf('<time>' . $post->getDisplayDate($this->params->get('featured_post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC1')) . '</time>'); ?>
							</div>
							<?php } ?>

							<?php if ($this->params->get('featured_post_category', true)) { ?>
							<div class="">
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


						<?php if ($this->params->get('featured_post_content', true)) { ?>
						<div class="eb-showcase-article">
							<?php echo $post->getIntro(true, true, 'intro', null, array('triggerPlugins' => false, 'forceTruncateByChars' => true, 'forceCharsLimit' => $this->params->get('featured_post_content_limit', 250) )); ?>
						</div>
						<?php } ?>

						<!--TODO: .eb-post-more should have specific height to cover .eb-showcase-control-->
						<div class="eb-showcase-more uk-margin-top">
							<?php if ($this->params->get('featured_post_readmore', true)) { ?>
							<a class="uk-button uk-button-default uk-button-small" href="<?php echo $post->getPermalink();?>"><?php echo JText::_('COM_EASYBLOG_CONTINUE_READING');?></a>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>

		<?php if ($this->config->get('listing_featured_bottom_navigation') && $i > 1) { ?>
			<div class="eb-showcase-control uk-button-group">
				<a class="uk-button uk-button-default uk-button-small" href="#eb-showcases" role="button" data-bp-slide="prev">
					<span class="fa fa-angle-left"></span>
				</a>
				<a class="uk-button uk-button-default uk-button-small" href="#eb-showcases" role="button" data-bp-slide="next">
					<span class="fa fa-angle-right"></span>
				</a>
			</div>
		<?php } ?>
	</div>
</div>
<?php } ?>

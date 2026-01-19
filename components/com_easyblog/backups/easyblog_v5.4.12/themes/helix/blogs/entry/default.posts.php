<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div data-eb-post-section data-url="<?php echo $post->getExternalPermalink(); ?>" data-page-title="<?php echo $this->html('string.escape', $post->getPagePostTitle()); ?>" data-permalink="<?php echo $post->getPermalink(); ?>" data-post-title="<?php echo $this->html('string.escape', $post->getTitle()); ?>">
	<div class="eb-adsense-head clearfix">
		<?php echo $adsense->header;?>
	</div>

	<div data-blog-post>

		<?php if ($this->config->get('main_show_reading_progress')) { ?>
		<div class="eb-reading-progress-sticky hide" data-eb-spy="affix" data-offset-top="240">
			<progress value="0" max="100" class="eb-reading-progress" data-blog-reading-progress style="<?php echo 'top:' . $this->config->get('main_reading_progress_offset') . 'px'; ?>">
				<div class="eb-reading-progress__container">
					<span class="eb-reading-progress__bar"></span>
				</div>
			</progress>
		</div>
		<?php } ?>

		<div id="entry-<?php echo $post->id; ?>" class="article-details" data-blog-posts-item data-id="<?php echo $post->id;?>" data-uid="<?php echo $post->getUid();?>">

			<div data-blog-reading-container>
				<?php if (!$preview && $post->isPending() && $post->canModerate()) { ?>
					<?php echo $this->output('site/blogs/entry/moderate'); ?>
				<?php } ?>

				<?php if ($post->isUnpublished()) { ?>
					<?php echo $this->output('site/blogs/entry/entry.unpublished'); ?>
				<?php } ?>

				<?php
				if ($preview) {
					if (!$post->canModerate() && $post->isPending()) {
						echo $this->output('site/blogs/entry/preview.pending.approval');
					} else if ($post->isPostPublished()) {
						echo $this->output('site/blogs/entry/preview.revision');
					} else {
						echo $this->output('site/blogs/entry/preview.unpublished');
					}
				}
				?>

				<?php echo $this->output('site/blogs/entry/post.cover', array('post' => $post)); ?>

				<?php if ($hasAdminTools || $preview) { ?>
				<div class="eb-entry-tools row-table">
					<?php if (!$preview) { ?>
					<div class="col-cell cell-tight">
						<?php echo $this->output('site/blogs/admin.tools', array('post' => $post, 'return' => $post->getPermalink(false))); ?>
					</div>
					<?php } ?>
				</div>
				<?php } ?>

				<div class="article-header">
					<?php if ($post->getType() == 'link' && $this->entryParams->get('show_title', true)) { ?>
						<h1 itemprop="headline" id="title-<?php echo $post->id; ?>" class="<?php echo ($post->isFeatured()) ? ' featured-item' : '';?> "><?php echo $post->title; ?></h1>
					<?php } ?>

					<?php if ((in_array($post->getType(), array('photo', 'standard', 'video', 'email'))) && $this->entryParams->get('post_title', true)) { ?>
						<h1 itemprop="headline" id="title-<?php echo $post->id; ?>" class="<?php echo ($post->isFeatured()) ? ' featured-item' : '';?> "><?php echo $post->title; ?></h1>
					<?php } ?>

					<?php if ($post->getType() == 'quote' && $this->entryParams->get('show_title', true)) { ?>
						<div class="eb-placeholder-quote">
							<h1 id="title-<?php echo $post->id; ?>" class="eb-placeholder-quote-text eb-post-title reset-heading"><?php echo nl2br($post->title); ?></h1>
							<?php if ($post->text) {  ?>
								<div class="eb-placeholder-quote-source"><?php echo $post->text; ?></div>
							<?php } ?>
						</div>
					<?php } ?>

					<?php echo $post->event->afterDisplayTitle; ?>
				</div>

				<div class="article-info">
					<?php if ($this->entryParams->get('post_author', true)) { ?>
					<span class="createdby" itemprop="author" itemscope="" itemtype="https://schema.org/Person">
						<a href="<?php echo $post->getAuthorPermalink();?>" rel="author"><?php echo $post->getAuthorName();?></a>
					</span>
					<?php } ?>

					<?php if ($this->entryParams->get('post_category', true)) { ?>
						<span class="category-name" title="Category: Blog">
							<?php foreach ($post->categories as $cat) { ?>
							<span><a href="<?php echo $cat->getPermalink();?>"><?php echo $cat->getTitle();?></a></span>
							<?php } ?>
						</span>
					<?php } ?>

					<?php if ($this->entryParams->get('post_date', true)) { ?>
					<span class="published">
						<time class="" datetime="<?php echo $post->getCreationDate($this->entryParams->get('post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC4'));?>">
							<?php echo $post->getDisplayDate($this->entryParams->get('post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC1')); ?>
						</time>
					</span>
					<?php } ?>

					<?php if ($this->entryParams->get('post_hits', true)) { ?>
					<span class="hits">
						<span class="fa fa-eye-o" aria-hidden="true"></span>
						<meta itemprop="interactionCount" content="UserPageVisits:<?php echo $post->hits;?>"> <?php echo JText::sprintf('COM_EASYBLOG_POST_HITS', $post->hits);?>
					</span>
					<?php } ?>

					<?php if ($this->config->get('main_comment') && $post->totalComments !== false && $this->entryParams->get('post_comment_counter', true) && $post->allowcomment) { ?>
					<span class="">
						<?php if ($this->config->get('comment_disqus')) { ?>
							<span><?php echo $post->totalComments; ?></span>
						<?php } else { ?>
							<span>
							<a href="<?php echo EBFactory::getURI(true);?>#comments"><?php echo $this->getNouns('COM_EASYBLOG_COMMENT_COUNT', $post->totalComments, true); ?></a>
							</span>
						<?php } ?>
					</span>
					<?php } ?>
				</div>

				<?php if ($this->entryParams->get('show_reading_time') || $post->isFeatured) { ?>
				<div class="es-post-state">
					<?php if ($this->entryParams->get('show_reading_time')) { ?>
					<div class="es-post-state__item label">
						<div class="eb-reading-indicator">
							<span class="eb-reading-indicator__icon"><i class="fa fa-clock-o"></i></span>
							<span class="eb-reading-indicator__time"><?php echo $post->getReadingTime(); ?></span>
							<span class="eb-reading-indicator__count">(<?php echo JText::sprintf('COM_EB_TOTAL_WORDS', $post->getTotalWords()); ?>)</span>
						</div>
					</div>
					<?php } ?>

					<?php if ($post->isFeatured) { ?>
					<div class="es-post-state__item">
						<div class="eb-entry-featured label">
							<?php if ($post->isFeatured) { ?>
							<i class="fa fa-star" data-original-title="<?php echo JText::_('COM_EASYBLOG_POST_IS_FEATURED');?>" data-placement="bottom" data-eb-provide="tooltip"></i>
							<?php echo JText::_('COM_EASYBLOG_FEATURED_FEATURED');?>&nbsp;
							<?php } ?>
						</div>
					</div>
					<?php } ?>
				</div>
				<?php } ?>

				<?php if ((!$preview && $this->config->get('main_ratings') && $this->entryParams->get('post_ratings', true)) || $this->entryParams->get('post_social_buttons')) { ?>
				<div class="article-ratings-social-share d-flex justify-content-end">
					<?php if (!$preview && $this->config->get('main_ratings') && $this->entryParams->get('post_ratings', true)) { ?>
					<div class="mr-auto align-self-center">
						<div class="article-ratings" >
							<div class="eb-entry-ratings">
								<?php echo $this->output('site/ratings/frontpage', array('post' => $post)); ?>
							</div>
						</div>
					</div>
					<?php } ?>

					<?php if ($this->entryParams->get('post_social_buttons', true)) { ?>
					<div>
						<div class="article-social-share">
							<div class="social-share-icon">
								<?php echo EB::socialbuttons()->html($post, 'entry'); ?>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
				<?php } ?>


				<?php echo $this->renderModule('easyblog-before-entry'); ?>


				<div itemprop="articleBody" class="eb-entry-body type-<?php echo $post->posttype; ?> clearfix">
					<div class="eb-entry-article clearfix" data-blog-content>

						<?php echo $post->event->beforeDisplayContent; ?>

						<?php echo EB::renderModule('easyblog-before-content'); ?>

						<?php if (in_array($post->posttype, array('photo', 'standard', 'twitter', 'email', 'link', 'video'))) { ?>


							<?php if(!empty($post->toc)){ echo $post->toc; } ?>

							<!--LINK TYPE FOR ENTRY VIEW-->
							<?php if ($post->getType() == 'link') { ?>
								<div class="eb-post-headline">
									<div class="eb-post-headline-source">
										<a href="<?php echo $post->getAsset('link')->getValue(); ?>" target="_blank"><?php echo $post->getAsset('link')->getValue();?></a>
									</div>
								</div>
							<?php } ?>

							<?php echo $content; ?>
						<?php } else { ?>
							<?php if(!empty($post->toc)){ echo $post->toc; } ?>
						<?php } ?>

						<?php echo $this->renderModule('easyblog-after-content'); ?>

						<?php if ($post->fields && $this->entryParams->get('post_fields', true)) { ?>
							<?php echo $this->output('site/blogs/entry/fields', array('fields' => $post->fields)); ?>
						<?php } ?>
					</div>



					<?php echo $this->output('site/blogs/entry/location', array('post' => $post)); ?>

					<?php echo $this->output('site/blogs/entry/copyright', array('post' => $post)); ?>

					<?php if ($this->config->get('reactions_enabled') && $this->entryParams->get('post_reactions', true)) { ?>
						<?php echo EB::reactions($post)->html();?>
					<?php } ?>

					<?php if ($this->entryParams->get('post_tags', true)) { ?>
					<div class="eb-entry-tags">
						<?php echo $this->output('site/blogs/tags/item', array('tags' => $post->tags)); ?>
					</div>
					<?php } ?>

					<?php if (!$preview) { ?>
						<?php echo EB::emotify()->html($post); ?>
					<?php } ?>


				</div>
				<hr>
				<?php if ($hasEntryTools || $preview) { ?>
				<?php echo $this->output('site/blogs/entry/tools', array('return' => $post->getPermalink(false))); ?>
				<?php } ?>

				<?php echo $this->output('site/blogs/entry/navigation'); ?>

			</div>

			<?php if ($this->entryParams->get('post_author_box', true) && !$post->hasAuthorAlias()) { ?>
				<?php echo $this->output('site/blogs/entry/author', array('post' => $post)); ?>
			<?php } ?>


			<?php if ($this->entryParams->get('post_related', true) && $relatedPosts) { ?>
			<h4 class="eb-section-heading reset-heading"><?php echo JText::_('COM_EASYBLOG_RELATED_POSTS');?></h4>

			<div class="eb-entry-related clearfix <?php echo $this->isMobile() ? 'is-mobile' : '';?>">
				<?php foreach ($relatedPosts as $related) { ?>
				<div>
					<?php if ($this->entryParams->get('post_related_image', true)) { ?>
						<?php if (EB::image()->isImage($related->getImage())) { ?>
							<a href="<?php echo $related->getPermalink();?>" class="eb-related-thumb" style="background-image: url('<?php echo $related->postimage;?>') !important;"></a>
						<?php } else { ?>
							<?php echo EB::media()->renderVideoPlayer($related->getImage(), array('width' => '260','height' => '200','ratio' => '','muted' => false,'autoplay' => false,'loop' => false), false); ?>
						<?php } ?>
					<?php } ?>

					<h3 class="eb-related-title">
						<a href="<?php echo $related->getPermalink();?>"><?php echo $related->title;?></a>
					</h3>
					<div class="text-muted">
						<a class="eb-related-category text-inherit" href="<?php echo $related->getPrimaryCategory()->getPermalink();?>"><?php echo $related->getPrimaryCategory()->getTitle();?></a>
					</div>
				</div>
				<?php } ?>
			</div>

			<?php } ?>
		</div>

		<?php echo $adsense->beforecomments; ?>

		<?php echo $post->event->afterDisplayContent; ?>

		<?php if (!$preview && $this->config->get('main_comment') && $this->entryParams->get('post_comment_form', true)) { ?>
			<?php if ($post->allowComments() && $post->canEdit() && !$post->allowcomment) { ?>
				<div class="eb-comment-notice eb-alert alert-warning mb-0">
					<?php echo JText::_('COM_EB_COMMENTS_LOCKED_BUT_VIEWED_BY_OWNER_ADMIN'); ?>
				</div>
			<?php } else if (!$post->allowComments()) { ?>
				<div class="eb-comment-notice eb-alert alert-warning mb-0">
					<?php echo JText::_('COM_EB_COMMENTS_LOCKED'); ?>
				</div>
			<?php } ?>
			
			<a class="eb-anchor-link" name="comments" id="comments" data-allow-comment="<?php echo $post->allowcomment;?>">&nbsp;</a>
			<?php echo EB::comment()->getCommentHTML($post);?>
		<?php } ?>
	</div>

	<div class="eb-adsense-foot clearfix">
		<?php echo $adsense->footer;?>
	</div>
</div>

<script type="application/ld+json">
	{
		"@context": "http://schema.org",
		"mainEntityOfPage": "<?php echo $post->getPermalink(true, true); ?>",
		"@type": ["BlogPosting", "Organization"],
		"name": "<?php echo EB::showSiteName(); ?>",
		"headline": "<?php echo $this->html('string.escape', $post->getTitle());?>",
		"image": "<?php echo $post->getImage($this->config->get('cover_size_entry', 'large'), true, true);?>",
		"editor": "<?php echo $post->getAuthor()->getName();?>",
		"genre": "<?php echo $post->getPrimaryCategory()->title;?>",
		"wordcount": "<?php echo $post->getTotalWords();?>",
		"publisher": {
			"@type": "Organization",
			"name": "<?php echo EB::showSiteName(); ?>",
			"logo": <?php echo $post->getSchemaLogo(); ?>
		},
		"datePublished": "<?php echo $post->getPublishDate(true)->format('Y-m-d');?>",
		"dateCreated": "<?php echo $post->getCreationDate(true)->format('Y-m-d');?>",
		"dateModified": "<?php echo $post->getModifiedDate()->format('Y-m-d');?>",
		"description": "<?php echo EB::jconfig()->get('MetaDesc'); ?>",
		"articleBody": "<?php echo EB::normalizeSchema($schemaContent); ?>",
		"author": {
			"@type": "Person",
			"name": "<?php echo $post->getAuthor()->getName();?>",
			"image": "<?php echo $post->creator->getAvatar();?>"
		}<?php if (!$preview && $this->config->get('main_ratings') && $this->entryParams->get('post_ratings', true) && $ratings->total > 0) { ?>,
			"aggregateRating": {
				"@type": "http://schema.org/AggregateRating",
				"ratingValue": "<?php echo round($ratings->ratings / 2, 2); ?>",
				"worstRating": "1",
				"bestRating": "5",
				"ratingCount": "<?php echo $ratings->total; ?>"
			}
		<?php } ?>
	}
</script>

<?php if ($prevId) { ?>
<hr class="eb-hr" />
<?php } ?>

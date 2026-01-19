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
<?php if ($this->params->get('authors_search', true) || $this->params->get('authors_sorting', true)) { ?>
<form name="authors" method="post" action="<?php echo JRoute::_('index.php'); ?>">
	<div class="uk-grid" uk-grid="">

		<?php if ($this->params->get('authors_search', true)) { ?>
		
		<div class="uk-width-expand@m">
			<div class="uk-grid uk-grid-small" uk-grid="">
				<div class="uk-width-expand@m uk-first-column">
					<div class="uk-inline uk-width-1-1">
						<span class="uk-form-icon uk-icon" uk-icon="icon: user"></span>

						<input class="uk-input" name="search" placeholder="<?php echo JText::_('COM_EASYBLOG_SEARCH_BLOGGERS', true);?>" value="<?php echo $this->html('string.escape', $search);?>" >
					</div>
				</div>

				<div class="uk-width-auto@m">
					<button type="submit" class="uk-button uk-button-default">
						<?php echo JText::_('COM_EASYBLOG_SEARCH_BUTTON', true);?>
					</button>
				</div>
								
			</div>
		</div>
		<?php } ?>
		
		<?php if ($this->params->get('authors_sorting', true)) { ?>
		<div class="uk-width-auto@m">
			<div class="uk-form-controls">
				<select class="uk-select" data-authors-sorting>
					<option value="default" data-url="<?php echo EBR::_('index.php?option=com_easyblog&view=blogger');?>"><?php echo JText::_('COM_EASYBLOG_BLOGGERS_SORT_BY');?></option>
					<option value="alphabet" <?php echo $sort == 'alphabet' ? 'selected="selected"' : '';?> data-url="<?php echo EBR::_('index.php?option=com_easyblog&view=blogger&sort=alphabet', false); ?>">
						<?php echo JText::_('COM_EASYBLOG_BLOGGERS_ORDER_BY_NAME');?>
					</option>
					<option value="mostactive" <?php echo $sort == 'active' ? 'selected="selected"' : '';?> data-url="<?php echo EB::_('index.php?option=com_easyblog&view=blogger&sort=active', false); ?>">
						<?php echo JText::_('COM_EASYBLOG_BLOGGERS_ORDER_BY_ACTIVE');?>
					</option>
					<option value="latestblogger" <?php echo $sort == 'latest' ? 'selected="selected"' : '';?> data-url="<?php echo EB::_('index.php?option=com_easyblog&view=blogger&sort=latest', false); ?>">
						<?php echo JText::_('COM_EASYBLOG_BLOGGERS_ORDER_BY_LATEST');?>
					</option>
					<option value="latestpost" <?php echo $sort == 'latestpost' ? 'selected="selected"' : '';?> data-url="<?php echo EB::_('index.php?option=com_easyblog&view=blogger&sort=latestpost', false); ?>">
						<?php echo JText::_('COM_EASYBLOG_BLOGGERS_ORDER_BY_LATEST_POST');?>
					</option>
					<option value="ordering" <?php echo $sort == 'ordering' ? 'selected="selected"' : '';?> data-url="<?php echo EB::_('index.php?option=com_easyblog&view=blogger&sort=ordering', false); ?>">
						<?php echo JText::_('COM_EB_BLOGGERS_ORDER_BY_COLUMN_ORDERING');?>
					</option>
				</select>
			</div>
		</div>
		<?php } ?>
	</div>

	<?php echo $this->html('form.action', 'search.blogger'); ?>
</form>

<div class="uk-divider uk-margin-medium"></div>
<?php } ?>




<div class="eb-authors" data-authors>
	<?php if ($authors) { ?>
		<?php foreach ($authors as $author) { ?>

			<div class="eb-author <?php echo $this->isMobile() ? 'is-mobile' : '';?>" data-author-item data-id="<?php echo $author->id;?>">

				<?php echo $this->html('headers.author', $author, array(
					'avatar' => $this->params->get('author_avatar', true),
					'rss' => $this->params->get('author_subscribe_rss', true),
					'subscription' => $this->params->get('author_subscribe_email', true),
					'twitter' => $this->params->get('author_twitter', true),
					'website' => $this->params->get('author_website', true),
					'biography' => $this->params->get('author_bio', true),
					'featureAction' => true
					)
				); ?>

				<?php if ($this->params->get('author_posts', true) || $this->params->get('author_categories', true) || $this->params->get('author_tags', true)) { ?>
				<div class="eb-authors-stats">
					<ul class="uk-child-width-expand" uk-tab>
						<?php if ($this->params->get('author_posts', true)) { ?>
						<li class="active">
							<a class="" href="#posts-<?php echo $author->id;?>" data-bp-toggle="tab">
								<?php echo JText::_('COM_EASYBLOG_BLOGGERS_TOTAL_POSTS');?>
							</a>
						</li>
						<?php } ?>

						<?php if ($this->params->get('author_categories', true)) { ?>
						<li>
							<a class="" href="#categories-<?php echo $author->id;?>"
								data-bp-toggle="tab"
								data-author-id="<?php echo $author->id; ?>"
								<?php echo ($author->categoryCount) ? 'data-tab-category' : ''; ?>
							>
								<?php echo JText::_('COM_EASYBLOG_BLOGGERS_TOTAL_CATEGORIES');?>
							</a>
						</li>
						<?php } ?>

						<?php if ($this->params->get('author_tags', true)) { ?>
						<li>
							<a class="" href="#tags-<?php echo $author->id;?>"
								data-bp-toggle="tab"
								data-author-id="<?php echo $author->id; ?>"
								<?php echo ($author->tagCount) ? 'data-tab-tag' : ''; ?>
							>
								<?php echo JText::_('COM_EASYBLOG_BLOGGERS_TAGS');?>
							</a>
						</li>
						<?php } ?>
					</ul>

					<div class="eb-stats-content">
						<?php if ($this->params->get('author_posts', true)) { ?>
						<div class="tab-pane eb-stats-posts active" id="posts-<?php echo $author->id;?>">

							<?php if ($author->blogs) { ?>
							<?php $authorp = 1; ?>
								<ul class="uk-list uk-list-divider uk-margin-small">
									<?php foreach ($author->blogs as $post) { ?>
										<?php if ($authorp <= $limitPreviewPost) { ?>
											<li>
												<div class="uk-grid-small" uk-grid>
													<div class="uk-width-expand" uk-leader>
														<a href="<?php echo EB::_('index.php?option=com_easyblog&view=entry&id=' . $post->id);?>">
															<span uk-icon="icon: file-text" class="uk-margin-small-right"></span> 
															<span><?php echo $post->title;?></span>
														</a>
													</div>
													<div>
														<time ><?php echo $post->getDisplayDate($this->config->get('blogger_post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC3'));?></time>
													</div>
												</div>
											</li>
										<?php } ?>
									<?php $authorp++; ?>
									<?php } ?>
								</ul>

								<a href="<?php echo $author->getPermalink();?>" class="uk-button uk-button-link uk-button-small uk-width-1-1">
									<?php echo JText::_('COM_EASYBLOG_VIEW_ALL_POSTS');?>
								</a>

							<?php } else { ?>
								<div class="eb-empty">
									<?php echo JText::_('COM_EASYBLOG_NO_RECORDS_FOUND');?>
								</div>
							<?php } ?>
						</div>
						<?php } ?>

						<?php if ($this->params->get('author_categories', true)) { ?>
						<div class="tab-pane eb-labels eb-stats-categories <?php echo !$this->params->get('author_posts', true) ? 'active' : '';?>" data-category-container id="categories-<?php echo $author->id;?>">
							<?php if ($author->categoryCount > 0) { ?>
								<div class="center">
									<i class="eb-loader-o"></i>
								</div>
							<?php } else { ?>
								<div class="eb-empty"><?php echo JText::_('COM_EASYBLOG_BLOGGERS_DID_NOT_CREATE_CATEGORY'); ?></div>
							<?php } ?>
						</div>
						<?php } ?>

						<?php if ($this->params->get('author_tags', true)) { ?>
						<div class="tab-pane eb-labels eb-stats-tags <?php echo (!$this->params->get('author_posts', true) && !$this->params->get('author_categories', true)) ? 'active' : '';?>" data-tag-container id="tags-<?php echo $author->id;?>">
							<?php if ($author->tagCount > 0) { ?>
								<div class="center">
									<i class="eb-loader-o"></i>
								</div>
							<?php } else { ?>
								<div class="eb-empty"><?php echo JText::_('COM_EASYBLOG_AUTHOR_DID_NOT_USE_ANY_TAGS_YET'); ?></div>
							<?php } ?>
						</div>
						<?php } ?>
					</div>
				</div>
				<?php } ?>
			</div>
		<?php } ?>
	<?php } else { ?>
		<div class="eb-empty">
			<i class="fa fa-users"></i>
			<?php echo JText::_('COM_EASYBLOG_NO_AUTHORS_CURRENTLY'); ?>
		</div>
	<?php } ?>

	<?php if ($pagination) { ?>
	<div class="eb-pagination clearfix">
		<?php echo $pagination; ?>
	</div>
	<?php } ?>
</div>

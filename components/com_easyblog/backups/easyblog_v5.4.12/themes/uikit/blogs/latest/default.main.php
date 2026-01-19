<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div>
	<article class="uk-article" typeof="Article" data-blog-posts-item data-id="<?php echo $post->id;?>" <?php echo $index == 0 ? 'data-eb-posts-section data-url="' . $currentPageLink . '"' : ''; ?>>
		
		<meta class="uk-margin-remove-adjacentx">

		<?php echo $this->output('site/blogs/latest/post.cover', array('post' => $post)); ?>

		<h2 class="uk-margin-medium-top uk-margin-remove-bottom uk-article-title">
			<a class="uk-link-reset" href="<?php echo $post->getPermalink();?>"><?php echo nl2br($post->title);?></a>
		</h2>
		<div class="uk-grid uk-margin-top">
			<?php if ($this->params->get('post_date', true) || $this->params->get('post_category', true)) { ?>
				<div class="uk-width-expand@m">
					<ul class="uk-margin-remove-bottom uk-subnav uk-subnav-divider">
						<?php if ($this->params->get('post_date', true)) { ?>
							<li>
								<time content="<?php echo $post->getDisplayDate($this->params->get('post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC4'));?>">
									<?php echo $post->getDisplayDate($this->params->get('post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC1')); ?>
								</time>
							</li>
						<?php } ?>

						<?php foreach ($post->categories as $category) { ?>
							<li><a href="<?php echo $category->getPermalink();?>" rel="category"><?php echo $category->getTitle();?></a></li>
						<?php } ?>
					</ul>
				</div>
			<?php } ?>

			<div class="uk-width-auto@m">
				<?php echo $this->output('site/blogs/admin.tools', array('post' => $post, 'return' => $return)); ?>
			</div>
		</div>
		
		<?php if ($post->getType() == 'quote') { ?>
			<div class="uk-margin-small-top" property="text">
				<?php echo $post->getContent(); ?>
			</div>
		<?php } ?>

		<?php if ($post->getType() == 'link') { ?>
			<div class="uk-margin-small-top" property="text">
				<a href="<?php echo $post->getAsset('link')->getValue(); ?>" target="_blank"><?php echo $post->getAsset('link')->getValue();?></a>
			</div>
		<?php } ?>

		<?php if ($post->getType() == 'twitter') { ?>
			<?php $screen_name = $post->getAsset('screen_name')->getValue();
				  $created_at = EB::date($post->getAsset('created_at')->getValue(), true)->format(JText::_('DATE_FORMAT_LC'));
			?>
			<div class="uk-margin-small-top" property="text">
				<?php echo $post->content;?>

				<?php echo '@'.$screen_name.' - '.$created_at; ?>
				&middot;
				<a href="<?php echo $post->getPermalink();?>">
					<?php echo JText::_('COM_EASYBLOG_LINK_TO_POST'); ?>
				</a>
			</div>
		<?php } ?>

		<?php if ($post->getType() == 'video') { ?>
			<div class="uk-margin-small-top" property="text">
				<?php foreach ($post->videos as $video) { ?>
				<div class="eb-responsive-video">
					<?php echo $video->html;?>
				</div>
				<?php } ?>
			</div>
		<?php } ?>
		
		<?php if (in_array($post->getType(), array('photo', 'standard', 'twitter', 'email', 'link'))) { ?>
			<div class="uk-margin-small-top" property="text">
				<?php echo $post->getIntro();?>
			</div>
		<?php } ?>

		<?php if ($post->hasReadmore() && $this->params->get('post_readmore', true)) { ?>
			<p class="uk-margin-medium">
				<a class="uk-button uk-button-default" href="<?php echo $post->getPermalink();?>"><?php echo JText::_('COM_EASYBLOG_CONTINUE_READING');?></a>
			</p>
		<?php } ?>
	</article>
</div>
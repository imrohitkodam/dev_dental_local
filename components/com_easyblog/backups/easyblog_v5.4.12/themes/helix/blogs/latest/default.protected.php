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
<!-- TODO Need to update DOM to follow default.main -->
<div class="article" itemprop="blogPost" itemscope="" itemtype="https://schema.org/BlogPosting" data-blog-posts-item data-id="<?php echo $post->id;?>" <?php echo $index == 0 ? 'data-eb-posts-section data-url="' . $currentPageLink . '"' : ''; ?>>

	<?php if (in_array($post->getType(), array('photo', 'standard', 'twitter', 'email', 'link'))) { ?>
		<div class="eb-post-body type-<?php echo $post->posttype; ?>">

			<?php echo $this->output('site/blogs/latest/post.cover', array('post' => $post)); ?>
		</div>
	<?php } ?>

	<div class="article-body">
		<?php echo $this->output('site/blogs/admin.tools', array('post' => $post, 'return' => $return)); ?>
		<div class="article-header">
			<h2>
				<a href="<?php echo $post->getPermalink();?>" class=""><?php echo nl2br($post->title);?></a>
			</h2>
		</div>
		

		
		
	</div>

	<div class="eb-post-protected">
		<?php echo $this->output('site/blogs/tools/protected.form', array('post' => $post)); ?>
	</div>
	<?php echo $this->output('site/blogs/post.schema', array('post' => $post)); ?>
</div>

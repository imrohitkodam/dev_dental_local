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

<?php if (($post->image || (!$post->image && $this->entryParams->get('post_image_placeholder', false))) && $this->entryParams->get('post_image', true)) { ?>

	<?php if ($post->getImage() && EB::image()->isImage($post->getImage())) { ?>
	<div class="uk-text-center uk-margin-top" property="image" typeof="ImageObject">
		<a href="<?php echo $post->getPermalink();?>">
			<div class="uk-height-medium uk-flex uk-flex-center uk-flex-middle uk-background-cover uk-light" data-src="<?php echo $post->getImage(EB::getCoverSize('cover_size'), true, false, false);?>" uk-img>
				<span><?php echo $this->escape($post->getImageCaption());?></span>
			</div>
		</a>
	</div>
	<?php } else { ?>
		<div class="uk-text-center uk-margin-top">
			<?php echo EB::media()->renderVideoPlayer($post->getImage(), array('width' => '260','height' => '200','ratio' => '','muted' => false,'autoplay' => false,'loop' => false), false); ?>
		</div>
	<?php } ?>


<?php } ?>

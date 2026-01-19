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
<div class="article-intro-image">
<?php if ($post->image && $this->params->get('post_image', true) || (!$post->image && $post->usePostImage() && $this->params->get('post_image', true))
		|| (!$post->image && !$post->usePostImage() && $this->params->get('post_image_placeholder', false) && $this->params->get('post_image', true))) { ?>

	<div class="eb-post-thumb<?php echo $this->config->get('cover_width_full') ? " is-full" : " is-" . $this->config->get('cover_alignment')?> mb-0">
		<?php if (!$this->config->get('cover_crop', false)) { ?>
			<a
				<?php if (EB::image()->isImage($post->getImage())) { ?>
					href="<?php echo $post->getPermalink();?>"
				<?php }?>
				class="eb-post-image"
				title="<?php echo $this->escape($post->getImageTitle());?>"
				caption="<?php echo $this->escape($post->getImageCaption());?>"
				style="
					<?php if ($this->config->get('cover_width_full')) { ?>
					width: 100%;
					<?php } else { ?>
					width: <?php echo $this->config->get('cover_width') ? $this->config->get('cover_width') : '260';?>px;
					<?php } ?>"
			>
				<?php if ($post->getImage() && EB::image()->isImage($post->getImage())) { ?>
					<img src="<?php echo $post->getImage(EB::getCoverSize('cover_size'), true, true, $this->config->get('cover_firstimage', 0));?>" alt="<?php echo $this->escape($post->getImageTitle());?>" />

					<?php if ($post->getImageCaption()) { ?>
						<span class="eb-post-thumb-caption"><?php echo $post->getImageCaption(); ?></span>
					<?php } ?>

				<?php } else { ?>
					<?php echo EB::media()->renderVideoPlayer($post->getImage(), array('width' => '260','height' => '200','ratio' => '','muted' => false,'autoplay' => false,'loop' => false), false); ?>
				<?php } ?>

			</a>
		<?php } ?>

		<?php if ($this->config->get('cover_crop', false)) { ?>
			<?php if ($post->getImage() && EB::image()->isImage($post->getImage())) { ?>
				<a href="<?php echo $post->getPermalink();?>"
					class="eb-post-image-cover"
					title="<?php echo $this->escape($post->getImageTitle());?>"
					caption="<?php echo $this->escape($post->getImageCaption());?>"
					style="
						background-image: url('<?php echo $post->getImage(EB::getCoverSize('cover_size'), true, true, $this->config->get('cover_firstimage', 0));?>');
						<?php if ($this->config->get('cover_width_full')) { ?>
						width: 100%;
						<?php } else { ?>
						width: <?php echo $this->config->get('cover_width') ? $this->config->get('cover_width') : '260';?>px;
						<?php } ?>
						height: <?php echo $this->config->get('cover_height') ? $this->config->get('cover_height') : '200';?>px;"
				></a>

				<?php if ($post->getImageCaption()) { ?>
					<span class="eb-post-thumb-caption"><?php echo $post->getImageCaption(); ?></span>
				<?php } ?>

			<?php } else { ?>
				<a
				class="eb-post-image"
				title="<?php echo $this->escape($post->getImageTitle());?>"
				caption="<?php echo $this->escape($post->getImageCaption());?>"
				style="
					<?php if ($this->config->get('cover_width_full')) { ?>
					width: 100%;
					<?php } else { ?>
					width: <?php echo $this->config->get('cover_width') ? $this->config->get('cover_width') : '260';?>px;
					<?php } ?>">
				<?php echo EB::media()->renderVideoPlayer($post->getImage(), array('width' => '260','height' => '200','ratio' => '','muted' => false,'autoplay' => false,'loop' => false), false); ?>
				</a>
			<?php } ?>
		<?php } ?>
	</div>
<?php } ?>
</div>

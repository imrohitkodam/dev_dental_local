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

	<div class="eb-image eb-post-thumb<?php echo $this->config->get('cover_width_entry_full') ? " is-full" : " is-" . $this->config->get('cover_alignment_entry')?>" data-eb-entry-cover>
		<?php if (!$this->config->get('cover_crop_entry', false)) { ?>
			<a
				<?php if (EB::image()->isImage($post->getImage())) { ?>
					class="eb-post-image eb-image-popup-button"
					href="<?php echo $post->getImage('original', true, false, false);?>"
					target="_blank"
				<?php }?>
				title="<?php echo $this->escape($post->getImageTitle());?>"
				caption="<?php echo $this->escape($post->getImageCaption());?>"
				style="
					<?php if ($this->config->get('cover_width_entry_full')) { ?>
					width: 100%;
					<?php } else { ?>
					width: <?php echo $this->config->get('cover_width_entry') ? $this->config->get('cover_width_entry') : '260';?>px;
					<?php } ?>"
			>
				<?php if ($post->getImage() && EB::image()->isImage($post->getImage())) { ?>
					<img src="<?php echo $post->getImage(EB::getCoverSize('cover_size_entry'), true, false, false);?>" alt="<?php echo $this->escape($post->getCoverImageAlt());?>" />
					<meta content="<?php echo $post->getImage(EB::getCoverSize('cover_size_entry'), true, true, false);?>" alt="<?php echo $this->escape($post->getCoverImageAlt());?>"/>

					<?php if ($post->getImageCaption()) { ?>
						<span class="eb-post-thumb-caption"><?php echo $post->getImageCaption(); ?></span>
					<?php } ?>

				<?php } else { ?>
					<?php echo EB::media()->renderVideoPlayer($post->getImage(), array('width' => '260','height' => '200','ratio' => '','muted' => false,'autoplay' => false,'loop' => false), false); ?>
				<?php } ?>
			</a>
		<?php } ?>

		<?php if ($this->config->get('cover_crop_entry', false)) { ?>
			<a
				<?php if (EB::image()->isImage($post->getImage())) { ?>
					class="eb-post-image-cover eb-image-popup-button"
					href="<?php echo $post->getImage('original', true, false, false);?>"
					style="
					display: inline-block;
					background-image: url('<?php echo $post->getImage(EB::getCoverSize('cover_size_entry'), true, true, false);?>');
					<?php if ($this->config->get('cover_width_entry_full')) { ?>
					width: 100%;
					<?php } else { ?>
					width: <?php echo $this->config->get('cover_width_entry') ? $this->config->get('cover_width_entry') : '260';?>px;
					<?php } ?>
					height: <?php echo $this->config->get('cover_height_entry') ? $this->config->get('cover_height_entry') : '200';?>px;"
				<?php }?>
				title="<?php echo $this->escape($post->getImageTitle());?>"
				caption="<?php echo $this->escape($post->getImageCaption());?>"
				target="_blank"
			></a>

			<?php if ($post->getImage() && EB::image()->isImage($post->getImage())) { ?>
				<img class="hide" src="<?php echo $post->getImage(EB::getCoverSize('cover_size_entry'), true, false, $this->config->get('cover_firstimage', 0));?>" alt="<?php echo $this->escape($post->getCoverImageAlt());?>" />


				<?php if ($post->getImageCaption()) { ?>
					<span class="eb-post-thumb-caption"><?php echo $post->getImageCaption(); ?></span>
				<?php } ?>

			<?php } else { ?>
				<?php echo EB::media()->renderVideoPlayer($post->getImage(), array('width' => '260','height' => '200','ratio' => '','muted' => false,'autoplay' => false,'loop' => false), false); ?>
			<?php } ?>

		<?php } ?>
	</div>
<?php } ?>

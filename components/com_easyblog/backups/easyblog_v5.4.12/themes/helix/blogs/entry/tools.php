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
<div class="article-print-email mt-3">
	
	<?php if ($this->entryParams->get('post_font_resize', true)) { ?>
	<span class="">
	<div class="eb-help-resize">
		<span><?php echo JText::_( 'COM_EASYBLOG_FONT_SIZE' ); ?>:</span>
		<a href="javascript:void(0);" data-font-resize data-operation="increase" data-eb-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYBLOG_FONT_LARGER', true);?>">
			&plus;
		</a>
		<a href="javascript:void(0);" data-font-resize data-operation="decrease" data-eb-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYBLOG_FONT_SMALLER', true); ?>">
			&ndash;
		</a>
	</div>
	</span>
	<?php } ?>

	<?php if ($preview) { ?>
	<span class="">
		<div class="eb-help-subscribe">
			<i class="fa fa-envelope"></i>
		</div>
	</span>
	<?php } ?>

	<?php if ($this->entryParams->get('post_subscribe_link', true) && $this->acl->get('allow_subscription')) { ?>
			<?php if (!$preview && $this->config->get('main_subscription') && $post->subscription) { ?>
				<span class="eb-help-subscribe">
					
					<a href="javascript:void(0);" class="btn btn-outline-secondary btn-sm link-subscribe <?php echo $subscription->id ? 'hide' : ''; ?>" data-blog-subscribe data-type="entry" data-id="<?php echo $post->id;?>"><i class="fa fa-envelope"></i> <?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_BLOG'); ?></a>

					<a href="javascript:void(0);" class="btn btn-outline-secondary btn-sm link-subscribe <?php echo $subscription->id ? '' : 'hide'; ?>" data-return="<?php echo $return; ?>" data-blog-unsubscribe data-subscription-id="<?php echo $subscription->id;?>"><i class="fa fa-envelope"></i> <?php echo JText::_('COM_EASYBLOG_UNSUBSCRIBE_ENTRY'); ?></a>
				</span>
			<?php } ?>
		<?php } ?>
	
	<?php if ($this->config->get('main_reporting') && (!$this->my->guest || $this->my->guest && $this->config->get('main_reporting_guests')) && $this->entryParams->get('post_reporting', true)) { ?>
	<a class="btn btn-outline-secondary btn-sm" href="javascript:void(0);" data-blog-report>
		<span class="fa fa-flag" aria-hidden="true"></span>
		<span><?php echo JText::_( 'COM_EASYBLOG_REPORT_THIS_POST');?></span>
	</a>
	<?php } ?>

	<?php if ($this->entryParams->get('post_print', true)) { ?>
	<a class="btn btn-outline-secondary btn-sm" rel="nofollow" title="<?php echo JText::_('COM_EASYBLOG_ENTRY_BLOG_OPTION_PRINT', true); ?>" href="<?php echo $post->getPrintLink();?>" data-post-print>
		<span class="fa fa-print" aria-hidden="true"></span>
		<span>
			<?php echo JText::_('COM_EASYBLOG_ENTRY_BLOG_OPTION_PRINT'); ?>
		</span>
	</a>
	<?php } ?>
	
</div>




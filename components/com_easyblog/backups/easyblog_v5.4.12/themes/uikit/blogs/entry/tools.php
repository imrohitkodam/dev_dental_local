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
<ul class="uk-subnav uk-subnav-divider">
	
	<?php if ($this->entryParams->get('post_font_resize', true)) { ?>
	<li>
	<div class="eb-help-resize">
		<span><?php echo JText::_( 'COM_EASYBLOG_FONT_SIZE' ); ?>:</span>
		<a href="javascript:void(0);" data-font-resize data-operation="increase" data-eb-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYBLOG_FONT_LARGER', true);?>">
			&plus;
		</a>
		<a href="javascript:void(0);" data-font-resize data-operation="decrease" data-eb-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYBLOG_FONT_SMALLER', true); ?>">
			&ndash;
		</a>
	</div>
	</li>
	<?php } ?>

	<?php if ($preview) { ?>
	<li>
		<div class="eb-help-subscribe">
			<i class="fa fa-envelope"></i>
		</div>
	</li>
	<?php } ?>

	<?php if ($this->entryParams->get('post_subscribe_link', true) && $this->acl->get('allow_subscription')) { ?>
		<?php if (!$preview && $this->config->get('main_subscription') && $post->subscription) { ?>
			<li>
				<span uk-icon="icon: mail" class="uk-margin-small-right"></span> 
				<a href="javascript:void(0);" class=" <?php echo $subscription->id ? 'hide' : ''; ?>" data-blog-subscribe data-type="entry" data-id="<?php echo $post->id;?>"><?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_BLOG'); ?></a>

				<a href="javascript:void(0);" class=" <?php echo $subscription->id ? '' : 'hide'; ?>" data-return="<?php echo $return; ?>" data-blog-unsubscribe data-subscription-id="<?php echo $subscription->id;?>"><?php echo JText::_('COM_EASYBLOG_UNSUBSCRIBE_ENTRY'); ?></a>
			</li>
		<?php } ?>
	<?php } ?>
	
	<?php if ($this->config->get('main_reporting') && (!$this->my->guest || $this->my->guest && $this->config->get('main_reporting_guests')) && $this->entryParams->get('post_reporting', true)) { ?>
	<li>
		<span uk-icon="icon: warning" class="uk-margin-small-right"></span> 
		<a href="javascript:void(0);" data-blog-report><?php echo JText::_( 'COM_EASYBLOG_REPORT_THIS_POST');?></a>
	</li>
	<?php } ?>

	<?php if ($this->entryParams->get('post_print', true)) { ?>
	<li>
		<span uk-icon="icon: print" class="uk-margin-small-right"></span> 
		<a rel="nofollow" title="<?php echo JText::_('COM_EASYBLOG_ENTRY_BLOG_OPTION_PRINT', true); ?>" href="<?php echo $post->getPrintLink();?>" data-post-print>
			<?php echo JText::_('COM_EASYBLOG_ENTRY_BLOG_OPTION_PRINT'); ?>
		</a>
	</li>
	<?php } ?>
	
</ul>




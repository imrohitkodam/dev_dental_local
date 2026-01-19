<?php
/**
* @package      StackIdeas
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* StackIdeas Toolbar is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="fd-toolbar__o-nav-item <?php echo $subscription->id ? 't-hidden' : '';?>"
	data-fd-tooltip="toolbar"
	data-fd-tooltip-title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SUBSCRIBE_TO_SITE'); ?>"
	data-fd-tooltip-placement="top" 
	role="button"
	data-blog-subscribe
	data-type="site"
	>
	<a href="javascript:void(0);" class="fd-toolbar__link">
		<i aria-hidden="true" class="fdi fa fa-envelope"></i>
		<span class="sr-only"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_SUBSCRIBE_TO_SITE'); ?></span>
	</a>
</div>

<div class="fd-toolbar__o-nav-item is-active <?php echo $subscription->id ? '' : 't-hidden';?>"
	data-fd-tooltip="toolbar"
	data-fd-tooltip-title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_UNSUBSCRIBE_TO_SITE');?>"
	data-fd-tooltip-placement="top"
	
	data-blog-unsubscribe
	data-subscription-id="<?php echo $subscription->id;?>"
	>
	<a href="javascript:void(0);" class="fd-toolbar__link">
		<i aria-hidden="true" class="fdi fa fa-envelope"></i>
		<span class="sr-only"><?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_UNSUBSCRIBE_TO_SITE'); ?></span>
	</a>
</div>
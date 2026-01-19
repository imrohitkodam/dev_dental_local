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
<div class="fd-toolbar__o-nav-item" 
	data-fd-tooltip="toolbar"
	data-fd-tooltip-title="<?php echo JText::_('MOD_SI_TOOLBAR_SEARCH'); ?>"
	data-fd-tooltip-placement="top" 
	role="button"
	>
	<a href="javascript:void(0);" 
		data-fd-toolbar-search-toggle
		data-fd-component="<?php echo $component;?>" 
		data-fd-moduleId="<?php echo FDT::getModuleId();?>" 
		data-fd-mobile="<?php echo $isMobile; ?>"
		class="fd-toolbar__link"
		>
		<i aria-hidden="true" class="fdi fa fa-search"></i>
		<span class="sr-only"><?php echo JText::_('MOD_SI_TOOLBAR_SEARCH'); ?></span>
	</a>
</div>
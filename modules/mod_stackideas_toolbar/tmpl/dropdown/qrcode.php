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
<div class="o-dropdown__bd px-md py-sm">
	<div class="flex items-center">
		<div class="flex-grow">
			<div class=" space-y-xs">
				<div class="font-bold text-sm text-gray-800">
					<?php echo JText::_('MOD_SI_TOOLBAR_MOBILE_APP');?>
				</div>
				<div class="text-xs text-gray-500">
					<?php echo JText::_('MOD_SI_TOOLBAR_MOBILE_APP_INFO');?>
				</div>
			</div>
			
		</div>
		<div class="pl-md flex-shrink-0">
			<img src="<?php echo $url; ?>" width="90" />
		</div>
	</div>
</div>

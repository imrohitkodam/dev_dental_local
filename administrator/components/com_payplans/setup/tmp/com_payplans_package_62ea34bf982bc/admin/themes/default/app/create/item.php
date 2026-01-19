<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="pp-appstore__item">
	<div class="pp-appstore-item bg-white rounded-md p-md border border-gray-200">
		<div class="flex">
			<div class="flex-shrink-0 pr-md">
				<div class="pp-appstore-item__action" >
					<a href="<?php echo JRoute::_('index.php?option=com_payplans&task=app.createInstance&element=' . $app->element . '&view=' . $view . '&layout=' . $layout);?>" class="rounded-full bg-primary-100 text-primary-400 hover:bg-primary-500 hover:text-white flex items-center justify-center hover:no-underline" style="width:40px;height: 40px;">
						<i class="fdi fa fa-plus"></i>
					</a>
				</div>
			</div>
			<div class="flex-grow">
				<div class="pp-appstore-item__hd">
					<h3 class="pp-reset pp-appstore-item__title my-no text-md"><?php echo JText::_($app->name);?></h3>
				</div>
				<div class="pp-appstore-item__bd">
					<div class="pp-appstore-item__desc text-gray-500">
						<?php echo $app->description;?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
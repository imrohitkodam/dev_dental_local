<?php
/**
* @package		Payplans
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
<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_USER_IMPORT_PROGRESS'); ?>

			<div class="panel-body">
				<div class="space-y-md">

					<div class="backend-progress-wrap space-y-sm">
						<div class="flex flex-wrap">
							<div class="flex-grow">
								<div data-user-progress-bar-status class="text-gray-800 text-xs">
									<?php echo JText::_('COM_PP_USER_IMPORT_START_IMPORTING'); ?>
									<span data-user-progress-loading class="eb-loader-o size-sm hide"></span>
								</div>
							</div>
							<div class="">
								<div data-user-progress-percentage class="text-gray-500 text-xs">0%</div>
							</div>
						</div>
						<div class="o-progress">
							<div data-user-progress-bar style="width:0%" class="o-progress__bar"></div>
						</div>
					</div>

					<div class="space-y-2xs">
						<div data-user-progress-status class="text-gray-500 text-xs"></div>
					</div>

					<div class="hide" data-user-progress-back>
						<a href="<?php echo rtrim(JURI::root(), '/'); ?>/administrator/index.php?option=com_payplans&view=user&layout=import">
							<button class="o-btn o-btn--default-o">&laquo; <?php echo JText::_('COM_PP_USER_IMPORT_BACK_BUTTON');?></button>
						</a>
					</div>

				</div>		
			</div>
		</div>
	</div>
	
	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_USER_IMPORT_STATISTIC'); ?>

			<div class="panel-body">
				<div class="space-y-2xs">
					<div data-user-progress-stat class="text-gray-700 text-sm"></div>
				</div>	
			</div>
		</div>
	</div>
</div>
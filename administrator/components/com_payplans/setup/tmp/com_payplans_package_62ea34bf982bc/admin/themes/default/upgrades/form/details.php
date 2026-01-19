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
<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
	<div class="col-span-1 md:col-span-6 w-auto">
		<?php echo $this->output('admin/app/generic/form', array('app' => $app)); ?>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_APP_PARAMETERS'); ?>

			<div class="panel-body">

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_APP_UPGRADE_UPGRADE_TO', 'app_params[upgrade_to]'); ?>
					<div class="flex-grow">
						<?php echo $this->html('form.plans', 'app_params[upgrade_to]', $appParams->get('upgrade_to'), true, true, '', [], ['theme' => 'fd']); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_APP_UPGRADE_IS_TRIAL_ALLOWED', 'app_params[willTrialApply]'); ?>
					<div class="flex-grow">
						<?php echo $this->fd->html('form.toggler', 'app_params[willTrialApply]', $appParams->get('willTrialApply', true)); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_APP_UPGRADE_PRORATE', 'app_params[upgradeProRate]'); ?>

					<div class="o-control-input">
						<?php echo $this->fd->html('form.toggler', 'app_params[upgradeProRate]', $appParams->get('upgradeProRate', true)); ?>
					</div>
				</div>				
			</div>
		</div>
	</div>
</div>

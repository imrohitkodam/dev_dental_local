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
			<?php echo $this->html('panel.heading', 'COM_PP_APP_PARAMETERS'); ?>

			<div class="panel-body">
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_APP_LIMITSUBSCRIPTION_LIMIT', 'app_params[limit]'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'app_params[limit]', $appParams->get('limit', '')); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_APP_LIMITSUBSCRIPTION_SUBSCRIPTION_STATUS', 'app_params[consider_status][]'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.status', 'app_params[consider_status][]', $appParams->get('consider_status'), 'subscription', '', true, '', array(PP_NONE)); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
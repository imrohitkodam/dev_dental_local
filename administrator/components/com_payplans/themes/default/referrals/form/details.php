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
		<?php echo $this->output('admin/app/generic/form', ['app' => $app]); ?>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_PP_REFERRALS_BEHAVIOUR'); ?>

			<div class="panel-body">

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PP_REFERRAL_LIMIT', '', 5); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'app_params[referral_limit]', $params->get('referral_limit', 5), '', ['postfix' => 'Times']); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PP_REFERRAL_WHEN_TO_SEND_EMAIL', '', 5); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.lists', 'app_params[after_invoice_paid]', $params->get('after_invoice_paid', true), '', '', [
							['title' => 'COM_PP_REFERRAL_AFTER_INVOICE_PAID', 'value' => 1],
							['title' => 'COM_PP_REFERRAL_AFTER_CODE_USED', 'value' => 0]
						]); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PP_REFERRAL_AMOUNT_TYPE', '', 5); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.lists', 'app_params[referral_amount_type]', $params->get('referral_amount_type', ''), '', '', [
							['title' => 'COM_PP_FIXED', 'value' => 'fixed'],
							['title' => 'COM_PP_PERCENTAGE', 'value' => 'percentage']
						]); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PP_REFERRAL_SHARER_DISCOUNT', '', 5); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'app_params[referrar_amount]', $params->get('referrar_amount', '5.00')); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PP_REFERRAL_PURCHASER_DISCOUNT', '', 5); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'app_params[referral_amount]', $params->get('referral_amount', '5.00')); ?>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>
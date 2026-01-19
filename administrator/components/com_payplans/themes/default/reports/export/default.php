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
<form name="adminForm" id="adminForm" class="o-form-horizontal" method="post" enctype="multipart/form-data">
	<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
		<div class="col-span-1 md:col-span-6 w-auto">
			<div class="panel">
				<?php echo $this->fd->html('panel.heading', 'COM_PP_HEADING_EXPORT_REPORTS_CSV'); ?>

				<div class="panel-body">
					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_PP_EXPORT_REPORTS_TYPE', 'type'); ?>

						<div class="flex-grow">
							<?php echo $this->html('form.lists', 'type', '', 'type', 'data-export-report-type', $exportTypes); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md t-hidden" data-subscription-status-wrapper>
						<?php echo $this->fd->html('form.label', 'COM_PP_EXPORT_REPORTS_SUBSCRIPTION_STATUS', 'subsStatus[]'); ?>

						<div class="flex-grow">
							<?php echo $this->html('form.status', 'subsStatus[]', '', 'subscription', '', true, 'data-subscription-status'); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md" data-invoice-status-wrapper>
						<?php echo $this->fd->html('form.label', 'COM_PP_EXPORT_REPORTS_INVOICE_STATUS', 'invStatus[]'); ?>

						<div class="flex-grow">
							<?php echo $this->html('form.status', 'invStatus[]', '', 'invoice', '', true, 'data-invoice-status', [PP_INVOICE_WALLET_RECHARGE]); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_PP_EXPORT_REPORTS_PLANS', 'plans'); ?>

						<div class="flex-grow">
							<?php echo $this->html('form.plans', 'plans', '', true, true, ['data-export-plans' => ''], [], ['theme' => 'fd']); ?>
							
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md" data-payment-gateway-wrapper>
						<?php echo $this->fd->html('form.label', 'COM_PP_EXPORT_REPORTS_PAYMENT_GATEWAY','gateway[]'); ?>

						<div class="flex-grow">
							<select class="o-form-control" name="gateway[]"  multiple="multiple" style="<?php count($gateways) > 4 ? 'min-height: 100px;' : ''; ?>">
								<?php foreach ($gateways as $gateway) { ?>
									<option value="<?php echo $gateway->app_id;?>"> 
										<?php echo $gateway->title;?>
									</option>
								<?php } ?>
							</select>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_PP_EXPORT_PDF_TRANSACTION_DATE_RANGE', 'daterange'); ?>
						
						<div class="flex-grow">
							<?php echo $this->fd->html('form.dateRange', '', 'dateRange', '', ['class' => 'border border-solid border-gray-300 py-xs rounded-md']); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_PP_EXPORT_REPORTS_LIMIT', 'limit'); ?>

						<div class="flex-grow">
							<?php echo $this->fd->html('form.text', 'limit', '50', '', ['postfix' => 'Items']); ?>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

	<?php echo $this->html('form.action', 'reports', 'export'); ?>
</form>

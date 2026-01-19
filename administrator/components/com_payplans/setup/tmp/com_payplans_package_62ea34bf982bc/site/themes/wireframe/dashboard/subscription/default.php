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
<div class="o-card bg-white text-gray-600">
	<div class="o-card__body space-y-sm">
		<div class="o-alert o-alert--danger t-hidden">
			<?php echo JText::_('COM_PP_SUBSCRIPTION_ALREADY_EXPIRED'); ?>
		</div>
		<div class="flex">
			<div class="flex-grow min-w-0 space-y-xs">
				<div class="o-card__title">
					<?php echo JText::_($subscription->getTitle());?>
				</div>
				<div class="o-card__desc">
					#<?php echo $subscription->getKey();?>
				</div>

				<?php if ($subscription->isRecurring() && $subscription->order->isCancelled()) { ?>
					<div class="o-card__desc">
						<?php echo JText::_('COM_PP_SUBSCRIPTION_CANCELLED_AND_WILL_NOT_BE_REBILLED');?>
					</div>
				<?php } ?>
			</div>
			<?php if (!$subscription->isNotActive()) { ?>
				<div class="flex-shrink-0">
					<?php echo $this->html('subscription.status', $subscription); ?>
				</div>
			<?php } ?>
		</div>
	</div>

	<?php $showInvoice = true; ?>
	<?php $plan = $subscription->getPlan();?>

	<?php if ($this->config->get('layout_free_invoices') && !$plan->isRecurring()) {
			if ($plan->isFree()) { 
					$showInvoice = false;
			} else {
				$order = $subscription->getOrder();
				$invoice = $order->getLastMasterInvoice();

				if ($invoice instanceof PPInvoice) {
					$payment = $invoice->getPayment();
					 if (!$payment instanceof PPPayment) {
						$showInvoice = false;
					 }
				}
			}
		} 
	?>
	<div class="o-card-list-group <?php echo $showInvoice ? '' : 't-hidden';?>">
		<div class="o-card-list-group__item border-b-0 py-no">
			<div class="o-card--meta">
				<div class="flex">
					<div class="flex-grow">
						<?php echo JText::_('COM_PP_INVOICES');?>
					</div>
				</div>
			</div>
		</div>

		<div class="o-card-list-group__item ">
			<div class="o-table-wrapper overflow-auto">

				<table class="o-table ">
					<thead>
						<tr>
							<th class="p-sm bg-white">
								<?php echo JText::_('#');?>
							</th>
							<th class="p-sm bg-white">
								<?php echo JText::_('COM_PP_KEY');?>
							</th>
							<th class="p-sm bg-white">
								<?php echo JText::_('COM_PP_TOTAL'); ?>
							</th>
							<th class="p-sm bg-white">
								<?php echo JText::_('COM_PP_STATUS');?>
							</th>
							<?php if (!$this->isMobile()) { ?>
							<th width="10%" class="p-sm bg-white">
								&nbsp;
							</th>
							<?php } ?>
							<th class="p-sm bg-white">
							</th>
						</tr>
					</thead>
					<tbody>
						<?php $i = 1; ?>
						<?php foreach ($invoices as $invoice) { ?>
							<?php
								$showDownloadInvoice = true;

								if (in_array($invoice->getStatus(), [PP_INVOICE_CONFIRMED, PP_NONE]) && !$this->config->get('layout_download_unpaid_invoices')) {
									$showDownloadInvoice = false;
								}
							?>
							<tr>
								<td class="p-sm">
									<?php echo $i;?>
								</td>
								<td class="p-sm">
									<a class="no-underline" href="<?php echo $invoice->getPermalink();?>" target="_blank">
										<?php echo $invoice->getKey();?>
									</a>

									<?php if ($this->isMobile() && $showDownloadInvoice) { ?>
									<div class="mt-lg flex gap-sm">
										<?php if ($this->config->get('enable_pdf_invoice')) { ?>
											<a href="<?php echo $invoice->getDownloadLink();?>" target="_blank">
												<i class="fdi fas fa-download"></i>
											</a>
											&nbsp;
										<?php } ?>

										<a href="<?php echo $invoice->getPrintLink();?>" target="_blank">
											<i class="fdi fa fa-print"></i>
										</a>
									</div>
									<?php } ?>
								</td>
								<td class="p-sm">
									<?php echo $this->html('html.amount', $invoice->getTotal(), $invoice->getCurrency());?>
								</td>
								<td class="p-sm">
									<?php echo $this->fd->html('label.standard', $invoice->getStatusName(), $invoice->getStatusLabelClass()); ?>
								</td>

								<?php if (in_array($invoice->getStatus(), array(PP_INVOICE_CONFIRMED, PP_NONE))) { ?>
								<td class="p-sm">
									<?php echo $this->fd->html('button.link',
										PPR::_('index.php?option=com_payplans&view=checkout&invoice_key=' . $invoice->getKey() . PP::getExcludeTplQuery('checkout')), 'COM_PP_COMPLETE_PAYMENT_NOW',
										'primary',
										'xs',
										[
										'attributes' => 'target="_blank"',
										'class' => 'whitespace-nowrap'
									]); ?>
								</td>
								<?php } ?>
								<?php if (!$this->isMobile() && $showDownloadInvoice) { ?>
									<td class="text-center">
										<div class="flex gap-xs justify-center">
											<?php if ($this->config->get('enable_pdf_invoice')) { ?>
												<a href="<?php echo $invoice->getDownloadLink();?>" target="_blank">
													<i class="fdi fas fa-download"></i>
												</a>&nbsp;
											<?php } ?>

											<a href="<?php echo $invoice->getPrintLink();?>" target="_blank">
												<i class="fdi fas fa-print"></i>
											</a>
										</div>
									</td>
								<?php } ?>
							</tr>
							<?php $i++;?>
						<?php } ?>
						
						
					</tbody>
				</table>
				
			</div>
		</div>
	</div>

	<?php if ($customDetails) { ?>
	<div class="o-card-list-group">
		<div class="o-card-list-group__item border-b-0 py-no">
			<div class="o-card--meta">
				<div class="flex">
					<div class="flex-grow">
						<?php echo JText::_('COM_PP_SUBSCRIPTION_DETAILS');?>
					</div>
				</div>
			</div>
		</div>

		<div class="o-card-list-group__item">
			<div class="">
				<table class="o-table">
					<tbody>
					<?php foreach ($customDetails as $details) { ?>
						<?php foreach ($details->getFieldsOutput($subscriptionParams) as $field) { ?>
							<tr class="p-sm bg-white">
								<td class="p-sm">
									<?php echo $field->label;?>
								</td>
								<td width="50%">
									<?php if ($field->type === 'file') { ?>
										<?php echo $this->html('customdetails.file', $field, '', $subscription, ['allowInput' => false]);?>
									<?php } ?>

									<?php if (!in_array($field->type, ['file', 'country', 'toggler'])) { ?>
										<?php echo $field->value;?>
									<?php } ?>

									<?php if ($field->type === 'country' && $field->value) { ?>
										<?php echo PP::getCountryNameById($field->value);?>
									<?php } ?>

									<?php if ($field->type === 'toggler') { ?>
										<?php 
											$togglerIcon = $field->value ? 'fa fa-check' : 'fa fa-times';
											$togglerClass = $field->value ? 'text-success-500' : 'text-danger-500';
										?>

										<?php echo $this->fd->html('icon.font', $togglerIcon, false, '', ['class' => $togglerClass]); ?>
									<?php } ?>
								</td>
							</tr>
							<?php $i++;?>
						<?php } ?>
					<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<?php } ?>
</div>

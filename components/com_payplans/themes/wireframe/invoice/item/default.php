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
<style type="text/css">
@media print {
	.pp-invoice-actions {
		display: none;
	}

	a[href]:after {
		content: none !important;
	}
}
</style>
<div class="pp-invoice-container t-lg-mt--xl">
	<?php $showDownloadInvoice = true; ?>
	<?php if (in_array($invoice->getStatus(), [PP_INVOICE_CONFIRMED, PP_NONE]) && !$this->config->get('layout_download_unpaid_invoices')) { ?>
		<?php $showDownloadInvoice = false; ?>
	<?php } ?>

	<?php if ($showDownloadInvoice) { ?>
		<div class="pp-invoice-container__hd t-lg-mb--xl pp-invoice-actions">
			<div class="o-card shadow-md">
				<div class="o-card__body">
					<div class="flex gap-md">
						<?php if ($this->config->get('enable_pdf_invoice')) { ?>
							<div class="flex-grow">
								<?php echo $this->fd->html('button.link', PPR::_('index.php?option=com_payplans&view=invoice&layout=download&invoice_key=' . $invoice->getKey()), 'COM_PP_DOWNLOAD_INVOICE', 'default', 'sm', [
									'attributes' => 'data-invoice-download',
									'icon' => 'fdi fa fa-download',
									'class' => ''
								]); ?>
							</div>
						<?php } ?>

						<div class="">
							<?php echo $this->fd->html('button.link', 'javascript:void(0);', 'COM_PP_PRINT_INVOICE', 'primary', 'sm', [
								'attributes' => 'data-invoice-print',
								'icon' => 'fdi fa fa-print',
								'class' => ''
							]); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>

	<div class="pp-invoice-container__bd">
		<div class="o-card shadow-md">
			<div class="o-card__body">
				<div class="pp-invoice-menu">
					<?php if (!$customInvoiceContent) { ?>
						<div class="pp-invoice-menu__hd">
							<div class="pp-invoice-menu__hd-id">
								<?php echo JText::_('COM_PP_INVOICE_LABEL');?> #<?php echo $invoice->getKey();?>
							</div>

							<div class="pp-invoice-menu__hd-date">
								<i class="fdi far fa-calendar"></i>&nbsp; <?php echo PP::date($invoice->getCreatedDate(), true)->toDisplay(PP::getDateFormat());?>
							</div>

							<div class="pp-invoice-menu__hd-state ml-sm">
								<?php echo $this->fd->html('label.standard', $invoice->getStatusName(), $invoice->getStatusLabelClass()); ?>
							</div>
						</div>

						<table class="pp-invoice-table">
							<thead>
								<tr>
									<th class="align-top">
										<?php echo JText::_('COM_PP_INVOICE_FROM');?>
									</th>
									<td class="align-top">
										<?php if ($this->config->get('companyName')) { ?>
											<?php echo $this->config->get('companyName'); ?><br />
										<?php } ?>

										<?php if ($this->config->get('companyAddress')) { ?>
											<?php echo nl2br($this->config->get('companyAddress'));?><br />
										<?php } ?>

										<?php if ($this->config->get('companyPostCode')) { ?>
											<?php echo $this->config->get('companyPostCode');?><br />
										<?php } ?>

										<?php if ($this->config->get('companyCityCountry')) { ?>
											<?php echo $this->config->get('companyCityCountry');?><br />
										<?php } ?>

										<?php if ($this->config->get('companyPhone')) { ?>
											<?php echo JText::_('COM_PP_TELEPHONE');?>: <?php echo $this->config->get('companyPhone');?>
										<?php } ?><br />

										<?php if ($this->config->get('companyTaxId')) { ?>
											<?php echo JText::_('COM_PP_COMPANY_TAX_ID');?>: <?php echo $this->config->get('companyTaxId');?>
										<?php } ?>
									</td>

									<td class="align-top text-right">
										<?php if ($this->config->get('invoice_showlogo')) { ?>
										<div class="pp-invoice-logo">
											<img src="<?php echo PP::getCompanyLogo();?>" title="<?php echo $this->html('string.escape', $this->config->get('companyName'));?>" />
										</div>
										<?php } ?>
									</td>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th class="align-top">
										<?php echo JText::_('COM_PAYPLANS_INVOICE_BILL_TO');?>
									</th>
									<td class="align-top">
										<?php echo $user->getDisplayName(); ?><br />
										<?php echo $user->getEmail(); ?><br />

										<div>
											<?php echo PP::rewriteContent($this->config->get('add_token'), $invoice, true); ?>
										</div>
									</td>

									<td class="text-right t-va--bottom">
										&nbsp;
									</td>
								</tr>
								<tr>
									<th class="align-top">
										<?php echo JText::_('COM_PP_DETAILS');?>
									</th>
									<td class="align-top">
										<b><?php echo JText::_($invoice->getTitle()); ?></b><br />

										<?php echo JText::sprintf('COM_PP_INVOICE_KEY', $invoice->getKey()); ?><br />

										<?php if ($invoice->isPaid() && $payment) { ?>
										<span><?php echo JText::_('COM_PAYPLANS_INVOICE_PAYMENT_METHOD'); ?>:</span>
										<b><?php echo $payment->getId() ? $payment->getAppName() : JText::_('COM_PAYPLANS_TRANSACTION_PAYMENT_GATEWAY_NONE');?></b>
										<?php } ?>
									</td>

									<td class="text-right">

									</td>
								</tr>

							</tbody>
						</table>

						<table class="pp-invoice-table">

							<tbody>
								<tr>
									<td class="align-top">
										<?php echo JText::_('COM_PAYPLANS_INVOICE_PRICE'); ?>
									</td>

									<td class="text-right">
										<?php echo $this->html('html.amount', $invoice->getSubtotal(), $invoice->getCurrency()); ?>
									</td>
								</tr>

								<?php foreach ($modifiers as $modifier) { ?>
									<?php if (in_array($modifier->getSerial(), $discountablesSerials) ||
												in_array($modifier->getSerial(), $taxableSerials) ||
												in_array($modifier->getSerial(), $nonTaxesSerials)) { ?>
										<tr>
											<td>
												<?php $message = JText::_($modifier->getMessage()); ?>
												 <div><?php echo $message; ?></div>
											</td>
											<td class="text-right">
												<?php echo ($modifier->isNegative()) ? '(-)&nbsp;' : '(+)&nbsp;'; ?>
												<?php $modifierAmount = str_replace('-', '', $modifier->_modificationOf); ?>
												<?php echo $this->html('html.amount', $modifierAmount, $invoice->getCurrency()); ?>
											</td>
										</tr>
									<?php } ?>
								<?php } ?>

								<?php if ($invoice->isPaid()) { ?>
								<tr>
									<td class="align-top">
										<?php echo JText::_('COM_PP_AMOUNT_PAID'); ?>
									</td>

									<td class="text-right">
										<?php echo $this->html('html.amount', $invoice->getTotal(), $invoice->getCurrency()); ?>
									</td>
								</tr>
								<?php } ?>

								<tr>
									<th class="align-top">
										<?php echo JText::_('COM_PAYPLANS_INVOICE_TOTAL'); ?>
									</th>

									<th class="text-right">
										<?php echo $this->html('html.amount', $invoice->getTotal(), $invoice->getCurrency()); ?>
									</th>
								</tr>

							</tbody>
						</table>
					<?php } else { ?>
						<?php echo $customInvoiceContent; ?>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>

	<?php if (PPJString::trim($this->config->get('note', ''))) { ?>
	<div class="pp-invoice-container__bd mt-md">
		<div class="o-card shadow-md">
			<div class="o-card__body">
				<h5><?php echo JText::_('COM_PP_ADDITIONAL_NOTES');?></h5>
				<p class="mt-md">
					<?php echo $this->config->get('note', ''); ?>
				</p>
			</div>
		</div>
	</div>
	<?php } ?>
</div>
<?php
/**
* @package      PayPlans
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="pp-invoice-container">
	<div class="pp-invoice-container__bd">
		<div class="o-card o-card--shadow">
			<div class="o-card__body">
				<div class="pp-invoice-menu">
					<?php if (!$customInvoiceContent) { ?>
						<div class="pp-invoice-menu__hd">

							<table style="width:100%;">
								<tr>
									<td>
										<?php echo JText::_('COM_PP_INVOICE_LABEL');?> #<?php echo $invoice->getKey();?>
									</td>
									<td>
										&nbsp;
									</td>
									<td class="text-right">
										<table style="width:100%;">
											<tr>
												<td class="text-right">
													<?php echo PP::date($invoice->getCreatedDate(), true)->toDisplay(PP::getDateFormat());?>
												</td>
												<td class="text-right" style="width:1%">
													<?php echo $this->fd->html('label.standard', $invoice->getStatusName(), $invoice->getStatusLabelClass()); ?>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</div>

						<table class="pp-invoice-table">
							<thead>
								<tr>
									<th>
										<?php echo JText::_('COM_PP_INVOICE_FROM');?>
									</th>
									<td>
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
									
									<td class="text-right">
										<?php if ($this->config->get('invoice_showlogo')) { ?>
										<div class="pp-invoice-logo">
										<img src="<?php echo PP::getCompanyLogo();?>?v=<?php echo time(); ?>" title="<?php echo $this->html('string.escape', $this->config->get('companyName'));?>" />
										</div>
										<?php } ?>
									</td>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th>
										<?php echo JText::_('COM_PAYPLANS_INVOICE_BILL_TO');?>
									</th>
									<td>
										<?php echo $user->getDisplayName(); ?><br />
										<?php echo $user->getEmail(); ?><br />

										<div>
											<?php echo PP::rewriteContent($this->config->get('add_token'), $invoice, true); ?>
										</div>
									</td>
									<td class="text-right">
										&nbsp;
									</td>
								</tr>
								<tr>
									<th>
										<?php echo JText::_('COM_PP_DETAILS');?>
									</th>
									<td>
										<b><?php echo JText::_($invoice->getTitle()); ?></b><br />
										<?php echo JText::sprintf('COM_PP_INVOICE_KEY', $invoice->getKey()); ?><br />

										<?php if ($invoice->isPaid() && $payment) { ?>
											<span><?php echo JText::_('COM_PAYPLANS_INVOICE_PAYMENT_METHOD'); ?>:</span>
											<b><?php echo $payment->getId() ? $payment->getAppName() : JText::_('COM_PAYPLANS_TRANSACTION_PAYMENT_GATEWAY_NONE');?></b>
										<?php } ?>
									</td>
									
									<td class="text-right">
										&nbsp;
									</td>
								</tr>
							</tbody>
						</table>

						<table class="pp-invoice-table">
							<tbody>
								<tr>
									<td>
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
												<?php if (in_array($modifier->getType() , array('eu-vat', 'basictax'))) { ?>
													<?php $message = $message. " ( ".round($modifier->getAmount())."% )"; ?>
												<?php } ?>

												 <div><?php echo $message;?></div>
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
									<td>
										<?php echo JText::_('COM_PP_AMOUNT_PAID'); ?>
									</td>

									<td class="text-right">
										<?php echo $this->html('html.amount', $invoice->getTotal(), $invoice->getCurrency()); ?>
									</td>
								</tr>
								<?php } ?>

								<tr>
									<th>
										<?php echo JText::_('COM_PAYPLANS_ORDER_DISPLAY_TOTAL'); ?>
									</th>

									<th class="text-right">
										<?php echo $this->html('html.amount', $invoice->getTotal(), $invoice->getCurrency()); ?>
									</th>
								</tr>
							</tbody>
						</table>

						<?php if (PPJString::trim($this->config->get('note'))) { ?>
						<div>&nbsp;</div>
						<table style="width:100%;">
							<tbody>
								<tr>
									<td><h5><?php echo JText::_('COM_PP_ADDITIONAL_NOTES');?></h5></td>
								</tr>
								<tr>
									<td>
										<?php echo $this->config->get('note'); ?>
									</td>
								</tr>
							</tbody>
						</table>
						<?php } ?>
					<?php } else { ?>
						<?php echo $customInvoiceContent; ?>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
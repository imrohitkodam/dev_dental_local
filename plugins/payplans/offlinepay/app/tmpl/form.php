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

$paymentMethods = $params->get('payment_method', ['Cash', 'Cheque', 'Wiretransfer']);
$paymentTypes = [];
$isChequeOptionOnly = false;

foreach ($paymentMethods as $paymentMethod) {
	$obj = new stdClass();
	$obj->value = $paymentMethod;
	$obj->title = 'COM_PP_' . strtoupper($paymentMethod);

	$paymentTypes[] = $obj;
}

// Check for the cheque option
if ($paymentTypes) {
	$totalOfPaymentTypes = count($paymentTypes);

	if ($totalOfPaymentTypes === 1 && $paymentTypes[0]->value === "Cheque") {
		$isChequeOptionOnly = true;
	}
}

?>
<form action="<?php echo JRoute::_('index.php?tmpl=component');?>" method="post" data-offline-form>

<div class="pp-checkout-item">
	<div class="pp-checkout-item__title"><?php echo strtoupper(JText::_('COM_PP_BANK_DETAILS'));?></div>

	<div class="pp-checkout-item__content">
		<div class="text-sm text-gray-800 mb-sm">
			<?php echo JText::sprintf('COM_PP_PAYMENT_VIA_BANK', '<b>' . $this->html('html.amount', $amount, $invoice->getCurrency()) . '</b>'); ?>
		</div>
		<div class="o-card">
			<div class="o-card__body">
				<table>
					<tbody>
						<tr class="text-sm text-gray-800">
							<td class="p-sm">
								<?php echo JText::_('COM_PP_PAYMENT_BANK_NAME');?>:
							</td>
							<td class="p-sm">
								&nbsp;<b><?php echo JText::_($params->get('bankname', '')); ?></b>
							</td>
						</tr>

						<tr class="text-sm text-gray-800">
							<td class="p-sm">
								<?php echo JText::_('COM_PP_PAYMENT_BANK_ACCOUNT_NAME');?>:
							</td>							 
							<td class="p-sm">
								&nbsp;<b><?php echo JText::_($params->get('account_name', '')); ?></b>
							</td>
						</tr>

						<tr class="text-sm text-gray-800">
							<td class="p-sm">
								<?php echo JText::_('COM_PP_PAYMENT_BANK_ACCOUNT_NUMBER');?>:
							</td>							 
							<td class="p-sm">
								&nbsp;<b><?php echo JText::_($params->get('account_number', '')); ?></b>
							</td>
						</tr>

						<tr class="text-sm text-gray-800">
							<td class="p-sm">
								<?php echo JText::_('COM_PP_PAYMENT_BANK_INVOICE_REFERENCE_NUMBER');?>:
							</td>
							<td class="p-sm">
								&nbsp;<b><?php echo $invoice->getKey(); ?></b>
							</td>
						</tr>

						<?php if ($email) { ?>
						<tr class="text-sm text-gray-800">
							<td colspan="2" class="p-sm">
								<div class="o-label bg-primary-100 text-primary-500 mb-2xs">
									<?php echo JText::_('COM_PP_NOTE');?>
								</div>
								<div class="text-sm text-gray-800">
									<?php echo JText::sprintf('COM_PP_PAYMENT_BANK_EMAIL_RECEIPT', '<b>' . $email . '</b>');?>
								</div>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<div class="pp-checkout-container__bd">
	<div class="pp-checkout-item">
		<div class="pp-checkout-item__title"><?php echo strtoupper(JText::_('COM_PP_PAYMENT_DETAILS'));?></div>

		<div class="pp-checkout-item__content space-y-sm">
			<div class="o-form-group">
				<?php echo JText::_('COM_PP_PAYMENT_VIA_BANK_COMPLETED'); ?>
			</div>

			<?php echo $this->html('floatlabel.lists', 'COM_PP_PAYMENT_METHOD', 'gateway_params[from]', '', '', ['data-offline-transaction-type' => ''], $paymentTypes); ?>

			<div class="<?php echo $isChequeOptionOnly ? '' : 't-hidden'; ?>" data-offline-transaction-id>
				<?php echo $this->html('floatlabel.text', 'COM_PP_CHEQUE_OR_DEMAND_DRAFT_ID', 'gateway_params[id]', ''); ?>
			</div>
		</div>
	</div>
</div>

<div class="pp-checkout-container__ft">
	<div class="pp-checkout-wrapper">
		<div class="flex items-center">
			<?php echo $this->output('site/payment/default/cancel', ['payment' => $payment]); ?>

			<div class="flex-shrink-0">
				<?php echo $this->fd->html('button.submit', 'COM_PP_COMPLETE_PAYMENT_BUTTON', 'primary', 'default'); ?>
			</div>
		</div>
	</div>
</div>


<?php echo $this->html('form.hidden', 'view', 'payment'); ?>
<?php echo $this->html('form.hidden', 'layout', 'complete'); ?>
<?php echo $this->html('form.hidden', 'action', 'success', ['data-offline-action' => '']); ?>
<?php echo $this->html('form.hidden', 'gateway_params[amount]', $amount); ?>
<?php echo $this->html('form.hidden', 'payment_key', $payment->getKey()); ?>
</form>
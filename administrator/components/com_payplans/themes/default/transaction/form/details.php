<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
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
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_PP_TRANSACTION_DETAILS'); ?>

			<div class="panel-body">
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PP_ID', '', 5, false); ?>

					<div class="flex-grow">
						<?php echo $transaction->getId();?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PP_INVOICE', '', 5, false); ?>

					<div class="flex-grow">
						<a href="index.php?option=com_payplans&view=invoice&layout=form&id=<?php echo $invoice->getId(); ?>">
							<?php echo $invoice->getId();?> (<?php echo $invoice->getKey();?>)
						</a>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PAYPLANS_TRANSACTION_EDIT_PAYMENT_ID', '', 5, false); ?>

					<div class="flex-grow">
						<?php echo $payment->getId();?> (<?php echo $payment->getKey();?>)
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PAYPLANS_TRANSACTION_EDIT_AMOUNT', '', 5, false); ?>

					<div class="flex-grow">
						<?php echo $this->html('html.amount', $transaction->getAmount(), $transaction->getCurrency()); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PAYPLANS_TRANSACTION_EDIT_GATEWAY_TYPE', '', 5, false); ?>

					<div class="flex-grow">
						<?php if ($gateway) { ?>
							<?php echo $gateway->getTitle(); ?>
						<?php } else { ?>
							&mdash;
						<?php } ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PAYPLANS_TRANSACTION_EDIT_GATEWAY_TRANSACTION_ID', '', 5, false); ?>

					<div class="flex-grow">
						<?php echo !$transaction->getGatewayTxnId() ? JText::_('COM_PP_NOT_AVAILABLE') : $transaction->getGatewayTxnId(); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PAYPLANS_TRANSACTION_EDIT_GATEWAY_PARENT_TRANSACTION', '', 5, false); ?>

					<div class="flex-grow">
						<?php echo !$transaction->getGatewayParentTxn() ? JText::_('COM_PP_NOT_AVAILABLE') : $transaction->getGatewayParentTxn(); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PAYPLANS_TRANSACTION_EDIT_GATEWAY_SUBSCRIPTION_ID', '', 5, false); ?>

					<div class="flex-grow">
						<?php echo !$transaction->getGatewaySubscriptionId() ? JText::_('COM_PP_NOT_AVAILABLE') : $transaction->getGatewaySubscriptionId(); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PAYPLANS_TRANSACTION_EDIT_CREATED_DATE', '', 5, false); ?>

					<div class="flex-grow">
						<?php echo $transaction->getCreatedDate(true)->toDisplay(JText::_('DATE_FORMAT_LC2')); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->html('form.label', 'COM_PAYPLANS_TRANSACTION_EDIT_MESSAGE', '', 5, false); ?>

					<div class="flex-grow">
						<?php echo JText::_($transaction->getMessage()); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
		<?php echo $this->output('admin/transaction/form/user'); ?>
	</div>
</div>
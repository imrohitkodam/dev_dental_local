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
<?php if ($order->getId() && $subscription->canCancel() && $subscription->isRecurring() && $order->isCancelled()) { ?>
<div class="grid grid-cols-1 md:grid-cols-12 gap-md pb-md">
	<div class="col-span-1 md:col-span-12 w-auto">
		<?php echo $this->fd->html('alert.standard', 'COM_PP_SUBSCRIPTION_CANCELLED_INFO', 'warning', ['icon' => 'fdi fa fa-info']); ?>
	</div>
</div>
<?php } ?>

<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_SUBSCRIPTION_DETAILS'); ?>

			<div class="panel-body">
				<?php if ($subscription->getId()) { ?>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_ID', 'id', '', '', false); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('label.standard', $subscription->getId());?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_SUBSCRIPTION_KEY', 'key', '', '', false); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('label.standard', $subscription->getKey());?>
					</div>
				</div>
				<?php } ?>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md" <?php echo !$subscription->getId() ? ' data-target="pp-form-plan"' : '';?>>
					<?php echo $this->fd->html('form.label', 'COM_PAYPLANS_SUBSCRIPTION_EDIT_PLAN', 'plan_id', '', '', false); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.plans', 'plan_id', $subscription->getPlan()->getId(), !$subscription->getId() ? true : false, false, ['data-pp-form-plan' => ''], [], ['theme' => 'fd']); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md" <?php echo !$subscription->getId() ? ' data-target="pp-form-user-input"' : ''; ?>>
					<?php echo $this->fd->html('form.label', 'COM_PAYPLANS_SUBSCRIPTION_EDIT_USER', '', '', false); ?>

					<div class="flex-grow">
						<?php if (!$subscription->isNew()) { ?>
							<a href="index.php?option=com_payplans&view=user&layout=form&id=<?php echo $subscription->getBuyer()->getId();?>"><?php echo $subscription->getBuyer()->getUsername();?></a>
						<?php } else { ?>
							<?php echo $this->fd->html('form.user', 'user_id', '', null, ['attributes' => 'data-pp-form-user-input']); ?>
						<?php } ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PAYPLANS_SUBSCRIPTION_EDIT_STATUS', 'status'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.status', 'status', $subscription->getStatus(), 'subscription'); ?>
					</div>
				</div>

				<?php if ($subscription->getId()) { ?>
					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_PAYPLANS_SUBSCRIPTION_EDIT_ORDER_TOTAL', ''); ?>

						<div class="flex-grow">
							<?php echo $this->fd->html('label.standard', $this->html('html.amount', $order->getTotal(), $order->getCurrency()), 'success'); ?>
						</div>
					</div>

					<?php if ($params->get('units', false)) { ?>
						<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
							<?php echo $this->fd->html('form.label', 'COM_PAYPLANS_SUBSCRIPTION_EDIT_TOTAL_UNITS_PURCHASED', ''); ?>

							<div class="flex-grow">
								<?php echo $params->get('units'); ?>
							</div>
						</div>
					<?php } ?>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_PAYPLANS_SUBSCRIPTION_EDIT_SUBSCRIPTION_DATE', 'subscription_date'); ?>

						<div class="flex-grow">
							<?php if ($subscription->getSubscriptionDate()) { ?>
								<?php echo $this->fd->html('form.datetimepicker', 'subscription_date', $subscription->getSubscriptionDate(true)->toSql(true)); ?>
							<?php } else { ?>
								&mdash;
							<?php } ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_PAYPLANS_SUBSCRIPTION_EDIT_EXPIRATION_DATE', 'expiration_date'); ?>

						<div class="flex-row">
							<?php if ($subscription->getExpirationDate()) { ?>
								<?php echo $this->fd->html('form.datetimepicker', 'expiration_date', $subscription->getExpirationDate(true)->toSql(true)); ?>
							<?php } else { ?>
								<u><?php echo JText::_('Plan never expires'); ?></u>
							<?php } ?>
						</div>
					</div>
				<?php } ?>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PAYPLANS_SUBSCRIPTION_PARAM_NOTES_LABEL', 'params[notes]'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.textarea', 'params[notes]', $params->get('notes'), 'params[notes]', array('rows' => 5)); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
		<?php if ($upgradedFrom || $upgradedTo) { ?>
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PAYPLANS_SUBSCRIPTION_EDIT_PARAMETERS'); ?>

			<div class="panel-body">

				<?php if ($upgradedFrom) { ?>
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PAYPLANS_SUBSCRIPTION_EDIT_PARAM_UPGRADED_FROM', 'expiration_date'); ?>

					<div class="flex-row">
						<?php if (!$upgradedFromSubscription) { ?>
							<?php echo PP::encryptor()->encrypt($upgradedFrom); ?>
						<?php } else { ?>
							<a href="index.php?option=com_payplans&view=subscription&layout=form&id=<?php echo $upgradedFrom;?>"><?php echo PP::encryptor()->encrypt($upgradedFrom); ?></a>
						<?php } ?>
					</div>
				</div>

				<?php } ?>

				<?php if ($upgradedTo) { ?>
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PAYPLANS_SUBSCRIPTION_EDIT_PARAM_UPGRADED_TO', 'expiration_date'); ?>

					<div class="flex-row">
						<a href="index.php?option=com_payplans&view=subscription&layout=form&id=<?php echo $upgradedTo;?>"><?php echo PP::encryptor()->encrypt($upgradedTo); ?></a>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
		<?php } ?>
	</div>
</div>




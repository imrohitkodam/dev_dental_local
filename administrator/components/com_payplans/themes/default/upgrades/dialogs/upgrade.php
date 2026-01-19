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
<dialog>
	<width>900</width>
	<height>600</height>
	<selectors type="json">
	{
		"{closeButton}" : "[data-close-button]",
		"{upgradeButton}": "[data-upgrade-button]",

		"{form}": "[data-upgrade-form]",
		"{planSelection}": "[data-upgrade-new-plan]",
		"{regularPrice}": "[data-upgrade-regular-price]",
		"{unutilizedAmount}": "[data-upgrade-unutilized]",
		"{unutilizedTax}": "[data-upgrade-unutilized-tax]",
		"{payableAmount}": "[data-upgrade-payable-amount]",
		"{paymentDetails}": "[data-upgrade-payment-details]",
		"{paymentOptions}": "[data-upgrade-payment-options]",
		"{alertWrapper}": "[data-alert-wrapper]",
		"{unutilizedAmountLabel}": "[data-upgrade-unutilized-amount-label]",
		"{unutilizedTaxLabel}": "[data-upgrade-unutilized-tax-label]",
		"{priceVariationPlaceholder}": "[data-upgrade-pricevariation-placeholder]",
		"{priceVariation}": "[data-upgrade-pricevariation]",
		"{priceVariationSelection}": "[data-pricevariation-selection]",
		"{discounts}": "[data-upgrade-discounts]",
		"{taxes}": "[data-upgrade-taxes]"

	}
	</selectors>
	<bindings type="javascript">
	{
		init: function() {
		},

		"{closeButton} click": function() {
			this.parent.close();
		},

		"{upgradeButton} click": function() {
			if (PayPlans.$('input[name="type"]:checked').length == 0) {
				this.alertWrapper().html('<?php echo JText::_('COM_PP_UPGRADE_SUBSCRIPTION_SELECT_PAYMENT_MODE', true); ?>');
				this.alertWrapper().removeClass('t-hidden');
				return false;
			}

			this.form().submit();
		},

		"{priceVariationSelection} change": function(element, event) {
			var self = this;
			var selectedPlanId = self.planSelection().val();
			var selectedPricevariation = self.priceVariationSelection().val();

			if (!selectedPlanId) {
				self.hideDetails();
				return;
			}

			// we need to recalculate the un-utilized amount.
			PayPlans.ajax('admin/views/order/calculatePriceVariation', {
				"key" : "<?php echo $order->getKey(); ?>",
				"id" : selectedPlanId,
				"priceVariation": selectedPricevariation
			}).done(function(data) {

				// Always reset to show first
				self.unutilizedAmountLabel().removeClass('t-hidden');
				self.unutilizedTaxLabel().removeClass('t-hidden');

				// show the pricing accordingly.
				self.regularPrice().html(data.price);
				self.unutilizedAmount().html(data.unutilized);
				self.unutilizedTax().html(data.unutilizedTax);
				self.discounts().html(data.discounts);
				self.taxes().html(data.taxes);				
				self.payableAmount().html('<b>' + data.payableAmount + '</b>');

				var isActivateProration = data.isActivateProration;

				// Do not show these unutilized amount during upgrade form if deactivate the upgrade proration
				if (!isActivateProration) {
					self.unutilizedAmountLabel().addClass('t-hidden');
					self.unutilizedTaxLabel().addClass('t-hidden');
				}
			});
		},

		"{planSelection} change": function(element, event) {
			element = $(element);

			var newPlan = element.val();
			var self = this;
			var priceVariationPlaceholder = self.priceVariationPlaceholder();
			var priceVariation = self.priceVariation();

			if (!newPlan) {
				self.paymentDetails().addClass('t-hidden');
				self.paymentOptions().addClass('t-hidden');
				self.upgradeButton().attr('disabled', 'disabled');

				return;
			}

			PayPlans.ajax('admin/views/upgrades/getUpgradeDetails', {
				"upgrade_to": newPlan,
				"id": "<?php echo $subscription->getId();?>"
			}).done(function(data) {

				var isActivateProration = data.isActivateProration;
				
				// Do not show these unutilized amount during upgrade form if deactivate the upgrade proration
				if (!isActivateProration) {
					self.unutilizedAmountLabel().addClass('t-hidden');
					self.unutilizedTaxLabel().addClass('t-hidden');
				}

				self.regularPrice().html(data.price);
				self.unutilizedAmount().html(data.unutilized);
				self.unutilizedTax().html(data.unutilizedTax);
				self.payableAmount().html(data.payableAmount);
				self.discounts().html(data.discounts);
				self.taxes().html(data.taxes);

				self.paymentDetails().removeClass('t-hidden');
				self.paymentOptions().removeClass('t-hidden');
				self.upgradeButton().removeAttr('disabled');				

				if (data.planpricevariation) {
					priceVariationPlaceholder.removeClass('t-hidden');
					priceVariation.html(data.planpricevariation);
					return;
				}

				// Always reset the placeholder when there's nothing to load
				priceVariation.html('');
				priceVariationPlaceholder.addClass('t-hidden');
			});
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_PP_UPGRADE_SELECTED_PLAN'); ?></title>
	<content>
		<form action="<?php echo JRoute::_('index.php');?>" method="post" class="o-form-horizontal pb-md" data-upgrade-form>
			<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md rounded-md  ">
				<?php echo $this->html('form.label', 'Current Plan', 'current', '', '', false); ?>

				<div class="flex-grow">
					<?php echo $subscription->getTitle();?> (<b><?php echo $this->html('html.amount', $subscription->getTotal(), $order->getCurrency());?></b>)
				</div>
			</div>

			<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md rounded-md">
				<?php echo $this->html('form.label', 'Upgrade to Plan', 'upgrade', '', '', false); ?>

				<div class="flex-grow is-column">
					<select name="upgrade_to" class="o-form-control" data-upgrade-new-plan>
						<option value="" selected="selected" disabled="disabled"><?php echo JText::_('COM_PP_UPGRADE_SELECT_NEW_PLAN_OPTION'); ?></option>
						<?php foreach ($plans as $plan) { ?>
						<option value="<?php echo $plan->getId();?>"><?php echo $plan->getTitle();?></option>
						<?php } ?>
					</select>
				
					<div class="t-lg-mt--md" data-upgrade-pricevariation-placeholder>
						<div data-upgrade-pricevariation></div>
					</div>

				</div>
			</div>

			<div class="flex flex-col md:flex-row bg-gray-100 px-xs py-xs rounded-md t-hidden" data-upgrade-payment-details>
				<table class="app-table table">
					<thead>
						<tr>
							<th class="text-left">
								<?php echo JText::_('Payment Details'); ?>
							</th>
							<th class="center" width="30%">
								&nbsp;
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?php echo JText::_('COM_PAYPLANS_UPGRADES_DETAILS_NEW_PRICE');?></td>
							<td class="center">
								<span data-upgrade-regular-price></span>
							</td>
						</tr>

						<tr data-upgrade-unutilized-amount-label>
							<td><?php echo JText::_('COM_PAYPLANS_UPGRADES_DETAILS_NOT_UTILIZED_PAYMENT');?></td>
							<td class="center">
								<span data-upgrade-unutilized></span>
							</td>
						</tr>

						<tr data-upgrade-unutilized-tax-label>
							<td><?php echo JText::_('COM_PAYPLANS_UPGRADES_DETAILS_NOT_UTILIZED_TAX');?></td>
							<td class="center">
								<span data-upgrade-unutilized-tax></span>
							</td>
						</tr>

						<tr>
							<td><?php echo JText::_('COM_PP_UPGRADE_DISCOUNT_MESSAGE'); ?></td>
							<td class="center">
								<span data-upgrade-discounts></span>
							</td>
						</tr>

						<tr>
							<td><?php echo JText::_('COM_PP_UPGRADE_TAX_MESSAGE'); ?></td>
							<td class="center">
								<span data-upgrade-taxes></span>
							</td>
						</tr>

						<tr>
							<td>
								<b><u><?php echo JText::_('COM_PAYPLANS_UPGRADES_DETAILS_NEW_CURRENT_PAYABLE_AMOUNT');?></b></u>
							</td>
							<td class="center">
								<u><b><span data-upgrade-payable-amount></span></b></u>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="o-alert o-alert--danger t-hidden" data-alert-wrapper></div>

			<div class="t-hidden" data-upgrade-payment-options>
				<h3><?php echo JText::_('COM_PP_PAYMENT_MODE');?></h3>

				<div class="o-form-group space-y-sm">
					<?php echo $this->html('form.radio', 'type', 'free', false, 'COM_PP_UPGRADE_TYPE_FREE', 'free-upgrade'); ?>
					<?php echo $this->html('form.radio', 'type', 'offline', false, 'COM_PP_UPGRADE_TYPE_OFFLINE', 'offline-upgrade'); ?>
					<?php echo $this->html('form.radio', 'type', 'user', false, 'COM_PP_UPGRADE_TYPE_USER', 'partial-upgrade'); ?>
				</div>
			</div>

			<?php echo $this->html('form.action', 'subscription', 'upgrade'); ?>
			<?php echo $this->html('form.hidden', 'id', $subscription->getId()); ?>
		</form>
		
	</content>
	<buttons>
		<?php echo $this->fd->html('dialog.button', 'COM_PP_CLOSE_BUTTON', 'default', ['attributes' => 'data-close-button']); ?>
		<?php echo $this->fd->html('dialog.button', 'COM_PP_UPGRADE_BUTTON', 'primary', ['attributes' => 'data-upgrade-button disabled="disabled"']); ?>
	</buttons>
</dialog>
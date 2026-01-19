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
<?php $height = '660px';
if ($this->config->get('upgrade_prorate')) {
	$height = '550px';
} ?>
<dialog>
	<width>550</width>
	<height><?php echo $height;?></height>
	<selectors type="json">
	{
		"{closeButton}" : "[data-close-button]",
		"{submitButton}" : "[data-submit-button]",
		"{planSelection}" : "[data-upgrade-plans]",
		"{priceVariationPlaceholder}": "[data-upgrade-pricevariation-placeholder]",
		"{priceVariation}": "[data-upgrade-pricevariation]",
		"{priceVariationSelection}": "[data-pricevariation-selection]",
		"{form}": "[data-submit-form]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			this.parent.close();
		},

		"{submitButton} click": function(element) {
			this.form().submit();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_PP_ORDER_UPGRADE_WINDOW_TITLE'); ?></title>
	<content>
		<div id="fd">
			<div id="pp">
				<form action="<?php echo JRoute::_('index.php');?>" method="post" data-submit-form>
					<div class="pp-plan-upgrade">
						<div class="pp-plan-upgrade-info mb-md">
							<div class="pp-plan-upgrade-info__title">
								<?php echo JText::_('COM_PAYPLANS_UPGRADES_DETAILS_PREVIOUS_PLAN'); ?>
							</div>
							<div class="pp-plan-upgrade-info__desc">
								<?php echo PPJString::ucfirst($plan->getTitle()); ?>
							</div>
							<div class="pp-plan-upgrade-info__price">
								<?php $this->html('html.amount', $plan->getPrice(), $plan->getCurrency()); ?>
							</div>
						</div>

						<div class="pp-plan-upgrade-info pp-plan-upgrade-info--action mb-md">
							<div class="pp-plan-upgrade-info__title">
								<?php echo JText::_('COM_PAYPLANS_UPGRADES_DETAILS_NEW_PLAN'); ?>
							</div>
							<div class="pp-plan-upgrade-info__desc">
								<?php echo JText::_('COM_PP_UPGRADE_SELECT_NEW_PLAN'); ?>
							</div>
							<div class="pp-plan-upgrade-info__select">
								<div class="o-select-group">
									<select name="upgrade_to" class="o-form-control" data-upgrade-plans>
										<option value="" selected="selected" disabled="disabled"><?php echo JText::_('COM_PP_UPGRADE_SELECT_NEW_PLAN_OPTION'); ?></option>
										<?php foreach ($upgrade_to as $uPlan) { ?>
											<option value="<?php echo $uPlan->getId(); ?>"><?php echo PPJString::ucfirst($uPlan->getTitle()); ?></option>
										<?php } ?>
									</select>
									<span class="o-select-group__drop"></span>
								</div>
							</div>

							<div class="pp-plan-upgrade-info__pricevariation t-lg-mt--lg t-hidden" data-upgrade-pricevariation-placeholder>
								<div class="o-select-group">
									<div data-upgrade-pricevariation></div>

									<span class="o-select-group__drop"></span>
								</div>
							</div>
						</div>

						<?php if (!$this->config->get('upgrade_prorate')) { ?>
							<table class="o-table o-table--borderless">
								<tbody>
									<tr class="text-sm text-gray-800">
										<td class="py-2xs">
											<?php echo JText::_('COM_PAYPLANS_UPGRADES_DETAILS_NEW_PRICE'); ?>
										</td>

										<td class="text-right" data-upgrade-amount>
											<?php echo $this->html('html.amount', '0', $plan->getCurrency()); ?>
										</td>
									</tr>
									<tr class="text-sm text-gray-800" data-ununtilized-amount-label>
										<td class="py-2xs">
											<?php echo JText::_('COM_PAYPLANS_UPGRADES_DETAILS_NOT_UTILIZED_PAYMENT'); ?>
										</td>

										<td class="text-right" data-ununtilized-amount>
											<?php echo $this->html('html.amount', '0', $plan->getCurrency()); ?>
										</td>
									</tr>
									<tr class="text-sm text-gray-800" data-ununtilized-tax-label>
										<td class="py-2xs">
											<?php echo JText::_('COM_PAYPLANS_UPGRADE_TAX_MESSAGE'); ?>
										</td>

										<td class="text-right" data-ununtilized-tax>
											<?php echo $this->html('html.amount', '0', $plan->getCurrency()); ?>
										</td>
									</tr>
									<tr class="text-sm text-gray-800">
										<td class="py-2xs">
											<?php echo JText::_('COM_PP_UPGRADE_DISCOUNT_MESSAGE'); ?>
										</td>

										<td class="text-right" data-discounts>
											<?php echo $this->html('html.amount', '0', $plan->getCurrency()); ?>
										</td>
									</tr>
									<tr class="text-sm text-gray-800">
										<td class="py-2xs">
											<?php echo JText::_('COM_PP_UPGRADE_TAX_MESSAGE'); ?>
										</td>

										<td class="text-right" data-taxes>
											<?php echo $this->html('html.amount', '0', $plan->getCurrency()); ?>
										</td>
									</tr>
								</tbody>
							</table>

							<hr class="flex h-[1px] border-none bg-gray-300">

							<table class="o-table o-table--borderless">

								<tbody>
									<tr class="text-sm text-gray-800">
										<td class="py-2xs">
											<b><?php echo JText::_('COM_PP_UPGRADE_TOTAL_PAYABLE_AMOUNT'); ?></b>
										</td>

										<td class="py-2xs text-right" data-payable-amount>
											<b><?php echo $this->html('html.amount', '0', $plan->getCurrency()); ?></b>
										</td>
									</tr>
								</tbody>
							</table>
						<?php } ?>

						<?php if ($this->config->get('upgrade_prorate')) { ?>
							<table class="o-table o-table--borderless">
								<tbody>
									<tr class="text-sm text-gray-800">
										<td class="py-2xs">
											<?php echo JText::_('COM_PAYPLANS_UPGRADES_DETAILS_NEW_PRICE'); ?>
										</td>

										<td class="py-2xs text-right" data-upgrade-amount>
											<?php echo $this->html('html.amount', '0', $plan->getCurrency()); ?>
										</td>
									</tr>
								</tbody>
							</table>
						<?php } ?>
					</div>

					<?php echo $this->html('form.action', 'order', 'processUpgrade'); ?>
					<?php echo $this->html('form.hidden', 'key', $key); ?>
				</form>
			</div>
		</div>
	</content>
	<buttons>
		<?php echo $this->fd->html('dialog.button', 'COM_PP_CLOSE_BUTTON', 'default', ['attributes' => 'data-close-button']); ?>
		<?php echo $this->fd->html('dialog.button', 'COM_PP_UPGRADE_NOW', 'primary', ['attributes' => 'data-submit-button']); ?>
	</buttons>
</dialog>
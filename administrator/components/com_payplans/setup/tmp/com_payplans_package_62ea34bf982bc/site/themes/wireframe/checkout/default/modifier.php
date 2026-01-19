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
<?php if ($modifiers) { ?>
	<?php foreach ($modifiers as $modifier) { ?>
		<?php 
			if ($modifier->getType() === 'plan_addons') {
				$addon = $modifier->getAddon();

				if (!$addon->isAvailable()) {
					$model = PP::model('addons');
					$addon = PP::Addon($addon->getId());

					$model->removeService($invoice, $addon);

					continue;
				}

				$params = $addon->getParams();
				$default = $params->get('default',0);

				$disabled = ($this->config->get('addons_forceful_default') && $default) ? true: false;
			}
		?>
	<tr class="text-left <?php echo $modifier->isDiscount() ? 'discountable-amount pp-modifiers' : '';?>
			<?php echo $modifier->isTax() ? 'taxable-amount pp-modifiers' : '';?>
			<?php echo $modifier->isNonTaxable() ? 'nontaxable-amount pp-modifiers' : '';?>
		"
		data-pp-modifier-discount
	>
		<td>
			<div class="pp-checkout-table__title"><?php echo JText::_($modifier->message);?></div>
		</td>
		<td class="pp-checkout-table__last-col text-right">	
			<?php $modifierAmount = str_replace('-', '', $modifier->_modificationOf); ?>

			<div class="flex items-center">
				<div class="flex-grow pr-sm">
					<?php if ($modifier->getType() === 'plan_addons') { ?>
						<?php if (!$disabled) { ?>
						<a href="javascript:void(0);" data-addons-item data-type="remove" data-id="<?php echo $addon->getId(); ?>" class="fd-link">
							<?php echo JText::_('COM_PP_REMOVE_PLANADDONS_BUTTON'); ?>
						</a> &nbsp;
						<?php } ?>
					<?php } ?>
				</div>
				<div class="pp-checkout-table__price">
					(<?php echo $modifier->isNegative() ? '-' : '+';?>) <?php echo $this->html('html.amount', $modifierAmount, $invoice->getCurrency()); ?>
				</div>
			</div>
		</td>
	</tr>
	<?php } ?>
<?php } ?>
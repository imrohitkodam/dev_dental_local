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

$total = 0;
?>
<?php if ($this->config->get('addons_enabled') && $addons) { ?>
<div class="pp-checkout-item">
	<div class="pp-checkout-item__title"><?php echo mb_strtoupper(JText::_('COM_PAYPLANS_ORDER_ADDONS'));?></div>

	<div class="pp-checkout-item__content">	
		<table class="pp-checkout-table">
			<tbody>
				<?php foreach ($addons as $addon) { ?>
					<?php
						$purchased = array_key_exists($addon->getId(), $purchasedAddons);
						$params = $addon->getParams();

						$default = $params->get('default',0);
						$forcefulDefault = $this->config->get('addons_forceful_default');
						$disabled = ($forcefulDefault && $default)? true: false;

						$multiple = $this->config->get('addons_select_multiple', 1);

						// Count the total number of the purchased addon without selected by default
						if (!$default && $purchased) {
							$total++;
						}
					?>

					<tr>
						<td class="text-left">
							<div class="pp-checkout-table__title"><?php echo JText::_($addon->getTitle(true, $invoice)); ?></div>

							<div class="pp-checkout-table__desc"><?php echo JText::_($addon->getDescriptions()); ?></div>
						</td>
						<td class="pp-checkout-table__last-col text-right t-va--middle">
							<div class="flex items-center">
								<div class="flex-grow pr-sm" >
									<a href="javascript:void(0);" class="o-btn o-btn--default-o <?php echo (!$purchased) ? '' : 't-hidden'; ?>" data-addons-add-button data-addons-item data-type="add" data-id="<?php echo $addon->getId(); ?>" <?php echo !$multiple && $total > 0 ? 'disabled="true"' : ''; ?>>
										+ <?php echo JText::_('COM_PP_ADD_PLANADDONS_BUTTON'); ?>
									</a>
								</div>
								<div class="">
									<div class="pp-checkout-table__price"><?php echo $addon->getAmount(true, $invoice); ?></div>
								</div>
							</div>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>

		<hr class="flex h-[1px] border-none bg-gray-300">
	</div>
</div>
<?php } ?>
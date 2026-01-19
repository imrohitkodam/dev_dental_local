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
<div class="pp-discount">
	<table class="pp-checkout-table md:w-6/12 md:ml-auto md:mr-no">
		<tbody>
			<tr>
				<td class="text-left">
					<div class="o-form-group o-form-group--ifta t-lg-mb--no" data-pp-discount-wrapper>
						<input class="o-form-control" id="pp-discount-code" placeholder="<?php echo JText::_('COM_PP_CHECKOUT_DISCOUNT_CODE');?>" type="text" data-pp-discount-code>
						<label class="o-form-label" for="pp-discount-code"><?php echo JText::_('COM_PP_CHECKOUT_DISCOUNT_CODE');?></label>
					</div>

					<div class="text-danger" data-pp-discount-message></div>
				</td>
				<td class="text-right align-middle">
					<a href="javascript:void(0);" class="fd-link font-bold pp-checkout-table__discount-link pr-no" data-pp-discount-apply>
						<?php echo JText::_('COM_PP_APPLY_BUTTON');?>
					</a>
				</td>
			</tr>
		</tbody>
	</table>
	<hr class="flex h-[1px] border-none bg-gray-300 md:w-6/12 md:ml-auto md:mr-no">
</div>
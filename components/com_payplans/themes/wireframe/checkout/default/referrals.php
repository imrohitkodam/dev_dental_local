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
<?php if ($this->config->get('discounts_referral') && $referrals) { ?>
<div class="pp-checkout-item">
	<div class="pp-checkout-item__title"><?php echo mb_strtoupper(JText::_('COM_PP_REFERRALS'));?></div>

	<div class="pp-checkout-item__content">
		<div class="pp-checkout-item__desc"><?php echo JText::_('COM_PP_REFERRALS_DESC'); ?></div>

		<table class="pp-checkout-table">
			<tbody>
				<tr>
					<td class="text-left">
						<div class="o-form-group o-form-group--ifta" data-pp-referral-wrapper>
							<div class="o-input-group">
								<input type="text" class="o-form-control" placeholder="<?php echo JText::_('COM_PP_CHECKOUT_REFERRAL_CODE_PLACEHOLDER');?>" data-pp-referral-code />	
								<label class="o-form-label" for="pp-referral-code"><?php echo JText::_('COM_PP_CHECKOUT_REFERRAL_CODE_PLACEHOLDER');?></label>					
							</div>

							<div class="text-danger" data-pp-referral-message></div>
						</div>
					</td>
					<td class="text-right align-middle">
						<a href="javascript:void(0);" class="o-btn o-btn--primary-ghost pp-checkout-table__discount-link t-lg-pr--no" data-pp-referral-apply>
							<?php echo JText::_('COM_PP_APPLY_BUTTON');?>
						</a>
						<a href="javascript:void(0);" class="btn btn-pp-danger-o t-hidden" type="button" data-pp-referral-cancel>
							<i class="fdi fa fa-times"></i>
						</a>
					</td>

				</tr>
			</tbody>
		</table>

		<hr class="flex h-[1px] border-none bg-gray-300" />
	</div>
</div>
<?php } ?>
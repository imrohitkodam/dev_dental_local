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
<?php if (!$userId) { ?>
	<?php echo $this->output('site/checkout/default/login'); ?>

	<div class="pp-checkout-item t-hidden" data-pp-register>
		<div class="pp-checkout-item__title">
			<div class="flex">
				<div class="flex-grow">
					<?php echo mb_strtoupper(JText::_('COM_PP_CHECKOUT_CREATE_NEW_ACCOUNT'));?>
				</div>
				<div class="flex-shrink-0">
					<div style="font-weight: normal;">
						<?php echo JText::_('COM_PP_CHECKOUT_ALREADY_HAVE_ACCOUNT');?> <a href="javascript:void(0);" class="no-underline" data-pp-login-link><?php echo JText::_('COM_PP_CHECKOUT_LOGIN');?></a>
					</div>
				</div>
			</div>
		</div>

		<div class="pp-checkout-item__content">
			<table class="pp-checkout-table">
				<tbody>
					<tr>
						<td class="pp-checkout-table__desc">
							<?php echo JText::_('COM_PP_REGISTER_FOR_NEW_ACCOUNT_INFO');?>
						</td>
					</tr>
					<tr>
						<td class="text-left">
							{JFBCLogin}
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
<?php } ?>
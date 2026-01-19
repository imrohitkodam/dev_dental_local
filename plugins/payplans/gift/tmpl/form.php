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
<div class="pp-checkout-item">
	<div class="pp-checkout-item__title"><?php echo strtoupper(JText::_('COM_PP_GIFT_FORM_HEADING'));?></div>

	<div class="pp-checkout-item__content">
		<table class="pp-checkout-table">
			<tbody>
				<tr>
					<td class="pp-checkout-table__desc">
						<?php echo JText::_('COM_PP_GIFT_DESC');?>
					</td>
				</tr>
				<tr>				
					<td class="t-text--center">
						<a href="javascript:void(0);" class="btn btn-pp-primary" data-pp-gift-purchase>
							<i class="fa fa-gift"></i>&nbsp; <?php echo JText::_('COM_PP_ADD_GIFTS');?>
						</a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<hr class="pp-hr t-lg-mt--no t-lg-mb--no">
</div>
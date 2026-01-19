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
<div class="pp-checkout-item">
	<div class="pp-checkout-item__title"><?php echo strtoupper(JText::_('COM_PP_APP_BASICTAX_TITLE'));?></div>

	<div class="pp-checkout-item__content">
		<table class="pp-checkout-table">
			<tbody>
				<tr>
					<td class="pp-checkout-table__desc">
						<?php echo JText::_('COM_PP_APP_BASICTAX_TITLE_DESC'); ?>
					</td>
				</tr>
				<tr>
					<td class="o-form-group" data-pp-basictax-wrapper>
						<div class="o-input-group">
							<?php echo $this->html('form.country', 'app_basictax_country_id', $country, 'app_basictax_country_id', array('data-pp-basictax-country' => '')); ?>
						</div>

						<div class="t-text--danger" data-pp-basictax-message></div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<hr class="pp-hr t-lg-mt--no t-lg-mb--no">
</div>

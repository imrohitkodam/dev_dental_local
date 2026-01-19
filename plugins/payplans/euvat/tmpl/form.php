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
<div class="pp-checkout-item t-hidden">
	<div class="pp-checkout-item__title"><?php echo strtoupper(JText::_('COM_PP_APP_EUVAT_TITLE'));?></div>

	<div class="pp-checkout-item__content" data-pp-euvat-wrapper>
		<table class="pp-checkout-table">
			<tbody>
				<tr>
					<td>
						<div class="pp-checkout-table__desc">
							<?php echo JText::_('COM_PP_APP_EUVAT_DESC'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<td>		
						<div class="o-input-group">
							<div class="o-select-group">
								<?php echo $this->html('form.country', 'app_euvat_country_id', $country, 'app_euvat_country_id', array('data-pp-euvat-country' => '')); ?>
							</div>
						</div>					
					</td>
				</tr>

				<div data-pp-euvat-company>
					<tr>
						<td>
							<div class="o-input-group">
								<?php echo $this->html('form.text', 'app_euvat_businessname', $business_name, 'app_euvat_businessname', array('placeholder' => 'Enter Business Name here...', 'data-pp-euvat-businessname' => '')); ?>
							</div>		
						</td>
					</tr>
					<tr>
						<td>	
							<div class="o-input-group">
								<?php echo $this->html('form.text', 'app_euvat_vatnumber', $business_vatno, 'app_euvat_vatnumber', array('placeholder' => 'Enter VAT Number here...', 'data-pp-euvat-vatnumber' => '')); ?>
							</div>
						</td>
					</tr>
				</div>

				<div class="t-text--danger" data-pp-euvat-message></div>

				<tr>
					<td class="o-grid-sm">
						<div class="o-grid-sm__cell o-grid-sm__cell--right">
							<button type="button" class="btn btn-pp-default-o" data-pp-euvat-update><?php echo JText::_('COM_PP_APP_EUVAT_UPDATE_TAX_BUTTON'); ?></button>
						</div>
					</td>
				</tr>	
			</tbody>
		</table>
	</div>

	<hr class="pp-hr t-lg-mt--no t-lg-mb--no">
</div>

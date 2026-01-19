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
	<div class="pp-checkout-item__title">
		<?php echo mb_strtoupper(JText::_('COM_PP_CHECKOUT_COMPANY_DETAILS'));?>
	</div>

	<div class="pp-checkout-item__content space-y-sm">
		<?php if (!$hideCompanyNameAndVat) { ?>
			<div data-pp-business class="space-y-sm">
				<?php echo $this->fd->html('form.floatingLabel', 'COM_PP_CHECKOUT_BUSINESS_NAME', 'preference[business_name]', 'text', $business->name, false, [
					'fieldAttributes' => 'data-pp-company-bizname',
					'readOnly' => !$canEditBusinessDetails,
					'attributes' => 'data-pp-form-group',
					'error' => 'COM_PP_FIELD_REQUIRED_MESSAGE',
					'errorAttributes' => 'data-error-message'
				]); ?>

				<?php echo $this->fd->html('form.floatingLabel', 'COM_PP_CHECKOUT_TAX_ID', 'preference[tin]', 'text', $business->vat, false, ['fieldAttributes' => 'data-pp-company-vatno', 'readOnly' => !$canEditBusinessDetails]); ?>
			</div>
		<?php } ?>

		<?php echo $this->fd->html('form.floatingLabel', 'COM_PP_CHECKOUT_ADDRESS', 'preference[business_address]', 'text', $business->address, false, [
			'fieldAttributes' => 'data-pp-company-address',
			'readOnly' => !$canEditBusinessDetails,
			'attributes' => 'data-pp-form-group',
			'error' => 'COM_PP_FIELD_REQUIRED_MESSAGE',
			'errorAttributes' => 'data-error-message'
		]); ?>

		<div class="grid md:grid-cols-3 gap-sm">
			<div>
				<?php echo $this->fd->html('form.floatingLabel', 'COM_PP_CHECKOUT_CITY', 'preference[business_city]', 'text', $business->city, false, [
					'fieldAttributes' => 'data-pp-company-city',
					'readOnly' => !$canEditBusinessDetails,
					'attributes' => 'data-pp-form-group',
					'error' => 'COM_PP_FIELD_REQUIRED_MESSAGE',
					'errorAttributes' => 'data-error-message'
				]); ?>
			</div>

			<div>
				<?php echo $this->fd->html('form.floatingLabel', 'COM_PP_CHECKOUT_STATE', 'preference[business_state]', 'text', $business->state, false, [
					'fieldAttributes' => 'data-pp-company-state',
					'readOnly' => !$canEditBusinessDetails,
					'attributes' => 'data-pp-form-group',
					'error' => 'COM_PP_FIELD_REQUIRED_MESSAGE',
					'errorAttributes' => 'data-error-message'
				]); ?>
			</div>

			<div>
				<?php echo $this->fd->html('form.floatingLabel', 'COM_PP_CHECKOUT_ZIP', 'preference[business_zip]', 'text', $business->zip, false, [
					'fieldAttributes' => 'data-pp-company-zip',
					'readOnly' => !$canEditBusinessDetails,
					'attributes' => 'data-pp-form-group',
					'error' => 'COM_PP_FIELD_REQUIRED_MESSAGE',
					'errorAttributes' => 'data-error-message'
				]); ?>
			</div>
		</div>

		<?php echo $this->html('floatlabel.country',  'COM_PP_CHECKOUT_COUNTRY', 'preference[business_country]', $business->country, '', ['data-pp-company-country' => ''], !$canEditBusinessDetails); ?>

		<div>
			<div class="o-loader o-loader--sm o-loader--inline" data-pp-company-loader></div>
			<label class="" data-pp-company-message></label>
		</div>

	</div>
</div>
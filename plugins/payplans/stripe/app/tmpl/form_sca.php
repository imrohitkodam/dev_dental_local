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
<script src="https://js.stripe.com/v3/"></script>

<script type="text/javascript">
	PayPlans.require()
.done(function($) {

	$('[data-country-select]').on('change', function() {
		var select = $(this);

		if (select.val() == "-1") {
			// unselect all the other options.
			select.find("option:selected").removeAttr("selected");
			select.find("option:selected").prop("selected", false);

			// now reselect the all country
			select.find('option[value="-1"]').attr("selected",true);
			select.find('option[value="-1"]').prop("selected",true);
		} else {
			select.find('option[value="-1"]').removeAttr("selected");
			select.find('option[value="-1"]').prop("selected",false);
		}
		
		$('[data-country-hidden]').val(select.val());
	});

});
</script>

<form method="post" autocomplete="off" action="<?php echo JRoute::_('index.php?option=com_payplans&view=payment&task=complete&action=process&payment_key=' . $payment->getKey());?>" data-pp-stripe-form>

	<div class="o-card o-card--borderless t-lg-mb--lg">
		<div class="o-card__header o-card__header--nobg t-lg-pl--no"><?php echo JText::_('COM_PP_CARD_DETAILS');?></div>

		<div class="o-card__body space-y-sm">
			<div data-pp-stripe-result></div>

			<?php echo $this->html('floatlabel.text', 'COM_PP_NAME_ON_CARD', 'cardholder-name', '', 'cardholder-name'); ?>

			<div id="card-element"></div>

			<div id="card-errors" role="alert"></div> <br>

			<?php if ($billingDetails) { ?>
			<div class="o-card__body space-y-sm">
				<?php echo $this->fd->html('form.floatingLabel', 'COM_PP_CHECKOUT_ADDRESS', 'address', 'text', $billingData->address, false, [
					'fieldAttributes' => 'data-pp-address',
					'attributes' => 'data-pp-form-group'
					]); ?>

			<div class="grid md:grid-cols-3 gap-sm">
				<div>
					<?php echo $this->fd->html('form.floatingLabel', 'COM_PP_CHECKOUT_CITY', 'city', 'text', $billingData->city, false, [
					'fieldAttributes' => 'data-pp-city',
					'attributes' => 'data-pp-form-group'
				]); ?>
				</div>

				<div>
					<?php echo $this->fd->html('form.floatingLabel', 'COM_PP_CHECKOUT_STATE', 'state', 'text', $billingData->state, false, [
					'fieldAttributes' => 'data-pp-state',
					'attributes' => 'data-pp-form-group'
				]); ?>
				</div>

				<div>
					<?php echo $this->fd->html('form.floatingLabel', 'COM_PP_CHECKOUT_ZIP', 'zip', 'text', $billingData->zip, false, [
					'fieldAttributes' => 'data-pp-zip',
					'attributes' => 'data-pp-form-group'
				]); ?>
				</div>
			</div>
			<div class="o-form-group o-form-group--ifta" data-pp-form-group>
				<select data-country-select class="o-form-control" id="country" name="country" data-pp-country>
					<option value="0"><?php echo JText::_('COM_PP_SELECT_COUNTRY'); ?></option>
	
					<?php foreach ($countries as $country) { ?>
						<option value="<?php echo $country->isocode2;?>" <?php echo $billingData->country == $country->country_id ? 'selected="selected"' : '';?>>
							<?php echo $country->title;?>
						</option>
					<?php } ?>
				</select>
				<input data-country-hidden type="hidden" value="">
				<label class="o-form-label" ><?php echo JText::_('COM_PP_CHECKOUT_COUNTRY');?></label>
			</div>
		<?php } ?>

		</div>
	</div>

	<div class="flex items-center">
		<?php echo $this->output('site/payment/default/cancel', ['payment' => $payment]); ?>

		<div class="flex-shrink-0">
			<?php echo $this->fd->html('button.standard', 'COM_PP_COMPLETE_PAYMENT_BUTTON', 'primary', 'default', ['attributes' => 'data-pp-stripe-submit data-secret="' . $publicKey . '"']); ?>
		</div>
	</div>

	<?php echo $this->html('form.hidden', 'dataSecret', $paymentIntentSecret, 'data-secret=""'); ?>
</form>
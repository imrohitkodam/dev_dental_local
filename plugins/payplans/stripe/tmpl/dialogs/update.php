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
<dialog>
	<width>600</width>
	<height>400</height>
	<selectors type="json">
	{
		"{closeButton}" : "[data-close-button]",
		"{submitButton}": "[data-submit-button]",
		"{result}": "[data-pp-stripe-result]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			this.parent.close();
		},
		
		"{submitButton} click": function() {
			var $ = PayPlans.$;
			var self = this;

			self.submitButton().addClass('is-loading');

			// Set public key
			Stripe.setPublishableKey("<?php echo $publicKey;?>");

			var number = $('input[name=stripe_card_num]').val();
			var cvc = $('input[name=stripe_card_code]').val();
			var expireMonth = $('input[name=stripe_exp_month]').val();
			var expireYear = $('input[name=stripe_exp_year]').val();

			var options = {
				"number": number,
				"cvc": cvc,
				"exp_month": expireMonth,
				"exp_year": expireYear
			};


			// Create a new token
			Stripe.createToken(options, function(code, response) {

				if (response.error) {
					self.submitButton().removeClass('is-loading');

					self.result().html(response.error.message);
					self.result().addClass('o-alert o-alert--danger');
					return;
				}

				var token = response.id;

				PayPlans.ajax('plugins/stripe/update', {
					"appId": "<?php echo $appId;?>",
					"subscriptionKey": "<?php echo $subscription->getKey();?>",
					"token": token
				}).done(function() {
					self.submitButton().removeClass('is-loading');
					
					PayPlans.dialog({
						"content": PayPlans.ajax('plugins/stripe/completed')
					});
				}).fail(function(message) {
					
					PayPlans.dialog({
						title: '<?php echo JText::_('COM_PP_UPDATE_PAYMENT_DETAILS', true);?>',
						"content": message
					});
			
				});
			});
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_PP_UPDATE_PAYMENT_DETAILS'); ?></title>
	<content>
		<div id="pp">
			<p class="mb-lg"><?php echo JText::_('COM_PP_UPDATE_PAYMENT_DETAILS_INFO');?></p>

			<div class="o-card o-card--borderless space-y-sm mb-sm">
				<div class="o-card__body">
					<div data-pp-stripe-result>
					</div>

					<?php echo $this->html('form.card', ['card' => 'stripe_card_num', 'expire_month_year' => $dateFormat, 'expire_month' => 'stripe_exp_month', 'expire_year' => 'stripe_exp_year', 'code' => 'stripe_card_code'],
						['stripe_card_num' => $sandbox ? '4012888888881881' : '', 'exp_month_year' => $dateFormat ? $dateFormat : 'MM / YYYY', 'exp_month' => $sandbox ? '12' : '', 'exp_year' => $sandbox ? '2024' : '', 'stripe_card_code' => $sandbox ? '123' : '']
					); ?>
				</div>

				<?php if ($billingDetails) { ?>
					<?php echo $this->html('floatLabel.text', 'COM_PP_CHECKOUT_ADDRESS', 'address', $billingData->address, '', 'data-pp-address'); ?>

					<div class="grid md:grid-cols-3 gap-sm">
						<div>
							<?php echo $this->html('floatLabel.text', 'COM_PP_CHECKOUT_CITY', 'city' ,$billingData->city, '', 'data-pp-city'); ?>
						</div>

						<div>
							<?php echo $this->html('floatLabel.text', 'COM_PP_CHECKOUT_STATE', 'state', $billingData->state, '', 'data-pp-state'); ?>
						</div>

						<div>
							<?php echo $this->html('floatLabel.text', 'COM_PP_CHECKOUT_ZIP', 'zip', $billingData->zip, '', 'data-pp-zip'); ?>
						</div>
					</div>

					<?php echo $this->html('floatlabel.country', 'COM_PP_CHECKOUT_COUNTRY', 'country', $billingData->country, '', array('data-pp-country' => '')); ?>
				<?php } ?>
			</div>
		</div>

		<?php echo $this->html('form.hidden', 'stripeToken', '', 'data-stripe-token=""'); ?>
	</content>
	<buttons>
		<?php echo $this->fd->html('dialog.button', 'COM_PP_CLOSE_BUTTON', 'danger', ['attributes' => 'data-close-button']); ?>
		<?php echo $this->fd->html('dialog.button', 'COM_PP_UPDATE_BUTTON', 'primary', ['attributes' => 'data-submit-button']); ?>
	</buttons>
</dialog>
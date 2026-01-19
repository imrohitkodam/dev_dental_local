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
<form action="<?php echo $postUrl;?>" method="post" data-pp-ccavenue-form>
	<div class="o-card o-card--borderless t-lg-mb--lg">
		<div class="o-card__header o-card__header--nobg t-lg-pl--no"><?php echo JText::_('COM_PAYPLANS_PAYMENT_APP_CCAVENUE_PERSONAL_DETAILS');?></div>

		<div class="o-card__body space-y-sm">
			<?php echo $this->fd->html('form.floatingLabel', 'COM_PAYPLANS_PAYMENT_APP_CCAVENUE_NAME', 'billing_name', 'text', '', false, ['attributes' => 'data-pp-form-group']); ?>

			<div class="grid md:grid-cols-2 gap-sm">
				<div>
					<?php echo $this->fd->html('form.floatingLabel', 'COM_PAYPLANS_PAYMENT_APP_CCAVENUE_EMAIL', 'billing_email', 'text', '', false, ['attributes' => 'data-pp-form-group']); ?>
				</div>

				<div>
					<?php echo $this->fd->html('form.floatingLabel', 'COM_PAYPLANS_PAYMENT_APP_CCAVENUE_PHONE_NUMBER', 'billing_tel', 'text', '', false, ['attributes' => 'data-pp-form-group']);?>
				</div>
			</div>

			<?php echo $this->fd->html('form.floatingLabel', 'COM_PAYPLANS_PAYMENT_APP_CCAVENUE_ADDRESS', 'billing_address', 'text', '', false, ['attributes' => 'data-pp-form-group']); ?>

			<div class="grid md:grid-cols-3 gap-sm">
				<div>
					<?php echo $this->fd->html('form.floatingLabel', 'COM_PAYPLANS_PAYMENT_APP_CCAVENUE_CITY', 'billing_city', 'text', '', false, ['attributes' => 'data-pp-form-group']); ?>
				</div>

				<div>
					<?php echo $this->fd->html('form.floatingLabel', 'COM_PAYPLANS_PAYMENT_APP_CCAVENUE_STATE', 'billing_state', 'text', '', false, ['attributes' => 'data-pp-form-group']); ?>
				</div>

				<div>
					<?php echo $this->fd->html('form.floatingLabel', 'COM_PAYPLANS_PAYMENT_APP_CCAVENUE_ZIP_CODE', 'billing_zip', 'text', '', false, ['attributes' => 'data-pp-form-group']); ?>
				</div>
			</div>

			<?php echo $this->fd->html('form.floatingLabel', 'COM_PAYPLANS_PAYMENT_APP_CCAVENUE_COUNRTY', 'billing_country', 'text', '', false, ['attributes' => 'data-pp-form-group']); ?>

		</div>
	</div>

	<div class="flex items-center">
		<?php echo $this->output('site/payment/default/cancel', ['payment' => $payment]); ?>
		
		<div class="flex-shrink-0">
			<?php echo $this->fd->html('button.submit', 'COM_PP_PROCEED_TO_PAYMENT_BUTTON', 'primary', 'default'); ?>
		</div>
	</div>

	<?php foreach ($data as $key => $value) { ?>
			<?php echo $this->html('form.hidden', $key, $value); ?>
	<?php } ?>
</form>
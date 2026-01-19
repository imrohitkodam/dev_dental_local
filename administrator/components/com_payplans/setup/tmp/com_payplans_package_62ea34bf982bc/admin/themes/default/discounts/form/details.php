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

$discountType = $discount->getCouponType();
?>
<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_DISCOUNT_GENERAL'); ?>

			<div class="panel-body">
				<?php if (!$generator) { ?>
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_DISCOUNT_TITLE', 'title'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.text', 'title', $discount->title, '', ['placeholder' => JText::_('COM_PP_DISCOUNT_COUPON_PLACEHOLDER')]); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_DISCOUNT_PUBLISHED', 'published'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.toggler', 'published', $discount->published); ?>
					</div>
				</div>
				<?php } ?>

				<?php if ($generator) { ?>
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_DISCOUNT_TOTAL_GENERATOR', 'generator_total'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.text', 'generator_total', 10); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_DISCOUNT_GENERATOR_CODE_PREFIX', 'generator_prefix'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.text', 'generator_prefix', 'COUPON_'); ?>
					</div>
				</div>
				<?php } ?>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_DISCOUNT_ALL_PLANS', 'core_discount'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.toggler', 'core_discount', in_array($discountType, [PP_DISCOUNTS_TYPE_GIFT]) ? false : $discount->isCoreDiscount(), 'core_discount', '', [
							'dependency' => '[data-discount-plans]', 
							'dependencyValue' => 0,
							'disabled' => in_array($discountType, [PP_DISCOUNTS_TYPE_GIFT])
						]); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md <?php echo $discount->isCoreDiscount() ? 't-hidden' : '';?>" data-discount-plans>
					<?php echo $this->fd->html('form.label', 'COM_PP_DISCOUNT_PLANS', 'plans'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.plans', 'plans', $discount->getPlans(), in_array($discountType, [PP_DISCOUNTS_TYPE_GIFT]) ? false : true, true, 'data-discount-plans', [], ['theme' => 'fd']); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_DISCOUNT_USAGE_TYPE', 'coupon_type'); ?>

					<?php
						$disabled = "";

						if (in_array($discountType, [PP_DISCOUNTS_TYPE_REFERRAL, PP_DISCOUNTS_TYPE_GIFT])) {
							$disabled = "disabled='disabled'";
						}

						if ($discountType !== PP_DISCOUNTS_TYPE_REFERRAL) {
							unset($types['referral']);
						}

						if ($discountType !== PP_DISCOUNTS_TYPE_GIFT) {
							unset($types['gift']);
						}
					?>

					<div class="flex-grow">
						<select name="coupon_type" class="o-form-control" data-discount-coupon-type <?php echo $disabled; ?>>
							<?php foreach ($types as $key => $value) { ?>
							<option value="<?php echo $key;?>" <?php echo $discountType == $key ? 'selected="selected"' : '';?>><?php echo $value;?></option>
							<?php } ?>
						</select>
					</div>
				</div>

				<?php if (!$generator) { ?>
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md <?php echo in_array($discount->getCouponType(), [null, 'firstinvoice', 'eachrecurring', 'discount_for_time_extend', 'global']) ? '' : 't-hidden';?>"
					data-discount-options="code"
				>
					<?php echo $this->fd->html('form.label', 'COM_PP_DISCOUNT_COUPON_CODE', 'coupon_code'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.text', 'coupon_code', $discount->getCouponCode(), '', array('placeholder' => JText::_('COM_PP_DISCOUNT_COUPON_CODE_PLACEHOLDER'))); ?>
					</div>
				</div>
				<?php } ?>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md <?php echo in_array($discount->getCouponType(), [null, 'firstinvoice', 'eachrecurring', 'autodiscount_onrenewal', 'autodiscount_onupgrade', 'autodiscount_oninvoicecreation', 'global']) ? '' : 't-hidden';?>"
					data-discount-options="type"
				>
					<?php echo $this->fd->html('form.label', 'COM_PP_DISCOUNT_TYPE', 'params[coupon_amount_type]'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.dropdown', 'params[coupon_amount_type]', $params->get('coupon_amount_type'), [
							'fixed' => JText::_('Fixed Amount'),
							'percentage' => JText::_('Percentage')
						]); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md <?php echo in_array($discount->getCouponType(), [null, 'firstinvoice', 'eachrecurring', 'autodiscount_onupgrade', 'autodiscount_oninvoicecreation', 'global']) ? '' : 't-hidden';?>"
					data-discount-options="amount"
				>
					<?php echo $this->fd->html('form.label', 'COM_PP_DISCOUNT_AMOUNT', 'coupon_amount'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.text', 'coupon_amount', $discount->getCouponAmount(), '', ['placeholder' => '5.00']); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md <?php echo in_array($discount->getCouponType(), ['autodiscount_onrenewal']) ? '' : 't-hidden';?>"
					data-discount-options="preexpiry">
					<?php echo $this->fd->html('form.label', 'COM_PP_DISCOUNT_PRE_EXPIRY', 'params[amount_pre_expiry]'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.text', 'params[amount_pre_expiry]', $params->get('amount_pre_expiry')); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md <?php echo in_array($discount->getCouponType(), ['autodiscount_onrenewal']) ? '' : 't-hidden';?>"
					data-discount-options="postexpiry">
					<?php echo $this->fd->html('form.label', 'COM_PP_DISCOUNT_POST_EXPIRY', 'params[amount_post_expiry]'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.text', 'params[amount_post_expiry]', $params->get('amount_post_expiry')); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md <?php echo in_array($discount->getCouponType(), ['discount_for_time_extend']) ? '' : 't-hidden';?>"
					data-discount-options="extendtime">
					<?php echo $this->fd->html('form.label', 'COM_PP_DISCOUNT_EXTEND_SUBSCRIPTION', 'params[extend_time_discount]'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.timer', 'params[extend_time_discount]', $params->get('extend_time_discount')); ?>
					</div>
				</div>

			</div>

		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
		<?php if (!in_array($discountType, [PP_DISCOUNTS_TYPE_GIFT, PP_DISCOUNTS_TYPE_REFERRAL])) { ?>
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_DISCOUNT_ADVANCED'); ?>

			<div class="panel-body">
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md hover:bg-gray-100">
					<?php echo $this->fd->html('form.label', 'COM_PP_DISCOUNT_START_DATE', 'start_date'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.datetimepicker', 'start_date', $discount->getStartDate() ? PP::date($discount->getStartDate(), true)->toSql(true) : ''); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_DISCOUNT_END_DATE', 'end_date'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.datetimepicker', 'end_date', $discount->getEndDate() ? PP::date($discount->getEndDate(), true)->toSql(true) : ''); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_DISCOUNT_USAGE_LIMIT', 'params[allowed_quantity]'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.text', 'params[allowed_quantity]', $params->get('allowed_quantity')); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_DISCOUNT_REUSABLE_BY_USER', 'params[reusable]'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.toggler', 'params[reusable]', $params->get('reusable', true)); ?>
					</div>
				</div>

				<?php if ($this->config->get('multipleDiscount', false)) {  ?>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_DISCOUNT_ALLOW_COMBINING_DISCOUNTS', 'params[allow_clubbing]'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.toggler', 'params[allow_clubbing]', $params->get('allow_clubbing', false)); ?>
					</div>
				</div>

				<?php } ?>
			</div>
		</div>
		<?php } ?>
	</div>
</div>

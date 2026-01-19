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
<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_PLAN_EDIT_PLAN_DETAILS'); ?>

			<div class="panel-body">
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md" data-pp-validate data-type="empty" data-target="plan-title">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_EDIT_PLAN_TITLE', 'title'); ?>
					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'title', $plan->getTitle(), 'title', ['attributes' => 'data-plan-title']); ?>
					</div>
				</div>

				<?php if ($this->config->get('useGroupsForPlan')) { ?>
					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_EDIT_GROUPS_TITLE', 'groups'); ?>
						<div class="flex-grow">
							<?php echo $this->html('form.groups', 'groups', $planGroups); ?>
						</div>
					</div>
				<?php } ?>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_EDIT_PLAN_REDIRECTURL', 'redirecturl'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'redirecturl', $plan->getRedirecturl(), 'redirecturl', []); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_EDIT_PLAN_TEASER_TEXT', 'teasertext'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'teasertext', $plan->getTeasertext(), 'teasertext', []); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_EDIT_PUBLISHED', 'published'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.toggler', 'published', $plan->getPublished(), 'published'); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_EDIT_VISIBLE', 'visible'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.toggler', 'visible', $plan->getVisible(), 'visible'); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_DISPLAY_BILLING', 'show_billing'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.toggler', 'show_billing', $plan->canShowBillingDetails(), '', 'data-pp-plan-show-billing', ['disabled' => !$plan->isFree()]); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_DESCRIPTION', 'description'); ?>

					<div class="flex-grow col-md-7">
						<?php if ($renderEditor) { ?>
							<?php echo $this->html('form.editor', 'description', $plan->getDescription(true), 'description', [], [], [], false); ?>
						<?php } else { ?>
							<?php echo $this->fd->html('form.textarea', 'description', $plan->getDescription(true), 'description', [], false); ?>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_PLAN_EDIT_TIME_PARAMETERS'); ?>
			<div class="panel-body">
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md" data-pp-form-group-wrapper>
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_TIME_EXPIRATION_TYPE', 'expirationtype'); ?>
					<div class="flex-grow">
						<?php echo $this->html('form.dependency', 'expirationtype', $plan->getExpirationtype(), 'expirationtype', 'fixed-expiration-type', $expirationTypes); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_PAYMENT_PRICE', 'price'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'price', $plan->getPrice(), 'price', ['attributes' => 'data-pp-plan-price']); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md" data-expiry-timer data-pp-form-group-wrapper>
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_TIME_EXPIRATION', 'expiration'); ?>
					<div class="flex-grow">
						<?php echo $this->html('form.timer', 'expiration', $plan->getExpiration(PP_PRICE_FIXED, true), 'expiration', 'data-expire-fixed data-expire-recurring data-expire-recurring-trial-1 data-expire-recurring-trial-2'); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md" data-pp-form-group-wrapper>
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_TIME_TRIAL_PRICE_1', 'trial_price_1'); ?>
					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'trial_price_1', $plan->getPrice(PP_PRICE_RECURRING_TRIAL_1), 'trial_price_1', ['attributes' => 'data-expire-recurring-trial-1 data-expire-recurring-trial-2']); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md" data-pp-form-group-wrapper>
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_TIME_TRIAL_TIME_1', 'trial_time_1'); ?>
					<div class="flex-grow">
						<?php echo $this->html('form.timer', 'trial_time_1', $plan->getExpiration(PP_PRICE_RECURRING_TRIAL_1, true), 'trial_time_1', 'data-expire-recurring-trial-1 data-expire-recurring-trial-2'); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md" data-pp-form-group-wrapper>
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_TIME_TRIAL_PRICE_2', 'trial_price_2'); ?>
					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'trial_price_2', $plan->getPrice(PP_PRICE_RECURRING_TRIAL_2), 'trial_price_2', ['attributes' => 'data-expire-recurring-trial-2']); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md" data-pp-form-group-wrapper>
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_TIME_TRIAL_TIME_2', 'trial_time_2'); ?>
					<div class="flex-grow">
						<?php echo $this->html('form.timer', 'trial_time_2', $plan->getExpiration(PP_PRICE_RECURRING_TRIAL_2, true), 'trial_time_2', 'data-expire-recurring-trial-2'); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md" data-pp-form-group-wrapper>
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_TIME_RECURRENCE_COUNT', 'recurrence_count'); ?>
					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'recurrence_count', $plan->getRecurrenceCount(), 'recurrence_count', [
							'class' => 't-text--center',
							'size' => 10,
							'postfix' => JText::_('Times'),
							'attributes' => 'data-expire-recurring data-expire-recurring-trial-1 data-expire-recurring-trial-2'
						]); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md" data-pp-form-group-wrapper>
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_TIME_RECURRENCE_VALIDATION', 'recurr_validation'); ?>
					<div class="flex-grow">
						<?php echo $this->fd->html('button.standard', 'COM_PAYPLANS_ELEMENT_POPUP_CLICK_HERE', 'default', 'default', ['outline' => true, 'attributes' => 'data-recurr-validate data-expire-recurring data-expire-recurring-trial-1 data-expire-recurring-trial-2']); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
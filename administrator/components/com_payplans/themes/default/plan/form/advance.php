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
			<?php echo $this->fd->html('panel.heading', 'COM_PP_PLAN_EDIT_PLAN_ADVANCE'); ?>

			<div class="panel-body">

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_EDIT_PLAN_LIMIT', 'limit_count'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'limit_count', $plan->getMaxSubscriptionLimit(), 'limit_count', array('data-plan-max-limit' => '')); ?>
						<input type="hidden" name="total_count" value="<?php echo $plan->getTotalSubscribers(); ?>" />
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_EDIT_PLAN_SCHEDULED', 'scheduled'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.toggler', 'scheduled', $plan->isScheduled(), 'scheduled', array()); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_EDIT_PLAN_START_DATE', 'start_date'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.datetimepicker', 'start_date', $plan->getPublishedDate()->toSql()); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_EDIT_PLAN_END_DATE', 'end_date'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.datetimepicker', 'end_date', $plan->getUnpublishedDate()->toSql()); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_EDIT_AUTO_APPROVE_SUBSCRIPTION', 'moderate_subscription'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.toggler', 'moderate_subscription', $plan->requireModeration(), 'moderate_subscription', array()); ?>
					</div>
				</div>

			</div>
		</div>
		<div class="panel <?php echo $plan->getExpirationtype() == 'fixed' ? '' : 't-hidden';?>" data-fixed-expiration-wrapper >
			<?php echo $this->fd->html('panel.heading', 'COM_PP_PLAN_FIXED_DATE_EXPIRATION'); ?>
			<div class="panel-body">
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
				<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_EXTEND_SUBSCRIPTION', 'enable_fixed_expiration_date'); ?>
					<div class="flex-grow">
						<?php echo $this->fd->html('form.toggler', 'enable_fixed_expiration_date', $plan->isFixedExpirationDate(), 'enable_fixed_expiration_date', 'data-expire-fixed'); ?>
					</div>
				</div>
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_EXPIRE_ON', 'expiration_date'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.datetimepicker', 'expiration_date', $plan->getExpirationOnDate()->toSql(), ['enableTime' => false]); ?>
					</div>
				</div>
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<label class="o-control-label"></label>
					<div class="flex-grow">
						<?php echo JText::_('COM_PP_PLAN_FIXED_DATERANGE_DESC'); ?> 
					</div>
				</div>
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_EXPORT_REPORTS_FROM', 'subscription_from'); ?>

					<div class="flex-grow">
						<?php $startDate = ""; 
							if ($plan->getParams()->get('subscription_from')) { 
								$startDate = $plan->getSubscriptionFromExpirationDate()->toSql();
							} ?>
						<?php echo $this->fd->html('form.datetimepicker', 'subscription_from', $startDate, ['enableTime' => false]); ?>
					</div>
				</div>
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_EXPORT_REPORTS_TO', 'subscription_to'); ?>

					<div class="flex-grow">
						<?php $endDate = ""; 
							if ($plan->getParams()->get('subscription_to')) { 
								$endDate = $plan->getSubscriptionEndExpirationDate()->toSql();
							} ?>

						<?php echo $this->fd->html('form.datetimepicker', 'subscription_to', $endDate, ['enableTime' => false]); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_PLAN_EDIT_PLAN_RELATIONSHIP'); ?>

			<div class="panel-body">

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_EDIT_PLAN_RELATIONSHIP_DEPENDS_ON', 'parentplans'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.plans', 'parentplans', $plan->getDependablePlans(), true, true, '', [], ['theme' => 'fd']); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_EDIT_PLAN_RELATIONSHIP_DISPLAY_CONDITION', 'displaychildplanon'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.lists', 'displaychildplanon', $plan->getParams()->get('displaychildplanon', PP_CONST_ANY), 'displaychildplanon', '', $childPlansDisplay); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
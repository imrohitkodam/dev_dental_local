<?php
/**
* @package      PayPlans
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
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
			<?php echo $this->fd->html('panel.heading', 'COM_PP_ADDONS_GENERAL'); ?>

			<div class="panel-body">

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_ADDONS_TITLE', 'title'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'title', $addon->title, '', ['placeholder' => JText::_('COM_PP_ADDONS_TITLE_PLACEHOLDER')]); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_ADDONS_DESCRIPTION', 'description'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.textarea', 'description', $addon->description, '', ['rows' => 5]); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_ADDONS_PUBLISHED', 'published'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.toggler', 'published', $addon->published); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_ADDONS_ALL_PLANS', 'apply_on'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.toggler', 'apply_on', $addon->getApplyOn(), 'apply_on', '', [
							'dependency' => '[data-addons-plans]', 
							'dependencyValue' => 0
						]); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md <?php echo $addon->getApplyOn() ? 't-hidden' : ''; ?>" data-addons-plans>
					<?php echo $this->fd->html('form.label', 'COM_PP_ADDONS_PLANS', 'plans'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.plans', 'plans', $addon->getPlans(), true, true, 'data-plans-input', [], ['theme' => 'fd']); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_ADDONS_CONDITION', 'addons_condition'); ?>

					<div class="flex-grow">
						<select name="addons_condition" class="o-form-control">
							<?php foreach ($conditions as $key => $value) { ?>
							<option value="<?php echo $key;?>" <?php echo $addon->addons_condition == $key ? 'selected="selected"' : '';?>><?php echo $value;?></option>
							<?php } ?>
						</select>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_ADDONS_PRICE', 'price'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'price', $addon->getPrice(), '', ['placeholder' => '0.00']); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_ADDONS_PRICETYPE', 'price_type'); ?>

					<div class="flex-grow">
						<select name="price_type" class="o-form-control">
							<?php foreach ($priceTypes as $key => $value) { ?>
							<option value="<?php echo $key;?>" <?php echo $addon->price_type == $key ? 'selected="selected"' : '';?>><?php echo $value;?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_ADDONS_ADVANCED'); ?>

			<div class="panel-body">
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_ADDONS_START_DATE', 'start_date'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.datetimepicker', 'start_date', $addon->getStartDate() ? PP::date($addon->getStartDate(), true)->toSql(true) : ''); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_ADDONS_END_DATE', 'end_date'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.datetimepicker', 'end_date', $addon->getEndDate() ? PP::date($addon->getEndDate(), true)->toSql(true) : ''); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_ADDONS_APPLICABILITY', 'params[applicability]'); ?>

					<div class="flex-grow">
						<select name="params[applicability]" class="o-form-control">
							<?php foreach ($taxesTypes as $key => $value) { ?>
							<option value="<?php echo $key;?>" <?php echo $params->get('applicability') == $key ? 'selected="selected"' : '';?>><?php echo $value;?></option>
							<?php } ?>
						</select>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_ADDONS_AVAILABILITY', 'params[availability]'); ?>

					<div class="flex-grow">
						<select name="params[availability]" class="o-form-control" data-availability>
							<?php foreach ($availabilityTypes as $key => $value) { ?>
							<option value="<?php echo $key;?>" <?php echo $params->get('availability') == $key ? 'selected="selected"' : '';?>><?php echo $value;?></option>
							<?php } ?>
						</select>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md <?php echo ($params->get('availability', 0)) ? '' : 't-hidden'; ?>" data-stock-container>
					<?php echo $this->fd->html('form.label', 'COM_PP_ADDONS_STOCK', 'params[stock]'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'params[stock]', $params->get('stock', ''), '', 'data-stock-input'); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_ADDON_USAGE_TYPE', 'params[usage_type]'); ?>

					<div class="flex-grow">
						<select name="params[usage_type]" class="o-form-control" data-usage-type>
							<?php foreach ($availabilityTypes as $key => $value) { ?>
							<option value="<?php echo $key;?>" <?php echo $params->get('usage_type') == $key ? 'selected="selected"' : '';?>><?php echo $value;?></option>
							<?php } ?>
						</select>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md <?php echo ($params->get('usage_type', 0)) ? '' : 't-hidden'; ?>" data-usage-type-container>
					<?php echo $this->fd->html('form.label', 'COM_PP_ADDON_USAGE_TYPE_LIMIT', 'params[usage_limit]'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'params[usage_limit]', $params->get('usage_limit', 0), '', 'data-usage-type-input'); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_ADDONS_TO_DEFAULT', 'params[default]'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.toggler', 'params[default]', $params->get('default')); ?>
					</div>
				</div>

				<?php if ($addon->getId()) { ?>
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_ADDONS_CONSUMED', ''); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', '', $addon->getConsumed(), '', ['readOnly' => true]); ?>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
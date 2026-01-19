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
	<div class="col-span-1 md:col-span-5 w-auto">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_PP_APP_GENERAL'); ?>

			<div class="panel-body">
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_APP_GENERAL_TITLE', 'title'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'title', $item->title); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_APP_GENERAL_PUBLISH_STATE', 'published'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.toggler', 'published', $item->published); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_ADV_PRICING_UNIT_TITLE', 'units_title'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'units_title', $item->units_title, '', '', array('placeholder' => 'COM_PP_ADV_PRICING_UNIT_TITLE_PLACEHOLDER')); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_ADV_PRICING_UNIT_MIN', 'units_min'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'units_min', $item->units_min); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_ADV_PRICING_UNIT_MAX', 'units_max'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'units_max', $item->units_max); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_APP_GENERAL_DESCRIPTION', 'description'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.textarea', 'description', $item->description, '', array('rows' => 5)); ?>
					</div>
				</div>
			</div>

		</div>
	</div>

	<div class="col-span-1 md:col-span-7 w-auto">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_PP_ADV_PRICING_OPTIONS'); ?>

			<div class="panel-body">

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_ADV_PRICING_ASSIGN_PLAN', 'plans'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.plans', 'plans', $plans, true, true, '', [], ['theme' => 'fd']); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_ADV_PRICING_PRICE_OPTIONS', 'price[]'); ?>

					<div data-select-container>
						<?php if ($priceSet) { ?>
							<?php $i = 1; ?>
							<?php foreach ($priceSet as $set) { ?>
							<div class="flex-grow space-y-sm pb-sm" data-select-row>
								<div class="o-input-group">
									<?php echo $this->fd->html('form.text', 'price[]', $this->html('string.escape', $set['price']), null, ['placeholder' => 'Price', 'attributes' => 'data-select-price']); ?>

									<?php echo $this->fd->html('button.standard', '', 'danger', 'default', ['iconOnly' => true, 'icon' => 'fdi fa fa-minus-circle', 'attributes' => 'data-select-remove', 'class' => ($item->getId() && $i > 1) ? '' : ' t-hidden']); ?>
									<?php echo $this->fd->html('button.standard', '', 'default', 'default', ['iconOnly' => true, 'icon' => 'fdi fa fa-plus-circle', 'attributes' => 'data-select-add']); ?>
								</div>
								<div class="o-input-group" >
									<?php echo $this->html('form.timer', 'duration[]', $set['duration'], ''); ?>
								</div>
							</div>
							<?php $i++; ?>
							<?php } ?>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
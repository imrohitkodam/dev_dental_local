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
		<?php echo $this->output('admin/app/generic/form', ['app' => $app]); ?>
	</div>

	<div class="col-span-1 md:col-span-7 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_APP_PARAMETERS'); ?>

			<div class="panel-body">
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_PRICE_VARIATION_OPTIONS', 'app_params[time_price][time][]'); ?>

					<div  data-select-container>
							<?php if ($options) { ?>
								<?php $i = 1; ?>
								<?php foreach ($options as $option) { ?>
								<div class="flex-grow space-y-sm pb-sm" data-select-row>
									<div class="o-input-group">
										<?php echo $this->fd->html('form.text', 'app_params[time_price][title][]', $this->html('string.escape', $option->title), null, ['placeholder' => 'Title', 'attributes' => 'data-select-title']); ?>

										<?php echo $this->fd->html('form.text', 'app_params[time_price][price][]', $this->html('string.escape', $option->price), null, ['placeholder' => 'Price', 'attributes' => 'data-select-price']); ?>

										<?php echo $this->fd->html('button.standard', '', 'danger', 'default', ['iconOnly' => true, 'icon' => 'fdi fa fa-minus-circle', 'attributes' => 'data-select-remove', 'class' => ($app->getId() && $i > 1) ? '' : ' t-hidden']); ?>
										
										<?php echo $this->fd->html('button.standard', '', 'default', 'default', ['iconOnly' => true, 'icon' => 'fdi fa fa-plus-circle', 'attributes' => 'data-select-add']); ?>
									</div>
									
									<div class="o-input-group" >
										<?php echo $this->html('form.timer', 'app_params[time_price][time][]', $option->time, ''); ?>
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
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
			<?php echo $this->fd->html('panel.heading', 'COM_PP_CONFIG_LAYOUT_DASHBOARD'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'render_site_css', 'COM_PP_CONFIG_RENDER_SITE_CSS', '', '', function() {
					return $this->fd->html('alert.extended', 'COM_PP_NOTE', 'COM_PP_CONFIG_RENDER_SITE_CSS_NOTE', 'warning', [
						'dismissible' => false,
						'class' => 'mt-md'
					]);
				}); ?>

				<?php echo $this->fd->html('settings.toggle', 'layout_pending_orders', 'COM_PP_CONFIG_LAYOUT_HIDE_PENDING_ORDERS'); ?>

				<?php echo $this->fd->html('settings.toggle', 'layout_free_invoices', 'COM_PP_CONFIG_LAYOUT_HIDE_FREE_INVOICES'); ?>

				<?php echo $this->fd->html('settings.toggle', 'layout_download_unpaid_invoices', 'COM_PP_CONFIG_LAYOUT_DOWNLOAD_UNPAID_INVOICES'); ?>

			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_CONFIG_LAYOUT_PLANS'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.dropdown', 'row_plan_counter', 'COM_PP_CONFIG_LAYOUT_TOTAL_PLANS_PER_ROW', [
					'1' => '1 Plan',
					'2' => '2 Plans',
					'3' => '3 Plans',
					'4' => '4 Plans'
				], '', '', function() {
					return $this->fd->html('alert.extended', 'COM_PP_NOTE', 'COM_PP_CONFIG_LAYOUT_PLANS_NOTE', 'warning', [
						'dismissible' => false,
						'class' => 'mt-md'
					]);
				}); ?>
			</div>
		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_CONFIG_LAYOUT_CHECKOUT'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'checkout_display_logo', 'COM_PP_CHECKOUT_DISPLAY_LOGO'); ?>

				<?php echo $this->fd->html('settings.toggle', 'checkout_display_steps', 'COM_PP_CHECKOUT_DISPLAY_STEPS'); ?>

				<?php echo $this->fd->html('settings.toggle', 'checkout_display_fullscreen', 'COM_PP_CHECKOUT_DISPLAY_FULLSCREEN'); ?>

				<?php echo $this->fd->html('settings.toggle', 'checkout_use_animated', 'COM_PP_CHECKOUT_USE_ANIMATED_ICONS'); ?>

				<?php echo $this->fd->html('settings.dropdown', 'checkout_payment_method_layout', 'COM_PP_CONFIG_LAYOUT_CHECKOUT_PAYMENT_METHOD_LAYOUT', [
						'dropdown' => 'COM_PP_CONFIG_PAYMENT_LAYOUT_DROPDOWN',
						'radio_buttons' => 'COM_PP_CONFIG_PAYMENT_LAYOUT_RADIO_BUTTON'
				]); ?>

			</div>
		</div>
	</div>
</div>
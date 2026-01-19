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
	<div class="col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_CONFIG_SETTINGS_SYSTEM_GENERAL'); ?>
			
			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'ajaxindex', 'COM_PP_USE_INDEX_FOR_AJAX_URLS'); ?>
				<?php echo $this->fd->html('settings.toggle', 'show_outdated_message', 'COM_PP_SHOW_SOFTWARE_UPDATE_NOTIFICATIONS'); ?>
				<?php echo $this->fd->html('settings.toggle', 'expert_use_jquery', 'COM_PP_RENDER_JQUERY'); ?>

				<?php echo $this->fd->html('settings.toggle', 'expert_run_automatic_cron', 'COM_PP_ENABLE_AUTOMATED_CRON', '', '', '', '', [
					'dependency' => '[data-pp-automated-cron]', 
					'dependencyValue' => 1]); ?>

				<?php echo $this->fd->html('settings.dropdown', 'cronFrequency', 'COM_PAYPLANS_CONFIG_CRON_FREQUENCY', [
						3600 => 'COM_PAYPLANS_CONFIG_CRON_FREQUENCY_LOWEST',
						1800 => 'COM_PAYPLANS_CONFIG_CRON_FREQUENCY_LOW',
						900 => 'COM_PAYPLANS_CONFIG_CRON_FREQUENCY_NORMAL',
						300 => 'COM_PAYPLANS_CONFIG_CRON_FREQUENCY_HIGH'
					], '', '', '', [
					'wrapperAttributes' => 'data-pp-automated-cron',
					'wrapperClass' => $this->config->get('expert_run_automatic_cron', 1) ? '' : 't-hidden'
				]); ?>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md rounded-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_CONFIG_ENCRYPTION_KEY', 'expert_encryption_key'); ?>

					<div class="flex-grow">
						<div class="o-input-group">
							<input type="text" name="expert_encryption_key" class="o-form-control" value="<?php echo $this->config->get('expert_encryption_key', 'AABBCCDD');?>" 
								<?php echo $this->config->get('expert_encryption_key') ? 'disabled="disabled"' : '';?> type="text" data-key-input />

							<?php if ($this->config->get('expert_encryption_key')) { ?>
								<button class="o-btn o-btn--success-o t-hidden" type="button" data-key-update>
									<i class="fdi fa fa-check"></i>	
								</button>
								<button class="o-btn o-btn--default-o" type="button" data-key-edit><?php echo JText::_('COM_PP_EDIT_BUTTON');?></button>
							<?php } ?>
						</div>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md rounded-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_CONFIG_WAIT_FOR_PAYMENT_BEFORE_EXPIRING', 'expoert_wait_for_payment'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.timer', 'expert_wait_for_payment', $this->config->get('expert_wait_for_payment', '000001000000')); ?>
					</div>
				</div>

				<?php echo $this->fd->html('settings.text', 'recurring_failure_limit', 'COM_PP_CONFIG_RECURRING_PAYMENT_FAILURE_LIMIT'); ?>

				<?php echo $this->fd->html('settings.dropdown', 'expert_auto_delete', 'COM_PP_CONFIG_SYSTEM_AUTO_DELETE_INCOMPLETE_ORDERS', [
						'NEVER' => 'COM_PAYPLANS_CONFIG_AUTO_DELETE_DUMMY_OPTION_NEVER',
						'000001000000' => 'COM_PAYPLANS_CONFIG_AUTO_DELETE_DUMMY_OPTION_ONE_DAY',
						'000003000000' => 'COM_PAYPLANS_CONFIG_AUTO_DELETE_DUMMY_OPTION_THREE_DAYS',
						'000007000000' => 'COM_PAYPLANS_CONFIG_AUTO_DELETE_DUMMY_OPTION_SEVEN_DAYS',
						'000015000000' => 'COM_PAYPLANS_CONFIG_AUTO_DELETE_DUMMY_OPTION_FIFTEEN_DAYS',
						'000100000000' => 'COM_PAYPLANS_CONFIG_AUTO_DELETE_DUMMY_OPTION_ONE_MONTH',
						'000200000000' => 'COM_PAYPLANS_CONFIG_AUTO_DELETE_DUMMY_OPTION_TWO_MONTH',
						'000300000000' => 'COM_PAYPLANS_CONFIG_AUTO_DELETE_DUMMY_OPTION_THREE_MONTH',
						'000600000000' => 'COM_PAYPLANS_CONFIG_AUTO_DELETE_DUMMY_OPTION_SIX_MONTH',
						'010000000000' => 'COM_PAYPLANS_CONFIG_AUTO_DELETE_DUMMY_OPTION_ONE_YEAR'
					]); ?>

				<?php echo $this->fd->html('settings.dropdown', 'stats_build_cron_interval', 'COM_PP_CONFIG_STAT_BUILD_CRON_INTERNAL', [
						'000000010000' => 'COM_PP_CONFIG_STAT_BUILD_CRON_INTERNAL_1_HOUR',
						'000000030000' => 'COM_PP_CONFIG_STAT_BUILD_CRON_INTERNAL_3_HOUR',
						'000000060000' => 'COM_PP_CONFIG_STAT_BUILD_CRON_INTERNAL_6_HOUR',
						'000000090000' => 'COM_PP_CONFIG_STAT_BUILD_CRON_INTERNAL_9_HOUR',
						'000000120000' => 'COM_PP_CONFIG_STAT_BUILD_CRON_INTERNAL_12_HOUR'
					]); ?>

				<?php echo $this->fd->html('settings.toggle', 'notify_admin_new_order', 'COM_PP_NOTIFY_ADMIN_FOR_NEW_ORDER'); ?>

				<?php echo $this->fd->html('settings.toggle', 'notify_user_payment_failure', 'COM_PP_NOTIFY_USER_FOR_PAYMENT_FAILURE'); ?>
			</div>
		</div>
	</div>

	<div class="col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_CONFIG_SYSTEM_STYLESHEET'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'enable_fontawesome', 'COM_PP_CONFIG_ENABLE_FONTAWESOME'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_CONFIG_LOGS'); ?>

			<div class="panel-body">
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md rounded-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_CONFIG_SYSTEM_LOGS_IGNORE_TYPE', 'blockLogging[]'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.select2', 'blockLogging[]', $ignoreLogTypes, [
								'plan' => 'COM_PAYPLANS_CONFIG_BLOCK_LOGGING_FOR_PLAN',
								'order' => 'COM_PAYPLANS_CONFIG_BLOCK_LOGGING_FOR_ORDER',
								'subscription' => 'COM_PAYPLANS_CONFIG_BLOCK_LOGGING_FOR_SUBSCRIPTION',
								'payment' => 'COM_PAYPLANS_CONFIG_BLOCK_LOGGING_FOR_PAYMENT',
								'app' => 'COM_PAYPLANS_CONFIG_BLOCK_LOGGING_FOR_APP',
								'config' => 'COM_PAYPLANS_CONFIG_BLOCK_LOGGING_FOR_CONFIG',
								'cron' => 'COM_PAYPLANS_CONFIG_BLOCK_LOGGING_FOR_CRON',
								'group' => 'COM_PAYPLANS_CONFIG_BLOCK_LOGGING_FOR_GROUP'
							], ['multiple' => true, 'theme' => 'fd']
						);?>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_CONFIG_SYSTEM_MAIL_SETTINGS'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.text', 'mail_from_email', 'COM_PP_CONFIG_MAIL_FROM_EMAIL'); ?>
			</div>
		</div>
	</div>
</div>

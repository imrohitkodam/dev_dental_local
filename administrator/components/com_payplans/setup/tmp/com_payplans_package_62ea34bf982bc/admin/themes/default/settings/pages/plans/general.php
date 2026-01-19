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
			<?php echo $this->fd->html('panel.heading', 'COM_PP_CONFIG_GENERAL_FEATURES'); ?>
	
			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'displayExistingSubscribedPlans', 'COM_PAYPLANS_CONFIG_DISPLAY_EXISTING_SUBSCRIBED_PLANS'); ?>
				<?php echo $this->fd->html('settings.toggle', 'useGroupsForPlan', 'COM_PAYPLANS_CONFIG_USE_GROUPS_FOR_PLAN'); ?>
				<?php echo $this->fd->html('settings.toggle', 'layout_plan_description_use_editor', 'COM_PP_CONFIG_LAYOUT_PLAN_DESCRIPTION_USE_HTML'); ?>
				<?php echo $this->fd->html('settings.toggle', 'layout_plan_include_tax', 'COM_PP_CONFIG_LAYOUT_PLAN_INCLUDE_TAX'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_CONFIG_GENERAL_ADDONS'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'addons_enabled', 'COM_PP_CONFIG_ADDONS_ENABLED'); ?>
				<?php echo $this->fd->html('settings.toggle', 'addons_select_multiple', 'COM_PP_CONFIG_ADDONS_SELECT_MULTIPLE'); ?>
				<?php echo $this->fd->html('settings.toggle', 'addons_forceful_default', 'COM_PP_CONFIG_ADDONS_FORCE_DEFAULT'); ?>
				<?php echo $this->fd->html('settings.toggle', 'show_addonprice_dashbaord', 'COM_PP_CONFIG_ADDONS_SHOW_ADDON_PRICE_DASHBOARD'); ?>
			</div>
		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_CONFIG_GENERAL_ASSIGNS'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'profileplan_enabled', 'COM_PP_CONFIG_ASSIGNS_ENABLED'); ?>
				<?php echo $this->fd->html('settings.toggle', 'profileplan_default_enabled', 'COM_PP_CONFIG_DEFAULT_PROFILEPLAN_ENABLED'); ?>
				<?php echo $this->html('settings.plans', 'profileplan_default', 'COM_PP_CONFIG_PROFILE_DEFAULT_PLAN'); ?>

				<?php echo $this->fd->html('settings.dropdown', 'profile_used', 'COM_PP_CONFIG_PROFILE_TYPE_SOURCE', [
					'joomla_usertype' => 'COM_PP_PROFILE_USED_JOOMLA_USERTYPE',
					'easysocial_profiletype' => 'COM_PP_PROFILE_USED_EASYSOCIAL_PROFILETYPE',
					'jomsocial_profiletype' => 'COM_PP_PROFILE_USED_JOOMSOCIAL_PROFILETYPE'
				]); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_CONFIG_GENERAL_UPGRADES'); ?>
	
			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'upgrade_prorate', 'COM_PAYPLANS_CONFIG_UPGRADE_PRORATE'); ?>
			</div>
		</div>
	</div>
</div>

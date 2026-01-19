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
<?php if ($groups || $plans) { ?>
	<form action="<?php echo JRoute::_('index.php');?>" method="post">
		<div class="flex pb-xs">
			<div class="flex-grow">
				<h2><?php echo JText::_('COM_PP_SELECT_PLAN_HEADING'); ?></h2>
			</div>

			<?php if ($returnUrl) { ?>
			<div class="flex-shrink-0 pt-sm">
				<a href="<?php echo $returnUrl;?>">&larr; <?php echo JText::_('COM_PP_BACK');?></a>
			</div>
			<?php } ?>
		</div>

		<?php echo $renderBadgeStyleCss; ?>
		<div class="pp-plans pp-plans--<?php echo $columns;?> t-lg-mt--xl">
			<?php if ($groups) { ?>
				<?php foreach ($groups as $group) { ?>
					<?php echo $this->output('site/plan/default/group', ['group' => $group]); ?>
				<?php } ?>
			<?php } ?>

			<?php if ($plans) { ?>
				<?php foreach ($plans as $plan) { ?>
					<?php echo $this->output('site/plan/default/plan', ['plan' => $plan, 'columns' => $columns]); ?>
				<?php } ?>
			<?php } ?>
		</div>

		<?php echo $this->html('form.action', 'plan.subscribe'); ?>
	</form>

<?php } else { ?>

	<div class="pp-access-alert pp-access-alert--warning">
		<div class="pp-access-alert__icon"><i class="fdi fas fa-exclamation-circle"></i></div>
		<div class="pp-access-alert__content">
			<div class="pp-access-alert__title t-lg-mb--xl">
				<?php echo JText::_('COM_PP_NO_PLANS_CURRENTLY'); ?>
			</div>

			<div class="pp-access-alert__desc">
				<?php if (!$this->config->get('displayExistingSubscribedPlans')) { ?>
					<?php echo JText::_('COM_PP_NO_OTHER_PLANS_CURRENTLY_INFO'); ?>
				<?php } else { ?>
					<?php echo JText::_('COM_PP_NO_PLANS_CURRENTLY_INFO'); ?>
				<?php } ?>
			</div>

		</div>
		<div class="pp-access-alert__action">
			<?php echo $this->fd->html('button.link', PPR::_('index.php?option=com_payplans&view=dashboard'), 'COM_PP_PROCEED_TO_DASHBOARD_BUTTON', 'primary', 'default', ['icon' => 'fdi fa fa-briefcase']); ?>
		</div>
	</div>
<?php } ?>
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
<form action="index.php" method="post" name="adminForm" id="adminForm">

	<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
		<div class="col-span-1 md:col-span-7">
			<?php if ($this->my->authorise('payplans.statistics', 'com_payplans')) { ?>
				<?php echo $this->fd->html('adminWidgets.statistics', 'Statistics', 'Statistics of sales & revenues from the site', [
					(object) [
						'url' => 'index.php?option=com_payplans&view=invoice&status=' . PP_INVOICE_PAID,
						'icon' => 'fdi fas fa-shopping-cart',
						'title' => 'COM_PAYPLANS_STATISTICS_NUMERIC_SALES',
						'count' => $statistics->totalSales
					],
					(object) [
						'url' => 'index.php?option=com_payplans&view=invoice&status=' . PP_INVOICE_PAID,
						'icon' => 'fdi fas fa-money-check-alt',
						'title' => 'COM_PAYPLANS_STATISTICS_NUMERIC_REVENUE',
						'count' => $this->html('html.amount', $statistics->totalRevenue, PP::getCurrency($this->config->get('currency'))->symbol)
					],
					(object) [
						'url' => 'index.php?option=com_payplans&view=plans',
						'icon' => 'fdi fa fa-tags',
						'title' => 'Coupons',
						'count' => $statistics->currentExpiredSubscription
					],
					(object) [
						'url' => 'index.php?option=com_payplans&view=subscription&status=' . PP_SUBSCRIPTION_ACTIVE,
						'icon' => 'fdi fas fa-user-check',
						'title' => 'DASHBOARD_STATISTICS_ACTIVE_SUBSCRIPTIONS',
						'count' => $statistics->currentActiveSubscription
					],
					(object) [
						'url' => 'index.php?option=com_payplans&view=subscription&status=' . PP_SUBSCRIPTION_EXPIRED,
						'icon' => 'fdi fas fa-user-times',
						'title' => 'DASHBOARD_STATISTICS_EXPIRE_SUBSCRIPTIONS',
						'count' => $statistics->currentExpiredSubscription
					],
					(object) [
						'url' => 'index.php?option=com_payplans&view=plans',
						'icon' => 'fdi fa fa-box-open',
						'title' => 'Plans',
						'count' => $statistics->currentExpiredSubscription
					]
				]);?>
			<?php } ?>

			<?php if ($this->my->authorise('payplans.statistics', 'com_payplans')) { ?>
				<?php echo $this->output('admin/dashboard/charts/default'); ?>
			<?php } ?>
		</div>

		<div class="col-span-1 md:col-span-5">
			<?php echo $this->fd->html('adminwidgets.version', $this->config->get('main_apikey'), PP::getLocalVersion(), PP_SERVICE_VERSION, rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_payplans&task=system.upgrade'); ?>

			<?php echo $this->fd->html('adminwidgets.news'); ?>
		</div>
	</div>

	<?php echo $this->html('form.action', 'payplans'); ?>
</form>

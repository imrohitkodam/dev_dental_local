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
<form class="o-form-horizontal" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" data-pp-form>
	<div data-fd-tab-wrapper>
		<?php echo $this->fd->html('admin.tabs', function() use ($activeTab, $customDetails) {
			$tabs = [
				(object) [
					'id' => 'details',
					'title' => 'COM_PP_DETAILS',
					'active' => !$activeTab || $activeTab === 'details'
				]
			];

			$customDetailTabs = [];

			foreach ($customDetails as $customDetail) {
				$customDetailTabs[] = (object) [
					'id' => 'customdetails-' . $customDetail->id,
					'title' => $customDetail->getTitle(),
					'active' => $activeTab === 'customdetails-' . $customDetail->id
				];
			}

			$tabs = array_merge($tabs, $customDetailTabs);

			$otherTabs = [
				(object) [
					'id' => 'subscriptions',
					'title' => 'COM_PP_SUBSCRIPTIONS',
					'active' => $activeTab === 'subscriptions'
				],
				(object) [
					'id' => 'invoices',
					'title' => 'COM_PP_INVOICES',
					'active' => $activeTab === 'invoices'
				],
				(object) [
					'id' => 'referrals',
					'title' => 'COM_PP_REFERRALS',
					'active' => $activeTab === 'referrals'
				],
				(object) [
					'id' => 'logs',
					'title' => 'COM_PP_LOGS',
					'active' => $activeTab === 'logs'
				]
			];

			$tabs = array_merge($tabs, $otherTabs);

			return $tabs;
		}); ?>

		<div class="tab-content">
			<div id="details" class="t-hidden <?php echo !$activeTab ? 't-block' : '';?>" data-fd-tab-contents>
				<?php echo $this->output('admin/user/form/details'); ?>
			</div>

			<?php foreach ($customDetails as $customDetail) { ?>
			<div id="customdetails-<?php echo $customDetail->id;?>" class="t-hidden <?php echo $activeTab == 'customdetails-' . $customDetail->id ? 't-block' : '';?>" data-fd-tab-contents>
				<?php $output = $customDetail->renderForm($user, false, 'userparams', $user); ?>
				<?php if ($output === false) { ?>
					<?php echo $this->fd->html('alert.standard', 'COM_PP_CUSTOM_DETAILS_XML_ERROR', 'danger'); ?>
				<?php } else { ?>
					<?php echo $output; ?>
				<?php } ?>
			</div>
			<?php } ?>

			<div id="subscriptions" class="t-hidden <?php echo $activeTab == 'subscriptions' ? 't-block' : '';?>" data-fd-tab-contents>
				<?php echo $this->output('admin/user/form/subscriptions'); ?>
			</div>

			<div id="invoices" class="t-hidden <?php echo $activeTab == 'invoices' ? 't-block' : '';?>" data-fd-tab-contents>
				<?php echo $this->output('admin/user/form/invoices'); ?>
			</div>

			<div id="referrals" class="t-hidden <?php echo $activeTab == 'referrals' ? 't-block' : '';?>" data-fd-tab-contents>
				<?php echo $this->output('admin/user/form/referrals'); ?>
			</div>
			
			<div id="logs" class="t-hidden <?php echo $activeTab == 'logs' ? 't-block' : '';?>" data-fd-tab-contents>
				<?php echo $this->output('admin/logs/default/default', array('logs' => $logs, 'pagination' => false, 'form' => false, 'editable' => false, 'renderFilterBar' => false, 'sortable' => false)); ?>
			</div>
		</div>
	</div>

	<?php echo $this->html('form.action', 'user', 'store'); ?>
	<?php echo $this->html('form.hidden', 'activeTab', $activeTab, 'data-pp-active-tab'); ?>
	<?php echo $this->html('form.hidden', 'id', $user->getId()); ?>
</form>
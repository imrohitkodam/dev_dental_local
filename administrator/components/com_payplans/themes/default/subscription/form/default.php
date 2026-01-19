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
		<?php echo $this->fd->html('admin.tabs', $tabs); ?>

		<div class="tab-content">
			<div id="details" class="t-hidden <?php echo !$activeTab ? 't-block' : '';?>" data-fd-tab-contents>
				<?php echo $this->output('admin/subscription/form/details'); ?>
			</div>

			<?php foreach ($customDetails as $customDetail) { ?>
			<div id="customdetails-<?php echo $customDetail->id;?>" class="t-hidden <?php echo $activeTab == 'customdetails-' . $customDetail->id ? 't-block' : '';?>"  data-fd-tab-contents>
				<?php $output = $customDetail->renderForm($subscription, false, 'subscriptionparams'); ?>

				<?php if ($output === false) { ?>
					<div class="o-alert o-alert--error"><?php echo JText::_('COM_PP_CUSTOM_DETAILS_XML_ERROR'); ?></div>
				<?php } else { ?>
					<?php echo $output; ?>
				<?php } ?>
			</div>
			<?php } ?>

			<?php if ($subscription->getId()) { ?>
			<div id="invoices" class="t-hidden <?php echo $activeTab == 'invoices' ? 't-block' : '';?>" data-fd-tab-contents>
				<?php echo $this->output('admin/subscription/form/invoices'); ?>
			</div>

			<div id="transactions" class="t-hidden <?php echo $activeTab == 'transactions' ? 't-block' : '';?>" data-fd-tab-contents>
				<?php echo $this->output('admin/subscription/form/transactions'); ?>
			</div>

			<?php if ($resources) { ?>
				<div id="resources" class="t-hidden <?php echo $activeTab == 'resources' ? 't-block' : '';?>" data-fd-tab-contents>
					<?php echo $this->output('admin/subscription/form/resources', [
							'resources' => $resources,
							'form' => false,
							'editable' => false,
							'pagination' =>false,
							'renderFilterBar' => false,
							'sortable' => false
						]); ?>
				</div>
			<?php } ?>

			<div id="logs" class="t-hidden <?php echo $activeTab == 'logs' ? 't-block' : '';?>" data-fd-tab-contents>
				<?php echo $this->output('admin/logs/default/default', [
					'logs' => $logs, 
					'form' => false, 
					'editable' => false, 
					'pagination' => false, 
					'renderFilterBar' => false, 
					'sortable' => false
				]); ?>
			</div>
			<?php } ?>
		</div>
	</div>

	<?php echo $this->html('form.action', 'subscription', 'store'); ?>
	<?php echo $this->html('form.hidden', 'subscription_id', $subscription->getId()); ?>
</form>
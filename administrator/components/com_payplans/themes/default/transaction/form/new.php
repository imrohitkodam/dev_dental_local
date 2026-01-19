<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form class="o-form-horizontal" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<div class="wrapper accordion">
		<div class="tab-box tab-box-alt">
			<div class="tabbable">

				<ul class="nav nav-tabs nav-tabs-icons">
					<li class="<?php echo !$activeTab ? 'active' : '';?>">
						<a href="#details" data-pp-toggle="tab"><?php echo JText::_('COM_PP_DETAILS'); ?></a>
					</li>
				</ul>

				

				<div data-fd-tab-wrapper>
					<?php echo $this->fd->html('admin.tabs', function() use ($activeTab) {
						$tabs = [
							(object) [
								'id' => 'details',
								'title' => 'COM_PP_DETAILS',
								'active' => !$activeTab || $activeTab === 'details'
							]
						];

						return $tabs;
					}); ?>

					<div class="tab-content">
						<div id="details" class="t-hidden <?php echo !$activeTab || $activeTab === 'details' ? 't-block' : '';?>">
							<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
								<div class="col-span-1 md:col-span-6 w-auto">
									<div class="panel">
										<?php echo $this->html('panel.heading', 'COM_PP_TRANSACTION_DETAILS'); ?>

										<div class="panel-body">
											<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
												<?php echo $this->html('form.label', 'COM_PP_INVOICE_ID', '', 5, false); ?>

												<div class="flex-grow">
													<?php echo $this->html('form.text', 'params[invoice_id]', $invoice->getId(), '', '', array('size' => 8, 'class' => 't-text--center')); ?>
												</div>
											</div>

											<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
												<?php echo $this->html('form.label', 'COM_PAYPLANS_TRANSACTION_EDIT_AMOUNT', '', 5, false); ?>

												<div class="flex-grow">
													<?php echo $this->html('form.text', 'params[amount]', $invoice->getTotal(), '', '', array('size' => 8, 'class' => 't-text--center')); ?>
												</div>
											</div>

											<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
												<?php echo $this->html('form.label', 'COM_PAYPLANS_TRANSACTION_EDIT_GATEWAY_TRANSACTION_ID', '', 5, false); ?>

												<div class="flex-grow">
													<?php echo $this->html('form.text', 'params[gateway_txn_id]', '0', '', '', array('size' => 8, 'class' => 't-text--center')); ?>
												</div>
											</div>

											<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
												<?php echo $this->html('form.label', 'COM_PAYPLANS_TRANSACTION_EDIT_GATEWAY_PARENT_TRANSACTION', '', 5, false); ?>

												<div class="flex-grow">
													<?php echo $this->html('form.text', 'params[gateway_parent_txn]', '0', '', '', array('size' => 8, 'class' => 't-text--center')); ?>
												</div>
											</div>

											<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
												<?php echo $this->html('form.label', 'COM_PAYPLANS_TRANSACTION_EDIT_GATEWAY_SUBSCRIPTION_ID', '', 5, false); ?>

												<div class="flex-grow">
													<?php echo $this->html('form.text', 'params[gateway_subscr_id]', '0', '', '', array('size' => 8, 'class' => 't-text--center')); ?>
												</div>
											</div>

											<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
												<?php echo $this->html('form.label', 'COM_PAYPLANS_TRANSACTION_EDIT_CREATED_DATE', '', 5, false); ?>

												<div class="flex-grow">
													<?php echo PP::date($invoice->getCreatedDate())->format(JText::_('DATE_FORMAT_LC2')); ?>
												</div>
											</div>

											<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
												<?php echo $this->html('form.label', 'COM_PAYPLANS_TRANSACTION_EDIT_MESSAGE', '', 5, false); ?>

												<div class="flex-grow">
													<?php echo $this->html('form.textarea', 'params[message]', ''); ?>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="col-span-1 md:col-span-6 w-auto">
									<?php echo $this->output('admin/transaction/form/user'); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<?php echo $this->html('form.hidden', 'params[payment_id]', $paymentId); ?>
	<?php echo $this->html('form.hidden', 'params[user_id]', $purchaser->getId()); ?>
	<?php echo $this->html('form.action', 'transaction');?>
	<?php echo $this->html('form.hidden', 'from', base64_encode($from)); ?>
</form>
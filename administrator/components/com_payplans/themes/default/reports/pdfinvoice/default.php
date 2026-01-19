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
<form class="o-form-horizontal" name="adminForm" id="adminForm" class="pointsForm" method="post" enctype="multipart/form-data">
	<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
		<div class="col-span-1 md:col-span-6 w-auto">
			<div class="panel">
				<?php echo $this->fd->html('panel.heading', 'COM_PP_HEADING_REPORTS_PDF_INVOICE'); ?>

				<div class="panel-body">

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_PP_REPORTS_PDF_INVOICE_TYPE', 'type'); ?>

						<div class="flex-grow">
							<?php echo $this->html('form.lists', 'type', '', 'type', 'data-export-invoice-type', $exportTypes); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md" data-invoice-key>
						<?php echo $this->fd->html('form.label', 'COM_PP_REPORTS_PDF_INVOICE_KEY', 'invoice_key'); ?>

						<div class="flex-grow">
							<?php echo $this->html('form.text', 'invoice_key', ''); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md t-hidden" data-invoice-transactiondate>
						<?php echo $this->fd->html('form.label', 'COM_PP_EXPORT_PDF_TRANSACTION_DATE_RANGE', 'daterange'); ?>
						
						<div class="flex-grow">
							<?php echo $this->fd->html('form.dateRange', '', 'dateRange', '', ['class' => 'border border-solid border-gray-300 py-xs rounded-md']); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md t-hidden" data-invoice-transactiondate>
						<?php echo $this->fd->html('form.label', 'COM_PP_EXPORT_PDF_EXCLUDE_PLANS', 'plans'); ?>

						<div class="flex-grow">
							<?php echo $this->html('form.plans', 'plans', '', true, true, [], [], ['theme' => 'fd']); ?>
							
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md t-hidden" data-invoice-limit>
						<?php echo $this->fd->html('form.label', 'COM_PP_EXPORT_REPORTS_LIMIT', 'limit'); ?>

						<div class="flex-grow">
							<?php echo $this->fd->html('form.text', 'limit', '50', '', ['postfix' => 'Items']); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php echo $this->html('form.action', 'reports', 'downloadPdf'); ?>
</form>

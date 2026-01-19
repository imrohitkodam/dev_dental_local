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
<form method="post" id="adminForm" class="o-form-horizontal" data-pp-form>
	<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
		<div class="col-span-1 md:col-span-5 w-auto">
			<div class="panel">
				<?php echo $this->html('panel.heading', 'COM_PP_HEADER_RECIPIENT_DETAILS', 'COM_PP_HEADER_RECIPIENT_DETAILS_DESC'); ?>

				<div class="panel-body">
					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_PP_RECIPIENT', 'recipient'); ?>

						<div class="flex-grow">
							<?php echo $this->html('form.text', 'recipient', $recipient, 'recipient'); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_PP_INVOICE_EDIT_SUBJECT', 'subject'); ?>

						<div class="flex-grow">
							<?php echo $this->html('form.text', 'subject', JText::_('COM_PP_INVOICE_AVAILABLE_TO_BE_VIEWED'), 'title'); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_PP_CC', 'cc'); ?>

						<div class="flex-grow">
							<?php echo $this->html('form.text', 'cc', '', 'cc'); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_PP_BCC', 'bcc'); ?>

						<div class="flex-grow">
							<?php echo $this->html('form.text', 'bcc', '', 'bcc'); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_PP_ATTACH_INVOICE', 'attach_invoice'); ?>

						<div class="flex-grow">
							<?php echo $this->html('form.toggler', 'attach_invoice', true, 'attach_invoice'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-span-1 md:col-span-7 w-auto">
			<div class="panel">
				<?php echo $this->html('panel.heading', 'COM_PP_HEADER_EMAIL_CONTENT', 'COM_PP_HEADER_EMAIL_CONTENT_DESC'); ?>

				<div class="panel-body">
					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $editor->display('contents', nl2br(JText::_('COM_PAYPLANS_INVOICE_EMAIL_LINK_BODY')), '100%', '200', '60', '20' ) ;?>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->html('form.rewriter'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php echo $this->html('form.action', 'invoice', 'sendEmail'); ?>
	<?php echo $this->html('form.hidden', 'invoice_id', $invoice->getId()); ?>
	<?php echo $this->html('form.hidden', 'return', base64_encode($return)); ?>
</form>
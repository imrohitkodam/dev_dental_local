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
			<?php echo $this->fd->html('panel.heading', 'COM_PP_CONFIG_INVOICE'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.text', 'expert_invoice_serial_format', 'COM_PAYPLANS_INVOICE_INVOICE_SERIAL_FORMAT'); ?>
				<?php echo $this->fd->html('settings.toggle', 'auto_reset_invoice_serial', 'COM_PP_AUTO_RESET_INVOICE_SERIAL'); ?>
				<?php echo $this->fd->html('settings.toggle', 'skip_free_invoices', 'COM_PP_SKIP_FREE_INVOICES'); ?>
				<?php echo $this->fd->html('settings.toggle', 'enable_pdf_invoice', 'COM_PP_ENABLE_PDF_INVOICE'); ?>

				<?php echo $this->fd->html('settings.dropdown', 'pdf_font', 'COM_PP_CONFIG_PDF_FONTS', [
					'times' => 'COM_PP_PDF_FONT_TIMES',
					'dejavu sans' => 'COM_PP_PDF_FONT_DEJAVU',
					'helvetica' => 'COM_PP_PDF_FONT_HELVETICA'
				]); ?>

				<?php echo $this->fd->html('settings.toggle', 'show_billing_details', 'COM_PP_SHOW_BILLING_DETAILS'); ?>
				<?php echo $this->fd->html('settings.toggle', 'required_company_name', 'COM_PP_BILLING_DETAILS_REQUIRED_COMPANY_NAME'); ?>
				<?php echo $this->fd->html('settings.toggle', 'show_company_name_and_vat', 'COM_PP_BILLING_DETAILS_SHOW_COMPANY_NAME_AND_VAT'); ?>
			</div>
		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_CONFIG_INVOICE_LAYOUT'); ?>
	
			<div class="panel-body">
				<?php echo $this->fd->html('settings.dropdown', 'invoice_source', 'COM_PP_CONFIG_INVOICE_CONTENT_SOURCE', [
					'default' => 'COM_PP_CONFIG_INVOICE_DEFAULT_LAYOUT',
					'custom' => 'COM_PP_CONFIG_INVOICE_CUSTOM_LAYOUT'
				], '', 'data-pp-invoice-source'); ?>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md rounded-md <?php echo $this->config->get('invoice_source') !== 'custom' ? 't-hidden' : '';?>" data-pp-invoice-setting>
					<?php echo $this->fd->html('form.label', 'COM_PP_CONFIG_INVOICE_JOOMLA_ARTICLE', 'invoice_joomla_article'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.article', 'invoice_joomla_article', $this->config->get('invoice_joomla_article'), 'invoice_joomla_article'); ?>
					</div>
				</div>

				<?php echo $this->fd->html('settings.toggle', 'invoice_showlogo', 'COM_PP_CONFIG_INVOICE_SHOW_LOGO', '', '', '', 'data-pp-invoice-setting', [
					'wrapperClass' => $this->config->get('invoice_source') !== 'default' ? 't-hidden' : ''
				]); ?>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md rounded-md <?php echo $this->config->get('invoice_source') !== 'default' ? 't-hidden' : '';?>" data-pp-invoice-setting>
					<?php echo $this->fd->html('form.label', 'COM_PP_CONFIG_INVOICE_COMPANY_LOGO', 'companyLogo'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.imagefile', 'companyLogo', $this->config->get('companyLogo', '')); ?>
					</div>
				</div>

				<?php echo $this->fd->html('settings.text', 'companyName', 'COM_PP_CONFIG_INVOICE_COMPANY_NAME'); ?>
				<?php echo $this->fd->html('settings.textarea', 'companyAddress', 'COM_PP_CONFIG_INVOICE_COMPANY_ADDRESS'); ?>
				<?php echo $this->fd->html('settings.text', 'companyPostCode', 'COM_PP_CONFIG_INVOICE_COMPANY_POSTCODE'); ?>
				<?php echo $this->fd->html('settings.text', 'companyCityCountry', 'COM_PP_CONFIG_INVOICE_COMPANY_CITY'); ?>
				<?php echo $this->fd->html('settings.text', 'companyPhone', 'COM_PP_CONFIG_INVOICE_TELEPHONE'); ?>
				<?php echo $this->fd->html('settings.text', 'companyTaxId', 'COM_PP_CONFIG_INVOICE_COMPANY_TAX_ID'); ?>

				<?php echo $this->fd->html('settings.textarea', 'add_token', 'COM_PP_CONFIG_INVOICE_ADD_CUSTOM_CONTENT', '', '', [
					'wrapperAttributes' => 'data-pp-invoice-setting',
					'visible' => $this->config->get('invoice_source') === 'default'
				]); ?>

				<?php echo $this->html('settings.rewriter', $this->config->get('invoice_source') === 'default', [
					'wrapperAttributes' => 'data-pp-invoice-setting data-search-exclude'
				]); ?>

				<?php echo $this->fd->html('settings.toggle', 'show_blank_token', 'COM_PP_DISPLAY_BLANK_TOKEN'); ?>
				<?php echo $this->fd->html('settings.textarea', 'note', 'COM_PP_CONFIG_INVOICE_NOTE'); ?>
			</div>
		</div>
	</div>
</div>
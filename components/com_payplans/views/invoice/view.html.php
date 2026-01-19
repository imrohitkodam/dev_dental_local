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

PP::import('site:/views/views');

class PayPlansViewInvoice extends PayPlansSiteView
{
	/**
	 * Renders the invoice layout for customer
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Ensure that the user is logged in
		PP::requireLogin();

		$id = $this->getKey('invoice_key');
		$invoice = PP::invoice($id);
		$user = $invoice->getBuyer(true);

		if ($user->getId() != $this->my->id && !PP::isSiteAdmin()) {
			$redirect = PPR::_('index.php?option=com_payplans&view=dashboard', false);

			$this->info->set('COM_PAYPLANS_SUBSCRIPTION_CAN_NOT_VIEW_SUBSCRIPTION_OF_OTHERS_USER', 'error');

			return PP::redirect($redirect);
		}

		// From 3.x,
		// $object_key  = JRequest::getVar('object_key', '');
		// $object_type = JRequest::getVar('object_type', '');
		// $object_id   = XiHelperUtils::getIdFromKey($object_key);
		// $object 	 = call_user_func(array($object_type, 'getInstance'), $object_id);
		// $invoices = PP::model('invoice')->loadRecords(array('object_id' => $object_id, 'object_type' => $object_type));
		// $invoice  = PayplansInvoice::getInstance();

		$title = JText::sprintf('COM_PP_PAGE_TITLE_INVOICE', $invoice->getKey());

		PP::setMeta();
		$this->page->title($title);

		$payment = $invoice->getPayment();
		$modifiers = $invoice->getModifiers();

		$discountablesSerials = [
			PP_MODIFIER_FIXED_DISCOUNTABLE, 
			PP_MODIFIER_PERCENT_DISCOUNTABLE, 
			PP_MODIFIER_PERCENT_OF_SUBTOTAL_DISCOUNTABLE,
			PP_MODIFIER_FIXED_DISCOUNT, 
			PP_MODIFIER_PERCENT_DISCOUNT
		];

		$nonTaxesSerials = [
			PP_MODIFIER_FIXED_NON_TAXABLE, 
			PP_MODIFIER_PERCENT_NON_TAXABLE, 
			PP_MODIFIER_PERCENT_OF_SUBTOTAL_NON_TAXABLE,
			PP_MODIFIER_FIXED_NON_TAXABLE_TAX_ADJUSTABLE
		];

		$taxableSerials = [
			PP_MODIFIER_PERCENT_TAXABLE, 
			PP_MODIFIER_PERCENT_OF_SUBTOTAL_TAXABLE,
			PP_MODIFIER_FIXED_TAX, 
			PP_MODIFIER_PERCENT_TAX
		];

		$print = $this->input->get('print', false, 'bool');

		// Retrieve custom invoice content
		$customInvoiceContent = PP::getCustomInvoiceContent($invoice);

		$this->set('print', $print);
		$this->set('payment', $payment);
		$this->set('modifiers', $modifiers);
		$this->set('invoice', $invoice);
		$this->set('user', $user);
		$this->set('customInvoiceContent', $customInvoiceContent);
		$this->set('discountablesSerials', $discountablesSerials);
		$this->set('taxableSerials', $taxableSerials);
		$this->set('nonTaxesSerials', $nonTaxesSerials);

		parent::display('site/invoice/item/default');
	}

	/**
	 * Renders the invoice confirmation page
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function confirm()
	{
	}

	/**
	 * Download invoice (pdf)
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function download()
	{
		if (!$this->config->get('enable_pdf_invoice')) {
			$this->info->set('COM_PP_INVOICE_PDF_INVOICE_DISABLED', 'error');
			return $this->redirectToView('invoice');
		}

		$invoiceKey = $this->input->get('invoice_key', '');

		$filePrefix = base64_encode($invoiceKey);

		// Get the invoice Id from the provided key
		$invoiceId = (int) PP::encryptor()->decrypt($invoiceKey);

		// Load the invoice object
		$invoice = PP::invoice($invoiceId);

		if (!$invoice->invoice_id) {
			$this->info->set('COM_PP_INVALID_INVOICE_KEY', 'error');
			return $this->redirectToView('invoice');
		}

		$pdf = PP::pdf($invoice);
		$pdfContent = $pdf->generateContent();

		// Convert it into a pdf format
		$pdfObj = $pdf->saveToPdf($pdfContent);
		$pdfObj->stream($filePrefix . '.pdf');

		exit;
	}
}

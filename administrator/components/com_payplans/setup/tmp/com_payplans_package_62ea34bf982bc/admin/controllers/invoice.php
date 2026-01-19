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

class PayplansControllerInvoice extends PayPlansController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('orders');

		$this->registerTask('save', 'store');
		$this->registerTask('apply', 'store');
		$this->registerTask('close', 'cancel');
	}

	/**
	 * Deletes invoices
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function delete()
	{
		$ids = $this->input->get('cid', [], 'array');

		if ($ids) {
			foreach ($ids as $id) {
				$invoice = PP::invoice((int) $id);

				if ($invoice->getId()) {
					$invoice->delete();
				}
			}
		}

		$this->info->set('COM_PP_INVOICE_DELETED_SUCCESSFULLY', 'success');

		return $this->redirectToView('invoice');
	}

	/**
	 * Marks invoice as paid
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function paid()
	{
		$id = $this->input->get('id', 0, 'int');
		$invoice = PP::invoice($id);

		// Only process this as paid provided that the invoice is not paid and hasn't been refunded before
		if (!$invoice->isPaid() && !$invoice->isRefunded()) {
			$invoice->addTransaction();
		}

		$actionlog = PP::actionlog();
		$actionlog->log('COM_PP_ACTIONLOGS_INVOICE_MARK_AS_PAID', 'invoice', [
			'invoiceKey' => $invoice->getKey(),
			'invoiceLink' => 'index.php?option=com_payplans&view=invoice&layout=form&id=' . $invoice->getId()			
		]);

		$this->info->set('COM_PP_INVOICE_MARK_PAID_SUCCESSFULLY', 'success');

		return $this->redirectToView('invoice', 'form', 'id=' . $invoice->getId());
	}

	/**
	 * Executes refund for customer
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function refund()
	{
		$id = $this->input->get('transactionId', 0, 'int');

		$transaction = PP::transaction($id);
		$invoice = $transaction->getInvoice();

		$state = $transaction->refund();

		$actionString = 'COM_PP_ACTIONLOGS_INVOICE_REFUNDED';
		$message = 'COM_PP_REFUND_SUCCESS';

		if (!$state) {
			$message = 'COM_PP_REFUND_FAILED';
		}

		$actionlog = PP::actionlog();
		$actionlog->log($actionString, 'invoice', [
			'invoiceKey' => $invoice->getKey(),
			'invoiceLink' => 'index.php?option=com_payplans&view=invoice&layout=form&id=' . $invoice->getId()			
		]);

		$this->info->set($message, $state ? 'success' : 'danger');
		return $this->redirectToView('invoice', 'form', 'id=' . $invoice->getId());
	}

	/**
	 * Save an invoice.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function store()
	{
		$id = $this->input->get('id', 0, 'int');
		$data = $this->input->post->getArray();

		$invoice = PP::invoice($id);
		$params = $invoice->getParams();

		if ($data['params']) {
			foreach ($data['params'] as $key => $value) {
				$params->set($key, $value);
			}
		}

		$data['params'] = $params->toString();

		$invoice->bind($data);
		$invoice->save();

		$actionlog = PP::actionlog();
		$actionlog->log('COM_PP_ACTIONLOGS_INVOICE_UPDATED', 'invoice', [
			'invoiceKey' => $invoice->getKey(),
			'invoiceLink' => 'index.php?option=com_payplans&view=invoice&layout=form&id=' . $invoice->getId()			
		]);

		$message = 'COM_PP_INVOICE_UPDATED_SUCCESSFULLY';
		$this->info->set($message, 'success');

		$task = $this->getTask();

		if ($task === 'apply') {

			$activeTab = $this->input->get('activeTab', '', 'default');

			if ($activeTab) {
				$activeTab = '&activeTab=' . $activeTab;
			}

			return $this->redirectToView('invoice', 'form', 'id=' . $invoice->getId() . $activeTab);
		}

		return $this->redirectToView('invoice');
	}

	/**
	 * Sends an e-mail to the customer with the invoice
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function sendEmail()
	{
		$recipient = $this->input->get('recipient', '', 'default');
		$cc = $this->input->get('cc', '', 'default');
		$bcc = $this->input->get('bcc', '', 'default');
		$subject = $this->input->get('subject', '', 'default');
		$contents = $this->input->get('contents', '', 'raw');
		$attachInvoice = $this->input->get('attach_invoice', true, 'bool');

		$id = $this->input->get('id', 0, 'int');
		$invoice = PP::invoice($id);

		if ($cc) {
			$cc = explode(',', $cc);
		}

		if ($bcc) {
			$bcc = explode(',', $bcc);
		}

		$recpient = explode(',', $recipient);

		$rewriter = PP::rewriter();
		$contents = $rewriter->rewrite($contents, $invoice);
		$params = ['contents' => $contents];
		$attachments = [];

		if ($attachInvoice && $this->config->get('enable_pdf_invoice')) {
			$pdf = PP::pdf($invoice);
			$pdf->generateFile();
			$attachments[] = $pdf->getFilePath();
		}

		$mailer = PP::mailer();
		$state = $mailer->send($recipient, $subject, 'emails/invoice/resend', $params, $attachments, $cc, $bcc);

		$data = [
			'send_to' => $recipient,
			'cc' => $cc,
			'bcc' => $bcc,
			'body' => $contents
		];

		$redirect = $this->input->get('return', '', 'default');
		$redirect = base64_decode($redirect);

		if ($state instanceof JException) {
			$this->info->set('COM_PAYPLANS_EMAIL_SENDING_FAILED', 'danger');
			PPLog::log(PPLogger::LEVEL_ERROR, JText::_('COM_PAYPLANS_EMAIL_SENDING_FAILED'), 'PPInvoice', $data, '', '', true);
			return $this->app->redirect($redirect);
		}

		$actionlog = PP::actionlog();
		$actionlog->log('COM_PP_ACTIONLOGS_INVOICE_SENT_VIA_EMAIL', 'invoice', [
			'invoiceKey' => $invoice->getKey(),
			'invoiceLink' => 'index.php?option=com_payplans&view=invoice&layout=form&id=' . $invoice->getId(),
			'recipient' => $recipient
		]);

		PPLog::log(PPLogger::LEVEL_INFO, JText::_('COM_PAYPLANS_EMAIL_SEND_SUCCESSFULLY'),'PayplansInvoice', $content);

		$this->info->set('COM_PAYPLANS_EMAIL_SEND_SUCCESSFULLY', 'success');
		return $this->app->redirect($redirect);
	}

}

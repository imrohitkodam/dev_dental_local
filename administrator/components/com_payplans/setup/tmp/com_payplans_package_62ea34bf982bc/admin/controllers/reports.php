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

class PayplansControllerReports extends PayplansController
{
	public function __construct()
	{
		parent::__construct();
		
		$this->checkAccess('reports');
	}
	
	public function export()
	{
		$type = $this->input->get('type', 'invoice');
		$plans = $this->input->get('plans');
		$subsStatus = $this->input->get('subsStatus');
		$invStatus = $this->input->get('invStatus');
		$limit = $this->input->get('limit', 50, 'int');
		$gateway = $this->input->get('gateway');

		$status = $type == 'invoice' ? $invStatus : $subsStatus;

		$model = PP::model($type);
		$actionlog = PP::actionlog();

		$options = [];
		$options['plans'] = $plans;
		$options['status'] = $status;
		$options['limit'] = $limit;
		$options['gateway'] = $gateway;

		$dateRange = $this->input->get('daterange', []);
		$options['dateFrom'] = '';
		$options['dateTo'] = '';
		$from = $to = '';

		if ($dateRange) {
			$from = PP::normalize($dateRange, 'start', '');
			$to = PP::normalize($dateRange, 'end', '');

			if ($from) {
				$options['dateFrom'] = PP::date($from)->toSql();
			}

			if ($to) {
				$options['dateTo'] = PP::date($to)->toSql();	
			}
		}

		// If from and End date both are same then add 1 day to endDate
		if (($from && $to) && ($options['dateFrom'] === $options['dateTo'])) {
			$dateTo = PP::date($to)->addExpiration('000001000000');
			$options['dateTo'] = $dateTo->toSql();
		}

		$records = $model->getDataToExport($options);

		if (empty($records)) {
			$this->info->set('COM_PP_REPORT_NO_RECORD', 'danger');
			return $this->redirectToView('reports', 'export');
		}

		$header = array_keys((array)$records[0]);
		$output = fopen('php://output', 'w');
		fputcsv($output, (array) $header);

		foreach ($records as $record) {

			if ($type === 'invoice' || $type === 'subscription') {
				$record->status = $model->getStatusString($record->status);	

				if ($type === 'invoice' && isset($record->gateway)) {
					$appLib = PP::app($record->gateway);
					$record->gateway = $appLib->getTitle();
				}
			}

			fputcsv($output, (array) $record);
		} 

		$date = JFactory::getDate();

		$fileName = 'export_' . $type . '_' . $date->format('m_d_Y') . '.csv';

		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=' . $fileName);

		fclose($output);

		$actionlog->log('COM_PP_ACTIONLOGS_REPORTS_EXPORT_' . strtoupper($type), 'reports');
		exit;
	}

	/**
	 * Downloads pdf invoices from the site 
	 *
	 * @since	4.2.10
	 * @access	public
	 */
	public function downloadPdf()
	{
		// Todo: ability to download invoice by transaction date
		$invoiceKey = $this->input->get('invoice_key', '');
		$type = $this->input->get('type', '');
		$actionlog = PP::actionlog();

		if ($type === 'invoiceKey') {

			$filePrefix = base64_encode($invoiceKey);

			// Get the invoice Id from the provided key
			$invoiceId = (int) PP::encryptor()->decrypt($invoiceKey);

			// Load the invoice object
			$invoice = PP::invoice($invoiceId);

			if (!$invoice->invoice_id) {
				$this->info->set('COM_PP_INVALID_INVOICE_KEY', 'danger');
				return $this->redirectToView('reports', 'pdfinvoice');
			}

			$pdf = PP::pdf($invoice);
			$pdfContent = $pdf->generateContent();

			$actionlog->log('COM_PP_ACTIONLOGS_REPORTS_PDF_INVOICE_BY_INVOICEKEY', 'reports', [
				'invoiceLink' => 'index.php?option=com_payplans&view=invoice&layout=form&id=' . $invoiceId,
				'invoiceKey' => $invoiceKey
			]);
		}


		if ($type === 'transactionDate') {
			$dateRange = $this->input->get('daterange', []);
			$limit = $this->input->get('limit', 50, 'int');

			if (!$dateRange) {
				$this->info->set('COM_PP_INVALID_DATE', 'danger');
				return $this->redirectToView('reports', 'pdfinvoice');
			}

			$from = PP::normalize($dateRange, 'start', '');
			$to = PP::normalize($dateRange, 'end', '');

			if (empty($from) || empty($to)) {
				$this->info->set('COM_PP_INVALID_DATE', 'danger');
				return $this->redirectToView('reports', 'pdfinvoice');
			}

			$from = PP::date($from)->toSql();
			$to = PP::date($to)->toSql();

			$fromDateToLog = PP::date($from)->format('F d, Y');
			$toDateToLog = PP::date($to)->format('F d, Y');

			$model = PP::model('invoice');
			$results = $model->getInvoiceWithinDates([
				'from' => $from, 
				'to' => $to, 
				'limit' => $limit
			]);

			if (empty($results)) {
				$this->info->set('COM_PP_NO_INVOICES_ON_SELECTED_DATES', 'danger');
				return $this->redirectToView('reports', 'pdfinvoice');
			}

			$invoices = array();
			foreach ($results as $result) {
				$invoices[] = $result->invoice_id;
			}

			$from = explode(' ', $from);
			$to = explode(' ', $to);

			$filePrefix = 'invoices_' . $from[0] . '_' . $to[0];

			$pdfContent = '';

			foreach ($invoices as $invoiceId) {
				$invoice = PP::invoice($invoiceId);
				$pdf = PP::pdf($invoice);

				// Determine when add the breakpage and when need to stop to add a breakpage
				$hasNextPage = next($invoices);
				$cssStyles = $hasNextPage ? 'always' : 'avoid';;

				$pdfContent .= '<div style="page-break-after: ' . $cssStyles . ';"></div>';
				$pdfContent .= $pdf->generateContent();
			}

			$actionlog->log('COM_PP_ACTIONLOGS_REPORTS_PDF_INVOICE_BY_TRANSACTIONDATE', 'reports', [
				'total' => count($invoices),
				'from' => $fromDateToLog,
				'to' => $toDateToLog
			]);
		}
	
		// Convert it into a pdf format
		$pdfObj = $pdf->saveToPdf($pdfContent);
		$pdfObj->stream($filePrefix . '.pdf');

		exit;
	}
}

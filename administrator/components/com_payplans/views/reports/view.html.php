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

class PayPlansViewReports extends PayPlansAdminView
{
	public function __construct()
	{
		parent::__construct();
		
		$this->checkAccess('reports');
	}

	public function display($tpl = null)
	{
		// default to export layout.
		return $this->export($tpl);
	}
	
	/**
	 * Displays the export layout
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function export($tpl = null)
	{
		$this->heading('Export Reports');

		$this->addHelpButton('https://stackideas.com/docs/payplans/administrators/logs-and-reports/export-CSV');

		JToolbarHelper::custom('reports.export', '', '', JText::_('COM_PP_REPORTS_CSV_EXPORT'), false);

		$types = [
			'invoice' => 'Invoices', 
			'user' => 'Users', 
			'subscription' => 'Subscriptions'
		];

		$exportTypes = [];
		
		foreach($types as $key => $val) {
			$obj = new stdClass();
			$obj->title = $val;
			$obj->value = $key;

			$exportTypes[] = $obj;
		}

		// Retrieve available payment gateway
		$model = PP::model('App');
		$options = [
			'group' => 'payment', 
			'published' => 1
		];

		$gateways = $model->loadRecords($options);

		$this->set('exportTypes', $exportTypes);
		$this->set('gateways', $gateways);

		parent::display('reports/export/default');
	}

	/**
	 * Renders the download pdf invoice layout
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function pdfinvoice($tpl = null)
	{
		$this->heading('PDF Invoice');

		$this->addHelpButton('https://stackideas.com/docs/payplans/administrators/logs-and-reports/PDF-invoice');

		JToolbarHelper::custom('reports.downloadPdf', '', '', JText::_('COM_PP_REPORTS_DOWNLOAD_PDF'), false);

		$types = [
			'invoiceKey' => 'Invoices Key', 
			'transactionDate' => 'Transaction Date'
		];

		$exportTypes = [];
		
		foreach ($types as $key => $val) {
			$obj = new stdClass();
			$obj->title = $val;
			$obj->value = $key;

			$exportTypes[] = $obj;
		}

		$this->set('exportTypes', $exportTypes);

		parent::display('reports/pdfinvoice/default');
	}
}
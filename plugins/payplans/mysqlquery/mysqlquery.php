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

jimport('joomla.filesystem.file');

$file = JPATH_ADMINISTRATOR . '/components/com_payplans/includes/payplans.php';

if (!JFile::exists($file)) {
	return;
}

require_once($file);

class plgPayplansMysqlquery extends PPPlugins
{
	/**
	 * Triggered after an invoice is saved
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansInvoiceAfterSave($prev, $new)
	{ 
		// no need to trigger if previous and current state is same
		if ($prev != null && $prev->getStatus() == $new->getStatus()) {
			return true;
		}

		// check if have to remove aup points on refund and current invoice status is refund 
		// then refund aup points and return
		if (!$new->getStatus() == PP_INVOICE_PAID || !$new->isRecurring()) {
			return;
		}

		$availableApps = PPHelperApp::getAvailableApps('mysqlquery');

		// Do not execute this if there doesn't have any MySql query app
		if (!$availableApps) {
			return true;
		}


		$order = $new->getReferenceObject();
		$subscription = $order->getSubscription();

		// Do notning if it's not recurring payment
		$firstInvoice  = $order->getFirstInvoice();
		if ($firstInvoice->getId() == $new->getId()) {
			return false;
		}

		$helper = $this->getAppHelper();

		foreach ($availableApps as $app) {

			$applicable = false;

			if ($app->getParam('applyAll', false) != false) {
				$applicable = true;
			}

			// If there are plans associated with the app, we need to update the points
			$appPlans = $app->getPlans();
			$plan = $new->getPlans();
		
			if (in_array($plan->getId(), $appPlans)) {
				$applicable = true;
			}

			if ($applicable) {
				// Remove previous points
				$expirationQuery = $app->getAppParam('queryOn' . ucfirst('expire'));
				$helper->executeQuery($subscription, $expirationQuery);

				// Add new points
				$activationQuery = $app->getAppParam('queryOn' . ucfirst('active'));
				$helper->executeQuery($subscription, $activationQuery);

			}
		}

		return true;
	}
}
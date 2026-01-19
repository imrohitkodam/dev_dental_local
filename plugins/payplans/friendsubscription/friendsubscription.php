<?php
/**
* @package      PayPlans
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.filesystem.file');

$file = JPATH_ADMINISTRATOR . '/components/com_payplans/includes/payplans.php';

if (!JFile::exists($file)) {
	return;
}

require_once($file);

class plgPayplansFriendSubscription extends PPPlugins
{

	// on render of order, display output
	public function onPayplansViewBeforeExecute($view, $task)
	{
		if (!(($view instanceof PayPlansViewCheckout) || $view instanceof PayPlansViewThanks) && !($view instanceof PayPlansViewInvoice && PP::isFromAdmin())) {
			return true;
		}

		if (PP::isFromAdmin()) {
			$layout = $this->input->get('layout', '', 'default');
			if ($layout != 'form') {
				return true;
			}
		}

		$id = $view->getKey('invoice_key');
		$invoice = PP::invoice($id);

		if ($view instanceof PayPlansViewThanks) {
			$order = $invoice->getReferenceObject();
			$subscription = $order->getSubscription();

			if ($subscription->getParams()->get('friend_subscription_updated', 0)) {

				$friendUserId = $subscription->getParams()->get('friend_user_id', 0);
				$friendUser = PP::user($friendUserId);

				PP::info()->set(JText::sprintf('COM_PP_FRIEND_SUBSCRIPTION_PURCHASED_SUCCESSFULLY', $friendUser->getName()), 'info');
				$redirect = JRoute::_('index.php?option=com_payplans&view=dashboard', false);
				return PP::redirect($redirect);
			}
		}

		$helper = $this->getAppHelper();

		if (!$helper->isEnabled()) {
			return true;
		}

		// Check app applicable for plan or not
		$apps = $this->getAvailableApps();
		$plan = $invoice->getPlan();

		$applicable = false;
		foreach ($apps as $app) {

			$applyAll = $app->getParam('applyAll', 0);

			if ($applyAll) {
				$applicable = true;
				$listOption  = $app->getAppParam('userListOption', 'everyone');
			} else {
				$appPlans = $app->getPlans();

				if (in_array($plan->getId(), $appPlans)) {
					$applicable = true;
					$listOption  = $app->getAppParam('userListOption', 'everyone');
				}
			}

		}

		if (!$applicable) {
			return true;
		}

		$this->set('listOption', $listOption);

		$namespace = 'form';
		$position = 'pp-checkout-options';

		$output = $this->output($namespace);
		$result = array($position => $output);

		return $result;
	}
	
}

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

jimport('joomla.filesystem.file');

$file = JPATH_ADMINISTRATOR . '/components/com_payplans/includes/payplans.php';

if (!JFile::exists($file)) {
	return;
}

require_once($file);

class plgPayplansEasysocialProfiletype extends PPPlugins
{
	/**
	 * When a plan is already selected, we need to set the profile id in the session
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansViewBeforeExecute($view, $task)
	{
		if (PP::isFromAdmin()) {
			return true;
		}

		$helper = $this->getAppHelper();

		if (!$helper->exists()) {
			return true;
		}

		if ($view instanceof PayPlansViewCheckout) {

			// Get the Plan
			$id = $view->getKey('invoice_key');
			$invoice = PP::invoice($id);
			$plan =  $invoice->getPlan();

			// Retrieve the default profile type id
			// Cannot remove this is because user before click the register button on the invoice confirmation page
			// It need to set this profile id session first 
            $profileId = $helper->getDefaultEasysocialProfiletypes();

			$easySocialApp = PPHelperApp::getAvailableApps('easysocialprofiletype');

			// Do not execute this if there doesn't have any Easysocial Profile type app
			if ($easySocialApp) {

				foreach ($easySocialApp as $app) {

					$applicable = $app->isApplicable($plan);

					if (!$applicable) {
						continue;
					}

					$profileId = $app->getAppParam('esprofiletypeOnactive', 0);	
					$profileIdOnHold = $app->getAppParam('esprofiletypeOnHold', 0);		
				}
			}

			// Do not execute this if existing Easysocial profile type doesn't have plan assigned to these app
			if (!$profileId) {
				return true;
			}

			$session = JFactory::getSession();
			$session->set('profile_id', $profileId, SOCIAL_SESSION_NAMESPACE);
			$session->set('PP_EASYSOCIAL_PROFILE', 1, 'payplans');

			// only proceed this if that is guest user
			// Need to prevent some of the user who doesn't complete the payment
			// we need to temporary update this user profile type to regular profile type which respected what user configure from the app
			$currentUserId = PP::user()->id;
			
			if (!$currentUserId && (isset($profileIdOnHold) && $profileIdOnHold)) {
				$member = ES::table('ProfileMap');
				$buyerId = $invoice->getBuyer()->getId();

				$member->loadByUser($buyerId);
				$member->profile_id = $profileIdOnHold;
				$member->user_id = $buyerId;
				$member->state = SOCIAL_STATE_PUBLISHED;
				$member->store();
			}
		}

		return true;
	}
}

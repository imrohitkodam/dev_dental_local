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

PP::import('admin:/includes/model');

class PayplansModelCustomdetails extends PayPlansModel
{
	public function __construct()
	{
		parent::__construct('customdetails');
	}

	/**
	 * Retrieve userdetails app.
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function getCustomDetails($type = 'user', $loadLibrary = false)
	{
		$db = PP::db();

		$query = [];
		$query[] = 'SELECT * FROM `#__payplans_customdetails`';
		$query[] = 'WHERE ' . $db->qn('type') . '=' . $db->Quote($type);

		$query = implode(' ', $query);
		$db->setQuery($query);
		$results = $db->loadObjectList();

		if (!$loadLibrary) {
			return $results;
		}

		$customDetails = [];
		
		foreach ($results as $row) {
			$customDetails[] = PP::customdetails($row);
		}

		return $customDetails;
	}

	/**
	 * Retrieve custom details for a user
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function getPlanCustomDetails(PPPlan $plan, $type = 'user')
	{
		$db = PP::db();

		$query = [];
		$query[] = 'SELECT * FROM `#__payplans_customdetails`';
		$query[] = 'WHERE ' . $db->qn('published') . '=' . $db->Quote(1);
		$query[] = 'AND ' . $db->qn('type') . '=' . $db->Quote($type);

		$query = implode(' ', $query);
		$db->setQuery($query);
		
		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$customDetails = [];

		foreach ($result as $row) {
			$customDetail = PP::customdetails($row);

			$applicable = $customDetail->isPlanApplicable($plan);

			if (!$applicable) {
				continue;
			}

			$customDetails[] = $customDetail;
		}

		return $customDetails;
	}

	/**
	 * Retrieve custom details for a user
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function getUserCustomDetails(PPUser $user)
	{
		$db = PP::db();

		$query = [];
		$query[] = 'SELECT * FROM `#__payplans_customdetails`';
		$query[] = 'WHERE ' . $db->qn('published') . '=' . $db->Quote(1);
		$query[] = 'AND ' . $db->qn('type') . '=' . $db->Quote('user');

		$query = implode(' ', $query);
		$db->setQuery($query);
		
		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$customDetails = [];

		foreach ($result as $row) {
			$customDetail = PP::customdetails($row);

			$applicable = $customDetail->isApplicable($user);

			if (!$applicable) {
				continue;
			}

			$customDetails[] = $customDetail;
		}

		return $customDetails;
	}

	/**
	 * Retrieve custom details for subscription
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function getSubscriptionCustomDetails(PPSubscription $subscription, $ignoreApplicablePlan = false)
	{
		$db = PP::db();
		
		$query = [];
		$query[] = 'SELECT * FROM `#__payplans_customdetails`';
		$query[] = 'WHERE ' . $db->qn('published') . '=' . $db->Quote(1);
		$query[] = 'AND ' . $db->qn('type') . '=' . $db->Quote('subscription');

		$query = implode(' ', $query);
		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$customDetails = [];

		// get this subscription's plan
		$plan = $subscription->getPlan();

		foreach ($result as $row) {
			$customDetail = PP::customdetails($row);

			if (!$ignoreApplicablePlan) {
				$applicable = $customDetail->isPlanApplicable($plan);

				if (!$applicable) {
					continue;
				}
			}

			$customDetails[] = $customDetail;
		}

		return $customDetails;
	}

	/**
	 * Retrieve customdetails field keys.
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function getCustomDetailsFields($type = 'user')
	{
		$customDetails = $this->getCustomDetails($type, true);

		$customDetailsFields = [];

		foreach ($customDetails as $details) {
			$xml = @simplexml_load_string(base64_decode($details->data));
			$fields = $xml->fields;
			foreach ($fields->fieldset as $fieldset) {
				foreach ($fieldset->children() as $child) {

					$optionsValue = [];
					if (isset($child->option)) {

						$options = (array) $child->option;
						$optionsValue = [];

						// Get option values for checkbox, list, radio type fields
						foreach ($child->option as $childOption) {
							$optionsValue[(string) $childOption['value']] = (string) $childOption;
						}
					}

					$name = (string) $child->attributes()['name'];
					$customDetailsFields[$name] = [
						'options' => $optionsValue
					];
				} 
			}
		}

		return $customDetailsFields;
	}
}


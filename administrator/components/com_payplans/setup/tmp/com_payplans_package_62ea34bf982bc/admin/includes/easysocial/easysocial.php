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

class PPEasysocial
{
	protected $file = JPATH_ROOT . '/administrator/components/com_easysocial/includes/easysocial.php';

	/**
	 * Determines if user can edit business details
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function canEditBusinessDetails($userId = null)
	{
		$my = PP::user($userId);

		// If is guest, always return true
		if (!$my->id) {
			return true;
		}

		if (!$this->exists()) {
			return true;
		}

		// Admin doesn't want to allow user to change business details on PayPlans
		$config = PP::config();

		if ($config->get('integrate_es_custom_fields') && !$config->get('allow_edit_field')) {
			return false;
		}

		return true;
	}

	/**
	 * Decorates a user's business data
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function decorateBusinessData($data, $userId = null)
	{
		if (!$this->exists()) {
			return $data;
		}

		$config = PP::config();


		$easysocialData = $this->getCustomFieldsForBusiness($userId);

		// if user has no ability to edit es custom field value in PP, then we will
		// always return the es data irregardless if user has these data stored in PP.
		// #1086
		if (!$config->get('allow_edit_field')) {
			$data = array_merge((array) $data, (array) $easysocialData);
			$data = (object) $data;

			return $data;
		}


		// here we need to take the data from ES only if PP data is empty.
		// #1086
		$dataArr = (array) $data;
		$esArr = (array) $easysocialData;

		foreach ($dataArr as $key => $val) {

			// if PP already has the value, use this value.
			if ($val) {
				continue;
			}

			if (isset($esArr[$key]) && $esArr[$key]) {
				$data->{$key} = $esArr[$key];
			}
		}

		return $data;
	}

	/**
	 * Determines if Easysocial exists
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function exists()
	{
		$enabled = JComponentHelper::isEnabled('com_easysocial');
		$exists = JFile::exists($this->file);

		if (!$exists || !$enabled) {
			return false;
		}

		require_once($this->file);

		return true;
	}

	/**
	 * Retrieves a list of profile type
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getProfileTypes()
	{
		static $profileTypes = null;

		if (is_null($profileTypes)) {
			$db = PP::db();
			$query = 'SELECT * FROM ' . $db->qn('#__social_profiles') . ' WHERE ' . $db->qn('state') . '=' . $db->Quote(1);
			$db->setQuery($query);

			$profileTypes = $db->loadObjectList();
		}

		return $profileTypes;

	}

	/**
	 * Retrieves a list of badges
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getBadges()
	{
		static $badges = null;

		if (is_null($badges)) {
			$db = PP::db();
			$query = 'SELECT * FROM ' . $db->qn('#__social_badges') . ' WHERE ' . $db->qn('state') . '=' . $db->Quote(1);
			$db->setQuery($query);

			$badges = $db->loadObjectList();
		}

		return $badges;
	}

	/**
	 * Retrieves a list of Groups
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getGroups()
	{
		return $this->getClusters(SOCIAL_TYPE_GROUP);
	}

	/**
	 * Retrieves a list of Pages
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getPages()
	{
		return $this->getClusters(SOCIAL_TYPE_PAGE);
	}

	/**
	 * Unified method to retrieve clusters from EasySocial
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getClusters($type)
	{
		static $clusters = [];

		if (!isset($clusters[$type])) {
			$db = PP::db();
			$query = 'SELECT * FROM ' . $db->qn('#__social_clusters') . ' WHERE ' . $db->qn('state') . '=' . $db->Quote(1) . ' AND ' . $db->qn('cluster_type') . '=' . $db->Quote($type);

			$db->setQuery($query);

			$clusters[$type] = $db->loadObjectList('id');
		}

		return $clusters[$type];
	}

	/**
	 * Retrieves a list of cluster's categories
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getClusterCategories($type)
	{
		static $_cache = array();

		$idx = $type;

		if (!isset($_cache[$idx])) {

			$db = PP::db();
			$query = 'SELECT * FROM ' . $db->qn('#__social_clusters_categories');
			$query .= ' WHERE ' . $db->qn('state') . '=' . $db->Quote(1);
			$query .= ' AND ' . $db->qn('type') . '=' . $db->Quote($type);
			$query .= ' AND ' . $db->qn('container') . '=' . $db->Quote(0);

			$db->setQuery($query);

			$_cache[$idx] = $db->loadObjectList('id');
		}

		return $_cache[$idx];
	}

	/**
	 * Retrieves a list of marketplace's categories
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getMarketplaceCategories()
	{
		static $_cache = array();

		$idx = 'marketplace';

		if (!isset($_cache[$idx])) {

			$db = PP::db();
			$query = 'SELECT * FROM ' . $db->qn('#__social_marketplaces_categories');
			$query .= ' WHERE ' . $db->qn('state') . '=' . $db->Quote(1);
			$query .= ' AND ' . $db->qn('container') . '=' . $db->Quote(0);

			$db->setQuery($query);

			$_cache[$idx] = $db->loadObjectList('id');
		}

		return $_cache[$idx];
	}

	/**
	 * Get the ES custom fields for the Business fields
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function getCustomFieldsForBusiness($userId = null)
	{
		$config = PP::config();
		
		if (!$this->exists() || !$config->get('integrate_es_custom_fields')) {
			return false;
		}

		$user = ES::user($userId);
		$uniqueKeys = array(
							'address' => $config->get('unique_key_address'), 
							'name' => $config->get('unique_key_company_name'), 
							'vat' => $config->get('unique_key_vat_id'),
							'shipping' => $config->get('unique_key_shipping_address')
		);

		$data = new stdClass();
				
		foreach ($uniqueKeys as $index => $value) {
			$data->$index = '';

			if ($value) {
				$data->$index = $user->getFieldValue($value);
			}

			// For address field, we need to deconstruct the object
			if ($index == 'address' && $data->$index) {

				// Process other attributes of the address
				$addressObject = $data->$index;

				$data->address = $this->formatAddressField($addressObject->value);
				$data->city = $addressObject->value->city ? $addressObject->value->city : '';
				$data->state = $addressObject->value->state ? $addressObject->value->state : '';
				$data->zip = $addressObject->value->zip ? $addressObject->value->zip : '';
				$data->country = $addressObject->value->country ? $this->getCountryValue($addressObject->value->country) : '';
			}

			if ($index == 'shipping' && $data->$index) {
				$data->$index = $this->formatShippingAddressField($data->$index->value);
			}

			if ($index != 'address' && $index != 'shipping' && $data->$index) {
				$data->$index = $data->$index->value;
			}
		}

		return $data;		
	}

	/**
	 * Format the Shipping Address
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function formatShippingAddressField($field)
	{
		$fields = array(
			'address1' => $field->address1 ? $field->address1 : '',
			'address2' => $field->address2 ? $field->address2 : '',
			'city' => $field->city ? $field->city : '',
			'state' => $field->state ? $field->state : '',
			'zip' => $field->zip ? $field->zip : '',
			'country' => $field->country ? $field->country : ''
		);

		$shippingAddress = array();

		foreach ($fields as $index => $value) {
			if (empty($value) || !$value) {
				continue;
			}

			$shippingAddress[] = $value;
		}

		$shippingAddress = !empty($shippingAddress) ? implode(", ", $shippingAddress) : '';
		
		return $shippingAddress;
	}

	/**
	 * Combine the ES address1 and address2 fields for the business address field
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function formatAddressField($field)
	{
		if (empty($field->address1) && empty($field->address2)) {
			return '';
		}

		$address = $field->address1 . ', ' . $field->address2;

		return $address;
	}

	/**
	 * Retrieve the ES Address's country field value and load into PP country table to return the country_id of it
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function getCountryValue($countryName)
	{ 
		$countryCode = '';

		// Return 0 so that it will just show back the 'Select a Country' in the checkout's country field if there is no value
		if (!$countryName || empty($countryName)) {
			return 0;
		}

		$user = PP::user();
		$esTable = ES::table('Region');

		// Get the code of the $country from `#__social_regions` first
		$esTable->load(array('name' => $countryName));

		if ($esTable->id) {
			$countryCode = $esTable->code;
		}

		$country = PP::getCountryIdByIso($countryCode);

		return $country;
	}


	/**
	 * Decrease easysocial app resource counter
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function decreaseAppResource($resourceTitle, $categoryId, $userId)
	{
		$db = PP::db();
		$query = array();
		$query[] = 'SELECT `count` FROM ' . $db->qn('#__payplans_resource');
		$query[] = 'WHERE ' . $db->qn('value') . '=' . $db->Quote($categoryId);
		$query[] = 'AND ' . $db->qn('title') . '=' . $db->Quote($resourceTitle);
		$query[] = 'AND ' . $db->qn('user_id') .'=' . $db->Quote($userId);

		$query = implode(' ', $query);
		$db->setQuery($query);
		
		$count = (int) $db->loadResult();
		$count = $count != 0 ? $count - 1 : 0;

		$query = array();
		$query[] = 'UPDATE ' . $db->qn('#__payplans_resource') . ' SET `count` = ' . $db->Quote($count);
		$query[] = 'WHERE ' . $db->qn('value') . '=' . $db->Quote($categoryId);
		$query[] = 'AND ' . $db->qn('title') . '=' . $db->Quote($resourceTitle);
		$query[] = 'AND ' . $db->qn('user_id') . '=' . $db->Quote($userId);

		$query = implode(' ', $query);

		$db->setQuery($query);
		$db->query();

		return true;
	}

	/**
	 * Increase easysocial app resource counter
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function increaseAppResource($resourceTitle, $totalSubmission, $categoryId, $userId)
	{
		$db = PP::db();
		$query = array();
		$query[] = 'SELECT `count` FROM ' . $db->qn('#__payplans_resource');
		$query[] = 'WHERE ' . $db->qn('value') . '=' . $db->Quote($categoryId);
		$query[] = 'AND ' . $db->qn('title') . '=' . $db->Quote($resourceTitle);
		$query[] = 'AND ' . $db->qn('user_id') .'=' . $db->Quote($userId);

		$query = implode(' ', $query);
		$db->setQuery($query);
		
		$count = (int) $db->loadResult();
		$count = $count + 1;

		// make sure the limit do not exceed the available boundary.
		$totalSubmission = (int) $totalSubmission;
		if ($count > $totalSubmission) {
			$count = $totalSubmission;
		}

		$query = array();
		$query[] = 'UPDATE ' . $db->qn('#__payplans_resource') . ' SET `count` = ' . $db->Quote($count);
		$query[] = 'WHERE ' . $db->qn('value') . '=' . $db->Quote($categoryId);
		$query[] = 'AND ' . $db->qn('title') . '=' . $db->Quote($resourceTitle);
		$query[] = 'AND ' . $db->qn('user_id') . '=' . $db->Quote($userId);

		$query = implode(' ', $query);

		$db->setQuery($query);
		$db->query();

		return true;
	}

	/**
	* Retrive ES cluster created by user.
	*
	* @since	4.2.0
	* @access	public
	*/
	public function getAppUserEasysocialCluster($clusterType, $userId, $restrictType, $restrictCategories)
	{
		$db = PP::db();

		$query = "SELECT a.`id`, a.`title`, a.`state`, a.`featured`";
		$query .= " FROM `#__social_clusters` AS a";
		$query .= " WHERE a.`creator_uid` = " . $db->Quote($userId);
		$query .= " AND a.`creator_type` = " . $db->Quote(SOCIAL_TYPE_USER);
		$query .= " AND a.`cluster_type` = " . $db->Quote($clusterType);

		if ($restrictType == 'restrict_specific' && $restrictCategories) {
			$query .= " AND a.`category_id` IN (" . implode(',', $restrictCategories) . ")";
		}
		// we only want published / unpublished groups.
		$query .= " AND a.`state` IN (" . SOCIAL_CLUSTER_PUBLISHED . "," . SOCIAL_CLUSTER_UNPUBLISHED . ")";

		$db->setQuery($query);
		$results = $db->loadObjectList('id');

		return $results;
	}

	/**
	* Retrive ES Marketplaces created by user.
	*
	* @since	4.2.0
	* @access	public
	*/
	public function getAppUserEasysocialMarketPlace($userId, $restrictType, $restrictCategories)
	{
		$db = PP::db();

		$query = "SELECT a.`id`, a.`title`, a.`state`, a.`featured`";
		$query .= " FROM `#__social_marketplaces` AS a";
		$query .= " WHERE a.`user_id` = " . $db->Quote($userId);

		if ($restrictType == 'restrict_specific' && $restrictCategories) {
			$query .= " AND a.`category_id` IN (" . implode(',', $restrictCategories) . ")";
		}
		// we only want published / unpublished marketplace.
		$query .= " AND a.`state` IN (" . SOCIAL_CLUSTER_PUBLISHED . "," . SOCIAL_CLUSTER_UNPUBLISHED . "," . SOCIAL_CLUSTER_PENDING .")";

		$db->setQuery($query);
		$results = $db->loadObjectList('id');

		return $results;
	}

	/**
	* Update cluster 's state from PP app.
	*
	* @since	4.2.0
	* @access	public
	*/
	public function appToggleClusterState($state, $clusterIds)
	{
		$db = PP::db();

		$query = "UPDATE `#__social_clusters` SET `state` = " . $db->Quote($state);
		$query .= " WHERE `id` IN (" . implode(',', $clusterIds) . ")";

		$db->setQuery($query);
		$result = $db->query();

		return $result;
	}

	/**
	* Update marketplace's state from PP app.
	*
	* @since	4.2.0
	* @access	public
	*/
	public function appToggleMarketplaceState($state, $marketplaceIds)
	{
		$db = PP::db();

		$query = "UPDATE `#__social_marketplaces` SET `state` = " . $db->Quote($state);
		$query .= " WHERE `id` IN (" . implode(',', $marketplaceIds) . ")";

		$db->setQuery($query);
		$result = $db->query();

		return $result;
	}

	/**
	* Retrive ES Ads created by user.
	*
	* @since	4.2.0
	* @access	public
	*/
	public function getAppUserEasysocialAds($userId)
	{
		$db = PP::db();

		$query = "SELECT COUNT(1) FROM `#__social_ads` AS a ";
		$query .= "INNER JOIN `#__social_advertisers` AS b ON a.`advertiser_id` = b.`id`";
		$query .= " WHERE b.`user_id` = " . $db->Quote($userId);

		$db->setQuery($query);
		$results = $db->loadResult();

		return $results;
	}

	/**
	* Retrive ES Ads created by user.
	*
	* @since	4.2.0
	* @access	public
	*/
	public function getAppUserEasysocialAdsData($userId)
	{
		$db = PP::db();

		$query = "SELECT * FROM `#__social_ads` AS a ";
		$query .= "INNER JOIN `#__social_advertisers` AS b ON a.`advertiser_id` = b.`id`";
		$query .= " WHERE b.`user_id` = " . $db->Quote($userId);

		// we only want published / unpublished groups.
		$query .= " AND a.`state` IN (" . SOCIAL_CLUSTER_PUBLISHED . "," . SOCIAL_CLUSTER_UNPUBLISHED . ")";

		$db->setQuery($query);
		$results = $db->loadObjectList(a.`id`);

		return $results;
	}

	/**
	* Update ads's state from PP app.
	*
	* @since	4.2.0
	* @access	public
	*/
	public function appToggleAdsState($state, $adsIds)
	{
		$db = PP::db();

		$query = "UPDATE `#__social_ads` SET `state` = " . $db->Quote($state);
		$query .= " WHERE `id` IN (" . implode(',', $adsIds) . ")";

		$db->setQuery($query);
		$result = $db->query();

		return $result;
	}

	/**
	* Update cluster's featured state from PP app.
	*
	* @since	5.0.0
	* @access	public
	*/
	public function appToggleClusterFeatured($isFeatured, $clusterIds)
	{
		$db = PP::db();

		$query = "UPDATE `#__social_clusters` SET `featured` = " . $db->Quote($isFeatured);
		$query .= " WHERE `id` IN (" . implode(',', $clusterIds) . ")";

		$db->setQuery($query);
		$result = $db->query();

		return $result;
	}

	/**
	* Update cluster's featured state from PP app.
	*
	* @since	5.0.0
	* @access	public
	*/
	public function appToggleMarketplaceFeatured($isFeatured, $clusterIds)
	{
		$db = PP::db();

		$query = "UPDATE `#__social_marketplaces` SET `featured` = " . $db->Quote($isFeatured);
		$query .= " WHERE `id` IN (" . implode(',', $clusterIds) . ")";

		$db->setQuery($query);
		$result = $db->query();

		return $result;
	}
}

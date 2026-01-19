<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class AdsenseWidgetsProfile extends SocialAppsWidgets
{
	/**
	 * Display user photos on the side bar
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function sidebarBottom($user)
	{
		$validate = $this->validate($user);

		// If the adsense code is not valid, do not proceed.
		if (!$validate) {
			return;
		}

		echo $this->getAdsense($user);
	}

	/**
	 * Display the list of photos a user has uploaded
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getAdsense($user)
	{
		// Get the user params
		$params = $this->getUserParams($user->id);

		// Get default params
		$appParam = $this->app->getParams();

		// Default adsense code
		$defaultCode = $params->get('profile_adsense_code', $appParam->get('profile_adsense_code', ''));

		// Responsive adsense code
		$responsiveCode = $params->get('profile_adsense_responsive_code', $appParam->get('profile_adsense_responsive_code', ''));

		if ($params->get('profile_adsense_use_responsive', $appParam->get('profile_adsense_use_responsive', '')) && $responsiveCode != '') {
			$this->set('code', $responsiveCode);
			return parent::display('widgets/profile/adsense/responsive/default');
		}

		$this->set('code', $defaultCode);
		return parent::display('widgets/profile/adsense/default/default');
	}

	/**
	 * Determines if the adsense is valid
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function validate($user)
	{
		// Get the user params
		$params = $this->getUserParams($user->id);

		// Get the app params
		$appParam = $this->app->getParams();

		// Default adsense code
		$defaultCode = $params->get('profile_adsense_code', $appParam->get('profile_adsense_code', ''));

		// Responsive adsense code
		$responsiveCode = $params->get('profile_adsense_responsive_code', $appParam->get('profile_adsense_responsive_code', ''));

		// Check for default adsense code
		if (!$defaultCode && !$responsiveCode) {
			return false;
		}

		return true;
	}
}

<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_ROOT . '/media/com_easysocial/apps/user/fitbit/libraries/fitbit.php');

class FitbitWidgetsProfile extends SocialAppsWidgets
{
	/**
	 * Renders the user steps details on the cover header
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public function headerMeta($user)
	{
		$params = $this->getParams();

		if (!$params->get('display_widget', true)) {
			return;
		}

		$userParams = $user->getEsParams();

		// Determines if others can view the user's fitbit stats
		$statsAccess = $userParams->get('fitbit_access', false);

		// Do not allow user to view stats
		if (!$statsAccess && $this->my->id != $user->id) {
			return;
		}

		$coverageDays = $params->get('coverage_days', 14);

		$averageSteps = FitBitHelper::getAverageSteps($coverageDays, $user);
		$averageSteps = number_format($averageSteps);

		$permalink = FitBitHelper::getProfileAppUrl($user, $this->app->getAlias());

		$theme = ES::themes();
		$theme->set('permalink', $permalink);
		$theme->set('averageSteps', $averageSteps);
		echo $theme->output('themes:/apps/user/fitbit/widgets/header');
	}
}

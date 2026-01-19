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

class FitbitViewProfile extends SocialAppsView
{
	public function display($userId = null, $docType = null)
	{
		$user = ES::user($userId);
		$userParams = $user->getEsParams();

		// Determines if others can view the user's fitbit stats
		$statsAccess = $userParams->get('fitbit_access', false);

		// Do not allow user to view stats
		if (!$statsAccess && $this->my->id != $user->id) {
			return $this->redirect($user->getPermalink(false));
		}

		$this->app->loadCss();
		$params = $this->app->getParams();
		$table = FitBitHelper::table('Fitbit');
		$exists = $table->load(array('user_id' => $userId));
		$authorizationUrl = false;

		// Display the authorize template
		if (!$exists) {
			$provider = FitBitHelper::getProvider($params->get('client_id'), $params->get('client_secret'));
			$authorizationUrl = $provider->getAuthorizationUrl();
		}

		$coverageDays = $params->get('coverage_days', 14);
		$steps = FitBitHelper::getSteps($coverageDays, $user);

		$averageSteps = FitBitHelper::getAverageSteps($coverageDays, $user);
		$averageSteps = number_format($averageSteps);

		$todaySteps = FitBitHelper::getTodaySteps($user);
		$todaySteps = number_format($todaySteps);

		$highestDay = FitBitHelper::getHighestSteps($coverageDays, $user);

		if ($highestDay) {
			$highestDay->dateObject = JFactory::getDate($highestDay->date);
			$highestDay->value = number_format($highestDay->value);
		}

		$limitEditDays = $params->get('data_edit_days', 0);

		$unlinkUrl = FitBitHelper::getControllerUrl($this->app->id, 'fitbit', 'unlink', false);

		$updatedDate = ES::date($table->updated);

		$this->set('authorizationUrl', $authorizationUrl);
		$this->set('hasFitbit', $exists);
		$this->set('updatedDate', $updatedDate);
		$this->set('unlinkUrl', $unlinkUrl);
		$this->set('user', $user);
		$this->set('todaySteps', $todaySteps);
		$this->set('statsAccess', $statsAccess);
		$this->set('limitEditDays', $limitEditDays);
		$this->set('params', $params);
		$this->set('appId', $this->app->id);
		$this->set('steps', $steps);
		$this->set('highestDay', $highestDay);
		$this->set('averageSteps', $averageSteps);
		$this->set('coverageDays', $coverageDays);

		echo parent::display('profile/default');
	}
}

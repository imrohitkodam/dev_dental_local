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

class FitbitControllerFitbit extends SocialAppsController
{
	/**
	 * Allow user to alter steps data for a given date
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public function edit()
	{
		ES::requireLogin();

		$app = $this->getApp();
		$params = $app->getParams();

		$id = $this->input->get('recordId', 0, 'int');

		$table = FitBitHelper::table('FitbitData');
		$exists = $table->load(array('user_id' => $this->my->id, 'id' => $id));

		if (!$exists) {
			die('Invalid');
		}

		// @TODO: Check if admin configured to allow user editing
		if (!$params->get('data_edit', true)) {
			die('Invalid');
		}

		$newValue = $this->input->get('value', 0, 'int');

		// // Check if the edited value is really larger
		// if ($newValue < $table->value) {
		// 	return $this->ajax->reject(JText::_('Value needs to be greater than the current steps'));
		// }

		$table->value = abs($newValue);
		$table->edited = true;
		$table->store();

		return $this->ajax->resolve(number_format($newValue));
	}

	/**
	 * Allow user to delete steps data
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public function delete()
	{
		ES::requireLogin();

		$app = $this->getApp();
		$params = $app->getParams();

		$id = $this->input->get('recordId', 0, 'int');

		$table = FitBitHelper::table('FitbitData');
		$exists = $table->load(array('user_id' => $this->my->id, 'id' => $id));

		if (!$exists) {
			die('Invalid');
		}

		// @TODO: Check if admin configured to allow user editing
		if (!$params->get('data_edit', true)) {
			die('Invalid');
		}

		$table->delete();

		return $this->ajax->resolve();
	}

	/**
	 * Allow user to alter steps data for a given date
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public function purge()
	{
		ES::requireLogin();

		$appId = $this->input->get('appId', 0, 'int');
		$app = ES::app($appId);
		$params = $app->getParams();

		FitBitHelper::purge($this->my->id);

		$redirect = FitBitHelper::getProfileAppUrl($this->my, $app->getAlias(), false);

		ES::info()->set(false, 'APP_FITBIT_DATA_PURGED', 'success');

		$this->app->redirect($redirect);
	}

	/**
	 * Allows user to purge fitbit logs
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public function purgeConfirmation()
	{
		ES::requireLogin();

		$appId = $this->input->get('appId', 0, 'int');

		// Load up the theme
		$theme = ES::themes();
		$theme->set('appId', $appId);
		$output	= $theme->output('apps/user/fitbit/dialogs/purge');

		return $this->ajax->resolve($output);
	}

	/**
	 * Allow user to alter steps data for a given date
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public function save()
	{
		ES::requireLogin();

		$appId = $this->input->get('appId', 0, 'int');
		$app = ES::app($appId);
		$redirect = FitBitHelper::getProfileAppUrl($this->my, $app->getAlias(), false);

		$table = FitBitHelper::table('FitbitData');


		try {
			JFactory::getDate($this->input->get('date'));
		} catch (Exception $e) {
			ES::info()->set(false, 'APP_USER_FITBIT_INVALID_DATE', 'error');
			return $this->app->redirect($redirect);
		}

		$options = [
			'user_id' => $this->my->id,
			'date' => JFactory::getDate($this->input->get('date'))->toSql()
		];

		// Ensure that the number is always positive
		$steps = $this->input->get('steps', 0, 'int');
		$steps = abs($steps);

		$table->load($options);

		$params = $app->getParams();
		$limitEditDays = (int) $params->get('data_edit_days', 0);

		$now = ES::date('midnight today');
		$newDate = ES::date($options['date']);

		$diff = ($newDate->toUnix() - $now->toUnix()) / 86400;
		$diff = floor($diff);


		// Check for past dates
		if (($diff < 0 && abs($diff) > $limitEditDays) || $diff > 0) {
			ES::info()->set(false, JText::sprintf('APP_USER_FITBIT_DATE_OUT_OF_RANGE', $limitEditDays), 'error');
			return $this->app->redirect($redirect);
		}

		$table->user_id = $this->my->id;
		$table->type = 'steps';
		$table->date = $options['date'];
		$table->value = $steps;
		$table->edited = true;
		$table->store();

		$this->app->redirect($redirect);
	}

	/**
	 * Allows user to enter the date and steps
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public function form()
	{
		ES::requireLogin();

		$appId = $this->input->get('appId', 0, 'int');

		// Load up the theme
		$theme = ES::themes();
		$theme->set('appId', $appId);
		$output	= $theme->output('apps/user/fitbit/dialogs/create');

		return $this->ajax->resolve($output);
	}

	/**
	 * Initialize the user's fitbit device
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public function initialize()
	{
		ES::requireLogin();

		$table = FitBitHelper::table('Fitbit');
		$table->load(array('user_id' => $this->my->id));

		$appId = $this->input->get('appId', 0, 'int');

		$app = ES::app($appId);
		$params = $app->getParams();

		$provider = FitBitHelper::getProvider($app->getParams());
		$provider->setToken($table->token);

		// 1d, 7d, 30d, 1w, 1m, 3m, 6m, 1y

		// Retrieve 1 years of data
		$result = $provider->request('activities/steps/date/today/6m.json');
		$activities = $result['activities-steps'];

		foreach ($activities as $key => $activity) {
			$data = FitBitHelper::table('FitbitData');
			$exists = $data->load(array(
				'user_id' => $this->my->id,
				'date' => $activity['dateTime']
			));

			if ($exists) {
				continue;
			}

			$data->user_id = $this->my->id;
			$data->type = 'steps';
			$data->date = $activity['dateTime'];
			$data->value = $activity['value'];
			$data->store();
		}

		// Generate an activity stream
		if ($params->get('activity', true)) {
			FitBitHelper::createStream($this->my);
		}

		// Once the initial initialization is done, redirect the user back to their profile app so that they can view their statistics
		$redirect = FitBitHelper::getProfileAppUrl($this->my, $app->getAlias(), false);

		$this->app->redirect($redirect);
	}

	/**
	 * Unlink a fitbit account
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public function unlink()
	{
		ES::requireLogin();

		$appId = $this->input->get('appId', 0, 'int');
		$app = ES::app($appId);
		$params = $app->getParams();

		$table = FitBitHelper::table('Fitbit');
		$table->load(array('user_id' => $this->my->id));

		$redirect = FitBitHelper::getProfileAppUrl($this->my, $app->getAlias(), false);

		$provider = FitBitHelper::getProvider($app->getParams());

		try {
			$response = $provider->revoke($table);
		} catch (Exception $e) {

		}

		FitBitHelper::unlink($this->my->id);

		ES::info()->set(false, 'APP_FITBIT_UNLINKED', 'success');

		$this->app->redirect($redirect);

	}

	/**
	 * Saves user privacy
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public function savePrivacy()
	{
		ES::requireLogin();

		$access = $this->input->get('access', 0, 'int');

		$params = $this->my->getEsParams();
		$params->set('fitbit_access', $access);

		$table = ES::table('Users');
		$table->load(array('user_id' => $this->my->id));
		$table->params = $params->toString();

		$table->store();

		return $this->ajax->resolve();
	}
}

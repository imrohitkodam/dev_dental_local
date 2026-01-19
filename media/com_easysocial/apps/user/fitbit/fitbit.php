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

ES::import('admin:/includes/apps/apps');

require_once(JPATH_ROOT . '/media/com_easysocial/apps/user/fitbit/libraries/fitbit.php');

class SocialUserAppFitbit extends SocialAppItem
{
	/**
	 * Determines if this app will be displayed in the user profile
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public function appListing($view, $userId)
	{
		$user = ES::user($userId);

		if ($this->my->id == $user->id) {
			return true;
		}

		$userParams = $user->getEsParams();

		if ($userParams->get('fitbit_access', false)) {
			return true;
		}

		return false;
	}

	/**
	 * Detect for redirects from fitbit when the user has authorized
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public function onComponentStart()
	{
		$fitbitAuthorization = $this->input->get('fitbitAuthorization', 0, 'int');

		if (!$fitbitAuthorization) {
			return;
		}

		$app = $this->getApp();
		$params = $app->getParams();

		// User cancelled the request
		$error = $this->input->get('error_description', '', 'default');

		if ($error) {
			$redirect = FitBitHelper::getProfileAppUrl($this->my, $app->getAlias(), false);
?>
<script type="text/javascript">
// Tell the parent window to redirect
window.opener.location = "<?php echo $redirect;?>";

// Close the current window
window.close();
</script>
<?php
			return;
		}

		$code = $this->input->get('code', '', 'default');


		$provider = FitBitHelper::getProvider($params->get('client_id'), $params->get('client_secret'));

		try {
			// Try to get an access token using the authorization code grant.
			$accessToken = $provider->getAccessToken($code);

			$values = $accessToken->getValues();
			$scopes = isset($values['scope']) ? $values['scope'] : '';

			if (!$scopes) {
				die('Please provide the necessary scopes');
			}

			$requiredScopes = ['profile', 'settings', 'activity'];

			$scopes = explode(' ', $scopes);

			$diff = array_diff($requiredScopes, $scopes);

			// There are missing required scopes
			if (!empty($diff)) {
			?>
				<p><?php echo JText::_('Please ensure that the following scopes are checked'); ?></p>
				<ul>
					<li>Fitbit devices and settings</li>
					<li>Activity</li>
					<li>Profile</li>
					<li>
						<a href="javascript:history.go(-1);"><?php echo JText::_('Back'); ?></a>
					</li>
				</ul>
			<?php
				exit;
			}

			// profile settings activity
			$table = FitBitHelper::table('Fitbit');
			$table->user_id = $this->my->id;
			$table->token = $accessToken->getToken();
			$table->created = JFactory::getDate()->toSql();
			$table->expires = JFactory::getDate($accessToken->getExpires())->toSql();
			$table->params = serialize($accessToken);
			$table->updated = $table->created;

			$table->store();

			$provider->setToken($table->token);

			// Determine the total devices the user has
			$totalDevices = $provider->getTotalDevices();

			// Default redirect url
			$redirect = FitBitHelper::getInitializeDeviceUrl($app->id);
?>
<script type="text/javascript">
// Tell the parent window to redirect
window.opener.location = "<?php echo $redirect;?>";

// Close the current window
window.close();
</script>
<?php
			exit;
		} catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
			// Failed to get the access token or user details.
			exit($e->getMessage());
		}
	}

	/**
	 * During cron executing, we need to retrieve user data
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public function onCronExecute()
	{
		// Release all locks if needed to
		FitBitHelper::releaseCronLocks();

		$accounts = FitBitHelper::getAccountsToSync();

		if (!$accounts) {
			return;
		}

		$params = $this->getParams();

		foreach ($accounts as $account) {
			$account->lockForCron();

			$provider = FitBitHelper::getProvider($params);

			// Ensure that the token isn't expired yet.
			$accessToken = unserialize($account->params);

			if ($accessToken->hasExpired()) {

				$newAccessToken = $provider->getRefreshToken($accessToken->getRefreshToken());

				// Purge old access token and store new access token to your data store.
				$account->token = $newAccessToken->getToken();
				$account->expires = JFactory::getDate($newAccessToken->getExpires())->toSql();

				$account->params = serialize($newAccessToken);
				$account->store();

			}


			$provider->setToken($account->token);

			// Retrieve 1 years of data
			$result = $provider->request('activities/steps/date/today/7d.json');
			$activities = $result['activities-steps'];

			// Save the last updated date
			$account->updated = JFactory::getDate()->toSql();
			$account->store();

			foreach ($activities as $key => $activity) {
				$data = FitBitHelper::table('FitbitData');
				$exists = $data->load(array(
					'user_id' => $account->user_id,
					'date' => $activity['dateTime']
				));

				if ($exists && $activity['value'] < $data->value) {
					continue;
				}

				$data->user_id = $account->user_id;
				$data->type = 'steps';
				$data->date = $activity['dateTime'];
				$data->value = $activity['value'];

				// If we are storing the data, because either this is a new record or the previous value was smaller.
				// We should ensure that edited is being reset to false in the event it has been edited before.
				$data->edited = false;
				$data->store();
			}

			$account->unlockCron();
		}
	}

	/**
	 * Generates activity stream
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public function onPrepareStream(SocialStreamItem &$item, $includePrivacy = true)
	{
		if ($item->context != 'fitbit') {
			return;
		}

		// Determine if we should display create stream
		$params = $this->getParams();

		if (!$params->get('activity', true)) {
			return;
		}

		$app = $this->getApp();
		$url = FitBitHelper::getProfileAppUrl($item->actor, $app->getAlias());

		$this->set('actor', $item->actor);
		$this->set('permalink', $url);

		$item->display = SOCIAL_STREAM_DISPLAY_MINI;
		$item->title = parent::display('streams/title');
		$item->preview = '';
	}
}

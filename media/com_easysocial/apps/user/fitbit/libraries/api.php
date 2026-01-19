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

require_once(JPATH_ROOT . '/media/com_easysocial/apps/user/fitbit/vendor/autoload.php');

use djchen\OAuth2\Client\Provider\Fitbit;

class FitBitProvider
{
	private $clientId = null;
	private $clientSecret = null;
	private $redirectUri = null;
	public $provider = null;

	public function __construct($clientId, $clientSecret, $redirectUri)
	{
		$this->provider = new Fitbit([
			'clientId' => $clientId,
			'clientSecret' => $clientSecret,
			'redirectUri' => $redirectUri
		]);
	}

	/**
	 * Retrieves the fitbit authorization url
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public function getAuthorizationUrl()
	{
		// Fetch the authorization URL from the provider; this returns the
		// urlAuthorize option and generates and applies any necessary parameters
		// (e.g. state).
		$authorizationUrl = $this->provider->getAuthorizationUrl();

		// Get the state generated for you and store it to the session.
		$_SESSION['oauth2state'] = $this->provider->getState();

		return $authorizationUrl;
	}

	/**
	 * Given the code, exchange for an access token
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public function getAccessToken($code)
	{
		$token = $this->provider->getAccessToken('authorization_code', array(
			'code' => $code
		));

		return $token;
	}

	/**
	 * Given the code, exchange for an access token
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public function getRefreshToken($refreshToken)
	{
		$token = $this->provider->getAccessToken('refresh_token', [
					'refresh_token' => $refreshToken
				]);

		return $token;
	}

	/**
	 * Determines the total number of devices the user has
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public function getTotalDevices()
	{
		$total = (int) $this->request('devices.json');

		return $total;
	}

	/**
	 * Sends a request to fitbit's api endpoint
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public function request($endPoint)
	{
		$request = $this->provider->getAuthenticatedRequest(
						Fitbit::METHOD_GET,
						Fitbit::BASE_FITBIT_API_URL . '/1/user/-/' . $endPoint,
						$this->token,
						[
							'headers' => [Fitbit::HEADER_ACCEPT_LANG => 'en_US'], [Fitbit::HEADER_ACCEPT_LOCALE => 'en_US']
						]
			// Fitbit uses the Accept-Language for setting the unit system used
			// and setting Accept-Locale will return a translated response if available.
			// https://dev.fitbit.com/docs/basics/#localization
		);

		// Make the authenticated API request and get the parsed response.
		$response = $this->provider->getParsedResponse($request);

		return $response;
	}

	/**
	 * Revokes the fitbit access token
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public function revoke($table)
	{
		$accessToken = unserialize($table->params);

		$response = $this->provider->revoke($accessToken);

		return $response;
	}

	/**
	 * Sets the token to the current object
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public function setToken($token)
	{
		$this->token = $token;
	}
}

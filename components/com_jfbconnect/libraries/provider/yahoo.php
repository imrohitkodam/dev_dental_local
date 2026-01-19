<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */

// Check to ensure this file is included in Joomla!
if (!(defined('_JEXEC') || defined('ABSPATH'))) {     die('Restricted access'); };

class JFBConnectProviderYahoo extends JFBConnectProvider
{
    function __construct()
    {
        $this->name = "Yahoo";
        $this->usernamePrefix = "yh_";
        $this->loginPrefix = "yh";

        parent::__construct();
    }
    
    public function setupAuthentication()
    {
        $options = new JRegistry();
        $options->set('authurl', 'https://api.login.yahoo.com/oauth2/request_auth');
        $options->set('tokenurl', 'https://api.login.yahoo.com/oauth2/get_token');
        $options->set('nonce', $this->generateNonce());

		$scope = 'openid'; /* Note: do not set sdpp-w, etc here as this breaks older applications and is not necessary */
		$options->set('scope', $scope);	

        $currentLanguage = JFactory::getLanguage();
        $requestparams['language'] = $currentLanguage->getTag();
        $requestparams['nonce'] = $options->get('nonce');
        $requestparams['prompt'] = 'consent';
        $options->set('requestparams', $requestparams);

        $this->client = new JFBConnectAuthenticationOauth2($options);

        $token = JFactory::getApplication()->getUserState('com_jfbconnect.' . $this->systemName . '.token', null);
        if ($token)
        {
            $token = (array)json_decode($token);
            $this->client->setToken($token);
        }
        $this->client->initialize($this);
    }

    /* getProviderUserId
    * Gets the provider User IdFacebook. This is regardless of whether they are mapped to an
    *  existing Joomla account.
    */
    function getProviderUserId()
    {
        if ($this->get('providerUserId', null) == null)
        {
            $profile = $this->profile->fetchProfile('me', 'guid');
            $id = $profile->get('guid');
            if (!empty($id))
                $this->set('providerUserId', $id);
            else
                $this->set('providerUserId', null);
        }
        return $this->get('providerUserId');
    }

	/**
	 * Method used to generate the current nonce.
	 *
	 * @return  string  The current nonce.
	 *
	 * @since   13.1
	 */
	function generateNonce()
	{
		$mt = microtime();
		$rand = mt_rand();

		// The md5s look nicer than numbers.
		return md5($mt . $rand);
	}    
}

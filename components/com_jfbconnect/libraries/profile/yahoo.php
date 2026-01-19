<?php
/**
 * @package		 JFBConnect
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license		 http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */

// Check to ensure this file is included in Joomla!
if (!(defined('_JEXEC') || defined('ABSPATH'))) {	 die('Restricted access'); };

// Probably should work toward extending the JFBConnectFacebookLibrary for this class.
// Too much intertwined in the root library right now.
class JFBConnectProfileYahoo extends JFBConnectProfile
{
	public function fetchProfile($userId, $fields = NULL)
	{
		$profile = new JFBConnectProfileDataYahoo();
		
		try
		{
			$url = 'https://social.yahooapis.com/v1/user/'.$userId.'/profile';
			$query = $this->provider->client->query($url);
			$xml = JFactory::getXML($query->body, false);
			$data = json_decode(json_encode($xml),TRUE);
			
			if (is_array($data))
			{
/*				if ($userId != "me")
				{
					$access_token = $this->provider->client->getToken();
                    //Retrieve data from OpenID Connect
                    //To retrieve email, email_verified => app uses extended profile scope sdpp-w, so will not be returned if app does not have it set
					if(isset($access_token['id_token']))
					{
                        $id_token_array = explode(".", $access_token['id_token']);
                        $payload_json = base64_decode($id_token_array[1]);
                        $payload_array = json_decode($payload_json, TRUE);
                        $openid_data = array_intersect_key(
                            $payload_array,
                            array_flip([
                                'given_name',
                                'family_name',
                                'birthdate',
                                'email',
                                'email_verified',
                                'locale'
                            ])
                        );

                        if(!empty($openid_data)) //Depending on YDN App OAuth version / scope, this data may not be returned
                        {
                            $openid_data['givenName'] = $openid_data['given_name'];
                            unset($openid_data['given_name']);

                            $openid_data['familyName'] = $openid_data['family_name'];
                            unset($openid_data['family_name']);

                            $openid_data['lang'] = $openid_data['locale'];
                            unset($openid_data['locale']);
                        }

                        $temp_data = $data;
                        $data = array_merge($temp_data, $openid_data);
					}
				}*/
				$profile->loadObject($data);
			}
		}
		catch (Exception $e)
		{
			if (JFBCFactory::config()->get('facebook_display_errors'))
			{
				JFBCFactory::log($e->getMessage());
			}
		}
		return $profile;
	}
	
	public function fetchStatus($userId)
	{
		// Return status from provider if available or NULL if not.
		return NULL;
	}


	// nullForDefault - If the avatar is the default image for the social network, return null instead
	// Prevents the default avatars from being imported
	function getAvatarUrl($userId, $nullForDefault = true, $params = null)
	{
		$nullString = $nullForDefault ? 'null' : 'notnull';
		$avatarUrl = JFBCFactory::cache()->get('yahoo.avatar.' . $nullString . '.' . $userId);
		if ($avatarUrl === false)
		{
			$profile = $this->fetchProfile($userId);
			$image = $profile->get('image', null);
			$avatarUrl = $image->imageUrl;
			if ($nullForDefault && (!$avatarUrl || strpos($avatarUrl, 'https://s.yimg.com/dg/users')))
			{
				$avatarUrl = null;
			}
			JFBCFactory::cache()->store($avatarUrl, 'yahoo.avatar.' . $nullString . '.' . $userId);
		}
		return $avatarUrl;
	}

	function getProfileUrl($userId)
	{
		$profile =  $this->fetchProfile($userId);
		return $profile->get('profileUrl', 'https://profile.yahoo.com/'.$userId);
	}

}

class JFBConnectProfileDataYahoo extends JFBConnectProfileData
{
	public function get($path, $default = NULL)
	{
		switch (strtolower($path)) {
            case 'email':
                $data = parent::get('emails.handle', $default);
                break;
            case 'name':
			case 'full_name':
				$data = parent::get('givenName', $default) . ' ' . parent::get('familyName', $default);
				break;
			case 'first_name':
			case 'given_name':
				$data = parent::get('givenName', $default);
				break;
			case 'surname':
			case 'last_name':
			case 'family_name':
				$data = parent::get('familyName', $default);
				break;
			case 'profile_url':
				$data = parent::get('profileUrl', $default);
				break;
			default:
				if ($this->exists($path))
				{
					$data = parent::get($path, $default);
				}
				else
				{
					$data = NULL;
				}
		}

		if (!is_null($data))
		{
			// format or manipulate the data as necessary here
			return $data;
		}
		else
		{		
			return $default;
		}
	}

}

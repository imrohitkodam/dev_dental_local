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

// Probably should work toward extending the JFBConnectFacebookLibrary for this class.
// Too much intertwined in the root library right now.
class JFBConnectProfileInstagram extends JFBConnectProfile
{
    public function getRequiredScope()
    {
        if (JFBCFactory::config()->get('instagram_new_api_enabled'))
            return ["user_profile"];
        else
            return [];
    }

    /* Fetch a user's profile based on the passed in array of fields
    * @return JRegistry with profile field values
    */
    public function fetchProfile($userId, $fields)
    {
        $profile = new JFBConnectProfileDataInstagram();

        if (JFBCFactory::config()->get('instagram_new_api_enabled'))
            $url = 'https://graph.instagram.com/'.$userId.'?fields=id,username'; // get the current user
        else
            $url = 'https://api.instagram.com/v1/users/self'; // get the current user
        try
        {
            $jdata = $this->provider->client->query($url);

            if ($jdata->code == 200)
            {
                $data = json_decode($jdata->body, true);
                if (JFBCFactory::config()->get('instagram_new_api_enabled'))
                    $profile->loadObject($data);
                else
                    $profile->loadObject($data['data']);
            }
        }
        catch (Exception $e)
        {
            if (JFBCFactory::config()->get('facebook_display_errors'))
                JFBCFactory::log($e->getMessage());
        }

        return $profile;
    }

    function getAvatarUrl($providerId, $nullForDefault = true, $params = null)
    {
        if (JFBCFactory::config()->get('instagram_new_api_enabled'))
            return null;
        else
        {
            $nullString = $nullForDefault ? 'null' : 'notnull';
            $avatarUrl = JFBCFactory::cache()->get('instagram.avatar.' . $nullString . '.' . $providerId);
            if ($avatarUrl === false)
            {
                $token = $this->provider->client->getToken();

                if(!empty($token))
                {
                    //instragram token includes user data
                    //get username from token
                    $user = (array) $token['user'];
                    $avatarUrl = $user['profile_picture'];

                    //http://images.ak.instagram.com/profiles/anonymousUser.jpg
                    if ($nullForDefault && (!$avatarUrl || strpos($avatarUrl, 'instagram.com/profiles/anonymousUser')))
                        $avatarUrl = null;
                    JFBCFactory::cache()->store($avatarUrl, 'instagram.avatar.' . $nullString . '.' . $providerId);
                }
            }
            return $avatarUrl;
        }
    }

    function getProfileUrl($providerUserId)
    {
        $profileUrl = JFBCFactory::cache()->get('instagram.profile.'.$providerUserId);
        $token = $this->provider->client->getToken();

        if(!empty($token))
        {
            if (JFBCFactory::config()->get('instagram_new_api_enabled'))
            {
                $profile =  $this->fetchProfile($providerUserId, array('username'));
                $username = $profile->get('username', $providerUserId);
                $profileUrl = 'https://instagram.com/' . $username;
            }
            else
            {
                //instragram token includes user data
                //get username from token
                $user = (array) $token['user'];

                $profileUrl = 'https://instagram.com/' . $user['username'];
            }

            JFBCFactory::cache()->store($profileUrl, 'instagram.profile.' . $providerUserId);
        }
        return $profileUrl;
    }

}

class JFBConnectProfileDataInstagram extends JFBConnectProfileData
{
    public function get($path, $default = "")
    {
        $data = null;
        if ($this->exists($path))
            $data = parent::get($path, $default);
        else if (!JFBCFactory::config()->get('instagram_new_api_enabled'))
        {
            if ($path == 'first_name')
            {
                $data = parent::get('full_name');
                $data = substr($data, 0, strpos($data, " "));
            }
            else if ($path == 'last_name')
            {
                $data = parent::get('full_name');
                $data = substr($data, strpos($data, " ") + 1);
            }else if ($path == "middle_name")
            {
                $data =  "";
            }
        }

        return $data;
    }

}
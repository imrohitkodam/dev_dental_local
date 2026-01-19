<?php
/**
 * @package         SourceCoast Extensions
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */
// Check to ensure this file is included in Joomla!
if (!(defined('_JEXEC') || defined('ABSPATH'))) {     die('Restricted access'); };

// Probably should work toward extending the JFBConnectFacebookLibrary for this class.
// Too much intertwined in the root library right now.
class JFBConnectProfileAzure extends JFBConnectProfile
{
    /* Fetch a user's profile based on the passed in array of fields
    * @return JRegistry with profile field values
    */
    public function fetchProfile($userId, $fields)
    {
        $profile = new JFBConnectProfileDataAzure();

        $url = 'https://graph.microsoft.com/v1.0/';
        if($userId == 'me')
            $url .= $userId;
        else
            $url .= 'users/' . $userId;

        try
        {
            $jdata = $this->provider->client->query($url);

            if ($jdata->code == 200)
            {
                $data = json_decode($jdata->body, true);
                $profile->loadObject($data);
            }
        }
        catch (Exception $e)
        {
            if (JFBCFactory::config()->get('facebook_display_errors'))
                JFBCFactory::log($e->getMessage());
        }

        return $profile;
    }

    // nullForDefault - If the avatar is the default image for the social network, return null instead
    // Prevents the default avatars from being imported
    function getAvatarUrl($providerId, $nullForDefault = true, $params = null)
    {
        $nullString = $nullForDefault ? 'null' : 'notnull';
        $avatarUrl = JFBCFactory::cache()->get('azure.avatar.' . $nullString . '.' . $providerId);
        if ($avatarUrl === false)
        {
            $avatarUrl = 'https://graph.microsoft.com/v1.0/me/photo/$value';
            JFBCFactory::cache()->store($avatarUrl, 'azure.avatar.' . $nullString . '.' . $providerId);
        }
        return $avatarUrl;
    }

    function getProfileUrl($providerId)
    {
        return null;
    }

}

class JFBConnectProfileDataAzure extends JFBConnectProfileData
{
    public function get($path, $default = "")
    {
        $data = null;
        if ($this->exists($path))
            $data = parent::get($path, $default);
        else
        {
            if ($path == 'full_name')
            {
                $data =  parent::get('displayName');
            }
            else if($path == 'first_name')
            {
                $data = parent::get('givenName');
            }
            else if($path == 'last_name')
            {
                $data = parent::get('surname');
            }
            else if ($path == 'middle_name')
            {
                $data =  "";
            }
            /*else if($path == 'birthday')
            {
                $birth_day = parent::get('birth_day');
                $birth_month = parent::get('birth_month');
                $birth_year = parent::get('birth_year');
                $data = sprintf("%s-%s-%s",$birth_year, $birth_day, $birth_month);
            }*/
            else if($path == 'email'){
                $data =  parent::get('userPrincipalName');
            }
        }

        return $data;
    }

}
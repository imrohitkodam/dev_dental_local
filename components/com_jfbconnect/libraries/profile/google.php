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
class JFBConnectProfileGoogle extends JFBConnectProfile
{
    /**
     *  Get all permissions that are required by Facebook for email, status, and/or profile, regardless
     *    of whether they're set to required in JFBConnect
     * @return string Comma separated list of FB permissions that are required
     */
    static private $requiredScope;

    public function getRequiredScope()
    {
        if (self::$requiredScope)
            return self::$requiredScope;

        self::$requiredScope = array();
        self::$requiredScope[] = 'https://www.googleapis.com/auth/userinfo.profile';
        self::$requiredScope[] = 'https://www.googleapis.com/auth/userinfo.email';

        JPluginHelper::importPlugin('socialprofiles');
        $app = JFactory::getApplication();
        $args = array('google');
        $perms = $app->triggerEvent('socialProfilesGetRequiredScope', $args);
        if ($perms)
        {
            foreach ($perms as $permArray)
                self::$requiredScope = array_merge(self::$requiredScope, $permArray);
        }

        self::$requiredScope = array_unique(self::$requiredScope);
        return self::$requiredScope;
    }

    /* Fetch a user's profile based on the passed in array of fields
    * @return JRegistry with profile field values
    */
    public function fetchProfile($socialId, $fields)
    {
        if (!is_array($fields))
            $fields = array($fields);
        $profile = new JFBConnectProfileDataGoogle();
        if (!empty($fields))
        {
            if ($this->provider->client->isAuthenticated())
            {
                //Get OAuth2 fields first
                $url = 'https://www.googleapis.com/oauth2/v2/userinfo';
                $data = $this->provider->client->query($url);
                $data = json_decode($data->body);
                if (is_object($data))
                {
                    $profile->loadArray($data);

                    if (isset($data->given_name) && $data->given_name != '')
                        $profile->set('first_name', $data->given_name);
                    else
                    {
                        // No given name, need to return something so Joomla doesn't choke
                        // In this case, we're just using their email handle (before the @) as their name. Not ideal, but it lets them register
                        $profile->set('first_name', substr($profile->get('email'), 0, strpos($profile->get('email'), '@')));
                        // If a name isn't set, then the 'name' field from Google is the email address.
                        // Can't use this as the full name or we'll have irate users.
                        $profile->set('full_name', $profile->get('first_name') . ' ' . $profile->get('last_name'));
                    }
                }
            }
        }
        return $profile;
    }


    // Created for parity with JLinked/SourceCoast library
    // nullForDefault - If the avatar is the default image for the social network, return null instead
    // Prevents the default avatars from being imported
    function getAvatarUrl($providerUserId, $nullForDefault = true, $params = null)
    {
        if (!$params)
            $params = new JRegistry();

        $width = $params->get('width', 300);
        $nullString = $nullForDefault ? 'null' : 'notnull';
        $avatarUrl = JFBCFactory::cache()->get('google.avatar.' . $nullString . '.' . $providerUserId . '.' . $width);
        if ($avatarUrl === false)
        {
            $profile = $this->fetchProfile($providerUserId, 'picture');
            $avatarUrl = $profile->get('picture');

            if($avatarUrl)
            {
                $avatarUrl = str_replace("?sz=50", "?sz=" . $width, $avatarUrl); // get a suitably large image to be resized
                JFBCFactory::cache()->store($avatarUrl, 'google.avatar.' . $nullString . '.' . $providerUserId . '.' . $width);
            }
        }

        return $avatarUrl;
    }
}

class JFBConnectProfileDataGoogle extends JFBConnectProfileData
{
    var $fieldMap;

    function get($path, $default = null)
    {
        if ($this->exists($path))
            $data = parent::get($path, $default);
        else
        {
            if ($path == 'full_name')
                $data = parent::get('name');
            else if ($path == 'first_name')
                $data = parent::get('given_name');
            else if ($path == 'last_name')
                $data = parent::get('family_name');
        }

        if (!empty($data))
        {
            // format or manipulate the data as necessary here
            return $data;
        }
        else
            return $default;

    }

}

<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */

// Check to ensure this file is included in Joomla!
if (!(defined('_JEXEC') || defined('ABSPATH')))
{
    die('Restricted access');
};

// Probably should work toward extending the JFBConnectFacebookLibrary for this class.
// Too much intertwined in the root library right now.
class JFBConnectProfileTwitter extends JFBConnectProfile
{
    /**
     *  Get all permissions that are required by Facebook for email, status, and/or profile, regardless
     *    of whether they're set to required in JFBConnect
     * @return array - list of permissions that are required
     */
    public function getRequiredScope()
    {
        return [];

    }

    private $profileData = array();

    /* Fetch a user's profile based on the passed in array of fields
    * @return JRegistry with profile field values
    */
    // Since Twitter returns a full profile each time, we just cache that result and return it if called on the same
    // page load. Need to implement actual caching of this data in the future, but this is good enough for now.
    public function fetchProfile($providerUserId, $fields)
    {
        if (isset($this->profileData[$providerUserId]))
            return $this->profileData[$providerUserId];

        if (!is_array($fields))
            $fields = array($fields);
        $profile = new JFBConnectProfileDataTwitter();
        if (!empty($fields))
        {
            $data = null;
            //Note: replace isAuthenticated call here in order to save creds for retrieving email address
            $creds = $this->provider->client->verifyCredentials();
            if (is_object($creds) && isset($creds->code) && $creds->code == 200)
            {
                $url = $this->provider->client->getOption('api.url') . 'users/show.json';
                $data = array('user_id' => $providerUserId);

                $jdata = $this->provider->client->query($url, $data, 'GET');

                $data = json_decode($jdata->body, true);

                //Add email address that's found from verify credentials
                if (isset($creds->body->email) && $creds->body->email)
                    $data['email'] = $creds->body->email;
            }

            $profile->loadArray($data);
            $this->profileData[$providerUserId] = $profile;
        }
        return $profile;
    }

    public function fetchStatus($providerUserId)
    {
        $profile = $this->fetchProfile($providerUserId, 'status');
        $socialStatus = $profile->get('status.text', '');

        //TODO: add config?
        $tweet_id = $profile->get('status.id_str', '');
        $socialStatus .= '<br /><a target="_blank" href="https://twitter.com/' . $providerUserId . '/status/' . $tweet_id . '">' . JText::_('COM_JFBCONNECT_TWITTER_STATUS_VIEW_ORIGINAL_TWEET') . '</a>';

        return $socialStatus;
    }

    // Created for parity with JLinked/SourceCoast library
    // nullForDefault - If the avatar is the default image for the social network, return null instead
    // Prevents the default avatars from being imported
    function getAvatarUrl($providerUserId, $nullForDefault = false, $params = null)
    {
        $avatarUrl = JFBCFactory::cache()->get('twitter.avatar.' . $providerUserId);
        if ($avatarUrl === false)
        {
            $profile = $this->fetchProfile($providerUserId, array('profile_image_url', 'default_profile_image'));
            if (!$profile->get('default_profile_image', true))
                $avatarUrl = $profile->get('profile_image_url', null);
            else
                $avatarUrl = null;

            JFBCFactory::cache()->store($avatarUrl, 'twitter.avatar.' . $providerUserId);
        }

        return $avatarUrl;
    }

    function getCoverPhoto($providerUserId)
    {
        $fields = array('profile_banner_url');
        $profile = $this->fetchProfile($providerUserId, $fields);
        if ($profile->get('profile_banner_url', null))
        {
            $cover = new JRegistry();

            $url = $profile->get('profile_banner_url');
            $cover->set('url', $url);
            $cover->set('offsetY', 0);
            $cover->set('offsetX', 0);
            return $cover;
        }

        return null;
    }

    function getProfileURL($providerUserId)
    {
        return 'https://twitter.com/intent/user?user_id=' . $providerUserId;
    }
}

class JFBConnectProfileDataTwitter extends JFBConnectProfileData
{
    var $fieldMap;

    function get($path, $default = null)
    {
        $data = null;
        if ($this->exists($path))
        {
            $data = parent::get($path, $default);
            if ($path == 'profile_image_url') // return a large image (if possible) instead of a teeny-tiny one
                $data = str_replace("_normal.", ".", $data); // This could probably be smarter in case someone has _normal in their original avatar...
            else if ($path == 'created_at')
            {
                $data = parent::get('created_at');
                $jdate = new JDate($data);
                $data = $jdate->format(JText::_('DATE_FORMAT_LC'));
            }
        } else // Case for custom profile values
        {
            if ($path == 'full_name')
                $data = parent::get('name');
            else if ($path == 'first_name')
            {
                $data = parent::get('name');
                $dividerPosition = strpos($data, " ");

                if ($dividerPosition === false)
                    $dividerPosition = strlen($data);
                $data = substr($data, 0, $dividerPosition);
            } else if ($path == 'last_name')
            {
                $data = parent::get('name');
                $dividerPosition = strpos($data, " ");
                if ($dividerPosition === false)
                    $data = "";
                else
                    $data = substr($data, strpos($data, " ") + 1);
            }
        }
        if (!is_null($data))
        {
            // format or manipulate the data as necessary here
            return $data;
        } else
            return $default;

    }

}
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
class JFBConnectProfileLinkedin extends JFBConnectProfile
{
    /**
     *  Get all permissions that are required by LinkedIn for email, status, and/or profile, regardless
     *    of whether they're set to required in JFBConnect
     * @return array - list of permissions that are required
     */

    public function getRequiredScope()
    {
        return ["r_liteprofile", "r_emailaddress"];
    }

    /* Fetch a user's profile based on the passed in array of fields
    * @return JRegistry with profile field values
    */
    public function fetchProfile($userId, $fields)
    {
        if (!is_array($fields))
            $fields = array($fields);

        if (in_array('first_name', $fields))
        {
            $fields[] = 'firstName';
            unset($fields[array_search('first_name', $fields)]);
        }
        if (in_array('last_name', $fields))
        {
            $fields[] = 'lastName';
            unset($fields[array_search('last_name', $fields)]);
        }
        if (in_array('full_name', $fields))
        {
            $fields[] = 'firstName';
            $fields[] = 'lastName';
            unset($fields[array_search('full_name', $fields)]);
        }
        if (in_array('email', $fields))
        {
            $fields[] = 'email-address';
            unset($fields[array_search('email', $fields)]);
        }
        if (in_array('middle_name', $fields))
            unset($fields[array_search('middle_name', $fields)]);

        $fields = array_unique($fields);

        $profile = new JFBConnectProfileDataLinkedin();
        if (!empty($fields))
        {
            $url = 'https://api.linkedin.com/v2/me';
            try
            {
                $data = $this->provider->client->query($url);
                $data = json_decode($data->body, true);
                $profile->loadObject($data);

                $localeData = $data['lastName']['preferredLocale'];
                $locale = $localeData['language'].'_'.$localeData['country'];

                $profile->set('firstName', $data['firstName']['localized'][$locale]);
                $profile->set('lastName', $data['lastName']['localized'][$locale]);
            }
            catch (Exception $e)
            {
                if (JFBCFactory::config()->get('facebook_display_errors'))
                    JFBCFactory::log($e->getMessage());
            }

            try{
                //Now try to get email address
                $emailUrl = 'https://api.linkedin.com/v2/emailAddress?q=members&projection=(elements*(handle~))';
                $jdata = $this->provider->client->query($emailUrl);
                $data = json_decode($jdata->body, true);

                $email = null;
                if (is_array($data))
                {
                    $email = $data['elements'][0]['handle~']['emailAddress'];
                    $profile->set('emailAddress', $email);
                }
            }
            catch (Exception $e)
            {
                if (JFBCFactory::config()->get('facebook_display_errors'))
                    JFBCFactory::log($e->getMessage());
            }

            if (in_array('picture-url', $fields))
            {
                $avatarUrl = $this->getAvatarUrl($this->provider->getProviderUserId());
                $profile->set('pictureUrl', $avatarUrl);
            }

        }
        return $profile;
    }

    /* Fetch a user's profile based on the passed in array of fields
    * @return JRegistry with profile field values
    */
    /* 4/1 - Removed for liteprofile
    public function fetchStatus($providerId)
    {
        $status = $this->fetchProfile($providerId, 'current-share');
        return $status->get('current-share.comment');
    }*/


    // nullForDefault - If the avatar is the default image for the social network, return null instead
    // Prevents the default avatars from being imported
    function getAvatarUrl($providerId, $nullForDefault = true, $params = null)
    {
        $avatarUrl = JFBCFactory::cache()->get('linkedin.avatar.' . $providerId);
        if ($avatarUrl === false)
        {
            $avatarUrl = 'https://api.linkedin.com/v2/me?projection=(id,profilePicture(displayImage~:playableStreams))';
            $jdata = $this->provider->client->query($avatarUrl);
            $data = json_decode($jdata->body, true);
            $avatarUrl = $data['profilePicture']['displayImage~']['elements'][0]['identifiers'][0]['identifier'];

            if ($avatarUrl == "")
                $avatarUrl = null;
            JFBCFactory::cache()->store($avatarUrl, 'linkedin.avatar.' . $providerId);
        }
        return $avatarUrl;
    }

    /*
     * 4/1 - Removed for liteprofile
    function getProfileUrl($memberId)
    {
        $profileUrl = JFBCFactory::cache()->get('linkedin.link.' . $memberId);
        if ($profileUrl === false)
        {
            $data = $this->fetchProfile($memberId, 'public-profile-url');
            $profileUrl = $data->get('publicProfileUrl');
            JFBCFactory::cache()->store($profileUrl, 'linkedin.link.' . $memberId);
        }
        return $profileUrl;
    }*/

}

class JFBConnectProfileDataLinkedin extends JFBConnectProfileData
{
    public function get($path, $default = "")
    {
        $value = $default;

        if ($path == 'full_name')
            return parent::get('firstName') . ' ' . parent::get('lastName');
        else if ($path == 'email')
            return parent::get('emailAddress');
        else if ($path == "middle_name")
            return "";

        if ($path != 'id')
        {
            if ($path == "first_name" || $path == "last_name")
                $path = str_replace('_', '-', $path);
            // Make the path into a JSON key value
            $parts = explode('-', $path);
            $parts = array_map('ucfirst', $parts);
            $path = implode('', $parts);
            $path = lcfirst($path);
        }

        if ($this->exists($path))
            $value = parent::get($path, $default);

        return $value;
    }
}
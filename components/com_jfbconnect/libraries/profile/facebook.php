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
class JFBConnectProfileFacebook extends JFBConnectProfile
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
        self::$requiredScope[] = "email";

        JPluginHelper::importPlugin('socialprofiles');
        $app = JFactory::getApplication();
        $args = array('facebook');
        $perms = $app->triggerEvent('socialProfilesGetRequiredScope', $args);
        if ($perms)
        {
            foreach ($perms as $permArray)
                self::$requiredScope = array_merge(self::$requiredScope, $permArray);
        }

        self::$requiredScope = array_unique(self::$requiredScope);

        return self::$requiredScope;
    }

    /*
     * Fetch a user's profile based on a profile plugin field-mapping
     * @return JRegistry with profile field values
     */
    public function fetchProfileFromFieldMap($fieldMap, $permissionGranted = true)
    {
        $fields = array();
        if (is_object($fieldMap))
        {
            foreach ($fieldMap as $field)
            {
                $fieldArray = explode('.', $field);
                if (!empty($fieldArray[0]))
                    $fields[] = $fieldArray[0]; // Get the root field to grab from FB
            }
        }
        $fbUserId = JFBCFactory::provider('facebook')->getProviderUserId();

        $fields = array_unique($fields);
        $profile = $this->fetchProfile($fbUserId, $fields);
        $profile->setFieldMap($fieldMap);
        return $profile;

    }

    /* Fetch a user's profile based on the passed in array of fields
    * @return JRegistry with profile field values
    */
    public function fetchProfile($fbUserId, $fields)
    {
        if (in_array('full_name', $fields))
        {
            $fields[] = 'name';
            unset($fields[array_search('full_name', $fields)]);
        }

        $profile = new JFBConnectProfileDataFacebook();
        if (!empty($fields))
        {
            $colFields = implode(",", $fields);
            $data = JFBCFactory::provider('facebook')->api('/v3.0/' . $fbUserId . '?fields=' . $colFields);
            $profile->loadObject($data);
        }
        return $profile;
    }

    public function fetchStatus($providerUserId)
    {
        //get the privacy field to check if the status is EVERYONE
        $response = JFBCFactory::provider('facebook')->api('/v2.12/me/feed/?fields=message,privacy');
        $socialStatus = '';

        if($response['data']){
            foreach($response['data'] as $k=>$data)
            {
                if($data['privacy']['value'] == 'EVERYONE')
                {
                    $socialStatus .= $data['message'];
                    $id = $data['id'];
                    break;
                }
                else
                {
                    continue;
                }
            }
        }

        if(empty($socialStatus)) return;

        //TODO: add config?
        $xplode = explode('_', $id); // $xplode[0] - user id , $xplode[1] - post id
        $socialStatus .= '<br /><a target="_blank" href="https://www.facebook.com/'.$xplode[0].'/posts/'.$xplode[1].'">'.JText::_('COM_JFBCONNECT_FACEBOOK_STATUS_VIEW_ORIGINAL_POST').'</a>';

        return $socialStatus;
    }

    // nullForDefault - If the avatar is the default image for the social network, return null instead
    // Prevents the default avatars from being imported
    function getAvatarUrl($providerUserId, $nullForDefault = false, $params = null)
    {
        if (!$params)
            $params = new JRegistry();

        $width = $params->get('width', 300);
        $height = $params->get('height', 300);

        $nullString = $nullForDefault ? 'null' : 'notnull';
        $avatarUrl = JFBCFactory::cache()->get('facebook.avatar.' . $nullString . '.' . $providerUserId . '.' . $width . 'x' . $height);
        if ($avatarUrl === false)
        {
            $avatarData = JFBCFactory::provider('facebook')->api('/' . $providerUserId . '/picture/?width=' . $width . "&height=" . $height . '&return_ssl_resources=1&redirect=false');
            if (is_array($avatarData) && array_key_exists('data', $avatarData))
            {
                if ($avatarData['data']['is_silhouette'] && $nullForDefault)
                    $avatarUrl = null;
                else
                    $avatarUrl = $avatarData['data']['url'];
            }
            JFBCFactory::cache()->store($avatarUrl, 'facebook.avatar.' . $nullString . '.' . $providerUserId . '.' . $width . 'x' . $height);
        }

        return $avatarUrl;
    }

    /*
     * 05/2018 - If user has not imported link (and granted user_link permission), link will be NULL from Facebook.
     * Disabling method for now to prevent attempting to fetch the link at every page load if it is null.
     */
    /*function getProfileUrl($fbUserId)
    {
        $profileUrl = JFBCFactory::cache()->get('facebook.link.' . '.' . $fbUserId);
        if ($profileUrl === false)
        {
            $profileData = JFBCFactory::provider('facebook')->api('/' . $fbUserId . '?fields=link');
            if (is_array($profileData) && array_key_exists('link', $profileData))
            {
                $profileUrl = $profileData['link'];
            }
            JFBCFactory::cache()->store($profileUrl, 'facebook.link.' . '.' . $fbUserId);
        }
        return $profileUrl;
    }*/

}

class JFBConnectProfileDataFacebook extends JFBConnectProfileData
{
    var $fieldMap;

    function get($path, $default = null)
    {
        if ($this->exists($path))
            $data = parent::get($path, $default);
        else if ($path == "full_name") // standardized provider value for full name
            $data = parent::get('name', $default);
        else
            return $default;

        if (!empty($data))
        {
            if (is_array($data))
            { // This is a field with multiple, comma separated values
                // Remove empty values to prevent blah, , blah as output
                unset($data['id']); // Remove id key which is useless to import
                $data = JFBConnectUtilities::r_implode(', ', $data);
            }
            // add custom field handlers here
            switch ($path)
            {
                case 'website':
                    $websites = explode("\n", $data);
                    if (count($websites) > 0)
                        $data = trim($websites[0]);
                    break;
            }
        }

        return $data;
    }
}
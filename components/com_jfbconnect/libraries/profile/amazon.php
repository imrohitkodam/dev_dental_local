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
class JFBConnectProfileAmazon extends JFBConnectProfile
{
    /* Fetch a user's profile based on the passed in array of fields
    * @return JRegistry with profile field values
    */
    public function fetchProfile($user, $fields)
    {
        $profile = new JFBConnectProfileDataAmazon();
        $url = 'https://api.amazon.com/user/profile'; // get the current user
        try
        {
            $jdata = $this->provider->client->query($url);
            $data = json_decode($jdata->body, true);

            if (is_array($data))
            {
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

    static private $requiredScope;
    public function getRequiredScope()
    {
        if (self::$requiredScope)
            return self::$requiredScope;

        self::$requiredScope = array();
        self::$requiredScope[] = "profile";

        JPluginHelper::importPlugin('socialprofiles');
        $app = JFactory::getApplication();
        $args = array('amazon');
        $perms = $app->triggerEvent('socialProfilesGetRequiredScope', $args);
        if ($perms)
        {
            foreach ($perms as $permArray)
                self::$requiredScope = array_merge(self::$requiredScope, $permArray);
        }

        return self::$requiredScope;
    }

}

class JFBConnectProfileDataAmazon extends JFBConnectProfileData
{
    function get($path, $default = null)
    {
        $data = null;
        if ($this->exists($path))
            $data = parent::get($path, $default);
        else
        {
            if ($path == 'full_name')
                $data = parent::get('name');
            else if ($path == 'first_name')
            {
                $data = parent::get('name');
                $data = substr($data, 0, strpos($data, " "));
            }
            else if ($path == 'last_name')
            {
                $data = parent::get('name');
                $data = substr($data, strpos($data, " ") + 1);
            }else if ($path == "middle_name")
            {
                $data =  "";
            }
        }

        if (!is_null($data))
        {
            // format or manipulate the data as necessary here
            return $data;
        }
        else
            return $default;
    }
}
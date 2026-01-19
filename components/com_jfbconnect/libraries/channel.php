<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */

include_once(JPATH_SITE . '/components/com_jfbconnect/libraries/stream.php');

define('AP_ACTIVITY_PENDING', 2);
define('AP_ACTIVITY_PUBLISHED', 1);
define('AP_ACTIVITY_ERROR', 0);
define('AP_TYPE_MANUAL', 0);
define('AP_TYPE_AUTOPUBLISH', 1);

abstract class JFBConnectChannel
{
    var $options;
    var $provider;
    var $name;
    var $inbound = false;
    var $outbound = false;
    var $requiredScope = array();

    var $postCharacterMax = 0;
    var $urlLength = 0;

    var $postSuccessMessage;

    public function __construct(JFBConnectProvider $provider, JRegistry $options)
    {
        $this->provider = $provider;
        $this->options = $options;
        $this->setup();
    }

    public function getStream($stream)
    {
        return null;
    }

    public function post(JRegistry $data, $type, $isPending = false, $autopost_id = null, $articleId = null, $ext = null, $layout = null)
    {
        $link = $data->get('link', '');

        try
        {
            JFBConnectUtilities::loadLanguage('com_jfbconnect', JPATH_SITE);

            $response = '';

            if(!$isPending)
            {
                $return = $this->performPost($data);
                if ($return && $return !== false)
                {
                    if($type == AP_TYPE_AUTOPUBLISH)
                    {
                        JFBCFactory::log(JText::sprintf('COM_JFBCONNECT_CHANNELS_POST_AUTOPOST_LABEL',$autopost_id), 'message', true);
                    }
                    else
                        JFBCFactory::log(JText::sprintf('COM_JFBCONNECT_CHANNELS_POST_MANUAL_LABEL',$link), 'message', true);

                    $status = AP_ACTIVITY_PUBLISHED;
                    $return = JText::_($this->postSuccessMessage);
                }
                else
                {
                    $status = AP_ACTIVITY_ERROR;
                    $response = JText::sprintf('COM_JFBCONNECT_CHANNELS_POST_FAIL_LABEL', $this->provider->getLastError());
                    $return = false;
                }
            }
            else
            {
                JFBCFactory::log(JText::sprintf('COM_JFBCONNECT_CHANNELS_POST_PENDING_LABEL',$autopost_id), 'message', true);
                $status = AP_ACTIVITY_PENDING;
                $return = JText::_('COM_JFBCONNECT_CHANNELS_PENDING_POST_MESSAGE');
            }
        }
        catch (Exception $e)
        {
            $status = AP_ACTIVITY_ERROR;
            $response = $e->getMessage();
            $return = false;
        }

        //Strip the base from the front-end url so we just have relative links. Items posting from the backend
        //will still have the front-end url so do not use JURI base or current because it will include administrator and subfolder
        //in the path
        $root = JURI::root();
        $link = str_replace($root, "", $link); //Strip off subfolder here out of path
        $juri = JURI::getInstance($link);
        $juri = JFBConnectUtilities::stripQuery($juri);
        $link = $juri->toString(array('path','query'));

        $this->savePostActivity($type, $link, $this->provider->name, $this->name, $status, $response, $autopost_id, $articleId, $ext, $layout);

        return $return;
    }

    public function performPost(JRegistry $data)
    {
        return false;
    }

    public function savePostActivity($type, $url, $provider, $channel_type, $status, $response, $autopost_id = null, $articleId = null, $ext = null, $layout = null)
    {
        $db = JFactory::getDbo();
        $filter = JFilterInput::getInstance();
        $columns = array('autopost_id', 'url', 'provider', 'channel_type', 'ext', 'layout', 'item_id', 'status', 'response', 'created','type');
        $query = $db->getQuery(true);
        $query->insert($db->qn("#__jfbconnect_autopost_activity"))
            ->columns($db->qn($columns));

        $autopost_id = $filter->clean($autopost_id, 'INT');
        $item_id = $filter->clean($articleId, 'INT');
        if (is_int($autopost_id) && is_int($item_id))
        {
            $query->clear('values');
            $query->values($autopost_id . ',' . $db->q($url) . ',' . $db->q($provider) . ','
                . $db->q($channel_type) . ',' . $db->q($ext) . ',' . $db->q($layout) . ','
                . $item_id . ',' . $status . ',' . $db->q($response) .',NOW(),'.$type);
            $db->setQuery($query);
            $db->query();
        }
    }

    public function canPostToNetwork()
    {
        return true;
    }

    public function canPublish($data)
    {
        return true;
    }

    // manipulate the input data in some way (retrieve an access token, etc)
    public function onBeforeSave($data)
    {
        if(!$this->canPublish($data))
        {
            $data['published'] = "0";
            JFBCFactory::log(JText::_('COM_JFBCONNECT_CHANNEL_CANNOT_PUBLISH_LABEL'), 'warning');
        }

        return $data;
    }

    // Called whenever a channel is saved to check the settings and make any required changes
    public function onAfterSave($newData, $oldData)
    {
        if (count($this->requiredScope) > 0)
        {
            if (isset($oldData['attribs']) && isset($oldData['attribs']['user_id']) && $oldData['attribs']['user_id'])
            {
                $userModel = JFBConnectModelUserMap::getUser($oldData['attribs']['user_id'], $this->provider->systemName);
                $userModel->removeAllScope('channel', $oldData['id']);
            }
            // Save the manage_pages permission to the required_scope settings for the selected user
            if (isset($newData['attribs']) && isset($newData['attribs']['user_id']) && $newData['attribs']['user_id'])
            {
                $userModel = JFBConnectModelUserMap::getUser($newData['attribs']['user_id'], $this->provider->systemName);
                foreach ($this->requiredScope as $scope)
                    $userModel->addScope($scope, 'channel', $newData['id']);
            }
        }
        return true;
    }

    /***
     * Get a list of scope requested from users for this object and details about why
     * @return array
     */
    public function getScopeUsed()
    {
        return $this->requiredScope;
    }
}
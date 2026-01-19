<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2015 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v7.0.3
 * @build-date      2016/09/18
 */

// Check to ensure this file is included in Joomla!
if (!(defined('_JEXEC') || defined('ABSPATH'))) {     die('Restricted access'); };

class JFBConnectProviderVkWidgetSubscribe extends JFBConnectWidget
{
    var $name = "Subscribe";
    var $systemName = "subscribe";
    var $className = "sc_vksubscribe";
    var $tagName = "scvksubscribe";
    var $examples = array (
        '{SCVkSubscribe}',
        '{SCVkSubscribe type=group link=http://vk.com/jfbconnect width=350 mode=0}'
    );

    /**
     * @return string
     */
    protected function getTagHtml()
    {
        $id = '';
        $type = $this->getParamValue('type');

        //also allow to directly enter screen name
        $screen_name = $this->getParamValue('link');

        //check if a link with screen_name...ex. http://vk.com/jfbconnect
        if(filter_var($screen_name, FILTER_VALIDATE_URL))
        {
            if (strpos($screen_name, 'vk.com/') !== false)
            {
                $xplode = explode('vk.com/', $screen_name);
                $screen_name = $xplode[1];
            }
        }

        if($type == 'user')
            $url = 'https://api.vk.com/method/users.get?user_ids=' . $screen_name;
        else
            $url = 'https://api.vk.com/method/groups.getById?group_id=' . $screen_name;

        try
        {
            $data = $this->provider->client->query($url);
            if ($data->code == 200)
            {
                $data = json_decode($data->body, true);

                //check if we have the response or error
                if(isset($data['response'][0]))
                {
                    $id = $type == 'user' ? $data['response'][0]['uid'] : '-' . $data['response'][0]['gid']; //if page or group, add a - (negative) prefix
                }
                elseif(isset($data['error']))
                {
                    if (JFBCFactory::config()->get('facebook_display_errors'))
                        JFBCFactory::log($data['error']['error_msg'], 'error');
                }
            }
        }
        catch (Exception $e)
        {
            if (JFBCFactory::config()->get('facebook_display_errors'))
                JFBCFactory::log($e->getMessage(), 'error');
        }

        $options = array();
        $options[] = 'width: ' . $this->getParamValue('width');
        $options[] = 'mode: ' . $this->getParamValue('mode');
        $options[] = 'soft: ' . $this->getParamValue('display');
        $option_txt = implode(',', $options);

        $tag = '<div id="vk_subscribe"></div>';
        $tag .= '<script type="text/javascript">';
        $tag .= 'VK.Widgets.Subscribe(\'vk_subscribe\', {'.$option_txt.'}, '.$id.');';
        $tag .= '</script>';
        return $tag;
    }
}
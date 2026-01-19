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

class JFBConnectProviderVkWidgetCommunity extends JFBConnectWidget
{
    var $name = "Community";
    var $systemName = "community";
    var $className = "sc_vkcommunity";
    var $tagName = "scvkcommunity";
    var $examples = array (
        '{SCVkCommunity}',
        '{SCVkCommunity link=http://vk.com/jfbconnect width=350 layout=3}'
    );

    /**
     * @return string
     */
    protected function getTagHtml()
    {
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

        $gid = '';
        $tag = '';
        //check if start is public/club which means is default screenname
        $url = 'https://api.vk.com/method/groups.getById?group_id=' . $screen_name;

        try
        {
            $data = $this->provider->client->query($url);
            if ($data->code == 200)
            {
                $data = json_decode($data->body, true);

                if(!isset($data['response'][0]['gid']) && isset($data['error']))
                {
                    //starting public/club with xxxxxxxxx numbers is the default screenname. Ex. public123456789
                    preg_match("/(?<=^public|club)\d.*\d$/", $screen_name, $output_array);
                    if($output_array)
                    {
                        $gid = $output_array[0];
                    }
                }
                else
                {
                    $gid = $data['response'][0]['gid'];
                }
            }
        }
        catch (Exception $e)
        {
            if (JFBCFactory::config()->get('facebook_display_errors'))
                JFBCFactory::log($e->getMessage());
        }

        if($gid)
        {
            $options = array();
            $options[] = 'mode: ' . $this->getParamValue('layout');
            $options[] = 'wide: ' . $this->getParamValue('wide');
            $options[] = 'width: "' . $this->getParamValue('width').'"';
            $options[] = 'height: "' . $this->getParamValue('height').'"';
            $options[] = 'color1: \'' . str_replace('#', '', $this->getParamValue('background_color')).'\'';
            $options[] = 'color2: \'' . str_replace('#', '', $this->getParamValue('text_color')).'\'';
            $options[] = 'color3: \'' . str_replace('#', '', $this->getParamValue('button_color')).'\'';
            $option_txt = implode(',', $options);

            $tag = '<div id="vk_groups"></div>';
            $tag .= '<script type="text/javascript">';
            $tag .= 'VK.Widgets.Group(\'vk_groups\', {'.$option_txt.'}, '.$gid.');';
            $tag .= '</script>';
        }
        else
        {
            JFBCFactory::log(JText::_('COM_JFBCONNECT_WIDGET_VK_COMMUNITY_MSG_GID_ERROR'), 'notice');
        }

        return $tag;
    }
}
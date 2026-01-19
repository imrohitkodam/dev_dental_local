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

class JFBConnectProviderVkWidgetPoll extends JFBConnectWidget
{
    var $name = "Poll";
    var $systemName = "poll";
    var $className = "sc_vkpoll";
    var $tagName = "scvkpoll";
    var $examples = array (
        '{SCVkPoll}',
        '{SCVkPoll poll_id=4_6df94627bb69225689 width=500}'
    );

    protected function getTagHtml()
    {
        $poll_id = $this->getParamValue('poll_id');
        $options = array();
        $options[] = 'width: "' . $this->getParamValue('width').'"';

        $url = $this->getParamValue('url');
        if($url)
            $options[] = 'pageUrl: "' . $url . '"';

        $option_txt = implode(',', $options);

        $tag = '<div id="vk_poll"></div>';
        $tag .= '<script type="text/javascript">';
        $tag .= 'VK.Widgets.Poll(\'vk_poll\', {'.$option_txt.'}, \''.$poll_id.'\');';
        $tag .= '</script>';
        return $tag;
    }
}

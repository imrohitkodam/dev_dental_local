<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2015 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v7.0.3
 * @build-date      2016/09/25
 */

// Check to ensure this file is included in Joomla!
if (!(defined('_JEXEC') || defined('ABSPATH'))) {     die('Restricted access'); };

class JFBConnectProviderVkWidgetShare extends JFBConnectWidget
{
    var $name = "Share";
    var $systemName = "share";
    var $className = "sc_vkshare";
    var $tagName = "scvkshare";
    var $examples = array (
        '{SCVkShare}',
        '{SCVkShare type=button text=Share}'
    );

    protected function getTagHtml()
    {
        $this->needsJavascript = false; //we dont need the vk.com/js/api/openapi.js here

        //add share js in the head
        JFactory::getDocument()->addScript("http://vk.com/js/api/share.js?90");

        $type = $this->getParamValue('type');
        $text = $this->getParamValue('text');
        $url = $this->getParamValue('url');

        $link = 'false';
        if(!empty($url))
            $link = '{url: "'.$url.'"}';

        $tag =
            <<<EOT
            <script type="text/javascript"><!--
document.write(VK.Share.button({$link},{type: '{$type}', text: '{$text}'}));
--></script>
EOT;

        return $tag;
    }
}

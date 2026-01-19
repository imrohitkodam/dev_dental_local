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

class JFBConnectProviderVkWidgetPost extends JFBConnectWidget
{
    var $name = "Post";
    var $systemName = "post";
    var $className = "sc_vkpost";
    var $tagName = "scvkpost";
    var $examples = array (
        '{SCVkPost}',
        '{SCVkPost width=650}'
    );

    protected function getTagHtml()
    {
        $tag = $this->getParamValue('code');
        if(empty($tag)) return $tag;

        //we remove the js insert of vk.com/js/api/openapi.js since we already have it in the VK provider class
        $tag = preg_replace('/\(function(.+)\bfunction/', '(function', $tag);

        //we set the width from the params
        $width = intval($this->getParamValue('width'));
        $width_txt = '{width: '.$width.'}';
        $tag = preg_replace('/{width:(.+)\b}/', $width_txt, $tag);

        return $tag;
    }
}

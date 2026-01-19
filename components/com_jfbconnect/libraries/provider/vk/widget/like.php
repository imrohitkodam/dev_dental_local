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

class JFBConnectProviderVkWidgetLike extends JFBConnectWidget
{
    var $name = "Like Button";
    var $systemName = "like";
    var $className = "sc_vklike";
    var $tagName = "scvklike";
    var $examples = array (
        '{SCVkLike}',
        '{SCVkLike layout=full height=22 width=350 verb=0}'
    );

    protected function getTagHtml()
    {
        $options = array();
        $options[] = 'type: "' . $this->getParamValue('layout').'"';
        $options[] = 'height: ' . $this->getParamValue('height');
        $options[] = 'width: ' . $this->getParamValue('width');
        $options[] = 'verb: ' . $this->getParamValue('verb');

        if($this->getParamValue('pagetitle'))
            $options[] = 'pageTitle: "' . $this->getParamValue('pagetitle').'"';

        if($this->getParamValue('url'))
            $options[] = 'pageUrl: "' . $this->getParamValue('url').'"';

        if($this->getParamValue('pageimage'))
            $options[] = 'pageImage: "' . $this->getParamValue('pageimage').'"';

        $option_txt = implode(',', $options);

        $tag = '<div id="vk_like"></div>';
        $tag .= '<script type="text/javascript">';
        $tag .= 'VK.Widgets.Like(\'vk_like\', {'.$option_txt.'});';
        $tag .= '</script>';
        return $tag;
    }
}

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

class JFBConnectProviderVkWidgetRecommendations extends JFBConnectWidget
{
    var $name = "Recommendations";
    var $systemName = "recommendations";
    var $className = "sc_vkrecommendations";
    var $tagName = "scvkrecommendations";
    var $examples = array (
        '{SCVkRecommendations}',
        '{SCVkRecommendations limit=5 period=week verb=0 sort=friend_likes}'
    );

    protected function getTagHtml()
    {
        $options = array();
        $options[] = 'limit: ' . $this->getParamValue('limit');
        $options[] = 'max: ' . $this->getParamValue('max');
        $options[] = 'period: "' . $this->getParamValue('period') . '"';
        $options[] = 'verb: ' . $this->getParamValue('verb');
        $options[] = 'sort: "' . $this->getParamValue('sort') . '"';
        $options[] = 'target: "' . $this->getParamValue('target') . '"';

        $option_txt = implode(',', $options);

        $tag = '<div id="vk_recommend"></div>';
        $tag .= '<script type="text/javascript">';
        $tag .= 'VK.Widgets.Recommended(\'vk_recommend\', {'.$option_txt.'});';
        $tag .= '</script>';
        return $tag;
    }
}

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

class JFBConnectProviderFacebookWidgetShare extends JFBConnectProviderFacebookWidget
{
    var $name = "Share";
    var $systemName = "share";
    var $className = "jfbcshare jfbcsharedialog";
    var $tagName = array("jfbcshare","scfacebookshare");
    var $examples = array (
        '{SCFacebookShare}',
        '{SCFacebookShare href=http://www.sourcecoast.com layout=button size=small}'
    );

    protected function getTagHtml()
    {
        $tag = '<div class="fb-share-button"';
        $tag .= $this->getField('href', 'url', null, JFBConnectUtilities::getStrippedUrl(), 'data-href');
        $tag .= $this->getField('layout', null, null, '', 'data-type');
        $tag .= $this->getField('size', null, null, '', 'data-size');
        $tag .= $this->getField('mobile_iframe', null, 'boolean', 'false', 'data-mobile-iframe');
        $tag .= '></div>';
        return $tag;
    }
}

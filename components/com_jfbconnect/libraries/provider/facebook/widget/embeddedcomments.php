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

class JFBConnectProviderFacebookWidgetEmbeddedcomments extends JFBConnectProviderFacebookWidget
{
    var $name = "Embedded Comments";
    var $systemName = "embeddedcomments";
    var $className = "sc_facebookembeddedcomments";
    var $tagName = array("jfbcembeddedcomments","scfacebookembeddedcomments");
    var $examples = array (
        '{SCFacebookEmbeddedComments}',
        '{SCFacebookEmbeddedComments href=https://www.facebook.com/FacebookDevelopers/posts/10151471074398553 width=560 include_parent=false}'
    );

    protected function getTagHtml()
    {
        $tag = '';
        $href = $this->getField('href', 'url', null, '', 'data-href');

        if($href)
        {
            $tag = '<div class="fb-comment-embed"' . $href;
            $tag .= $this->getField('width', null, null, '', 'data-width');
            $tag .= $this->getField('include_parent', null, 'boolean', 'false', 'data-include-parent');
            $tag .= '></div>';
        }

        return $tag;
    }
}

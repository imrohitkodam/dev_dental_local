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

class JFBConnectProviderTwitterWidgetOembed extends JFBConnectProviderWidgetOembed
{
    public $examples = array (
        '{SCTwitterOEmbed url=https://twitter.com/BarackObama/statuses/266031293945503744 maxwidth=550}',
        '{SCTwitterOEmbed url=266031293945503744 maxwidth=550}'
    );

    function __construct($provider, $fields)
    {
        parent::__construct($provider, $fields, 'scTwitterOembedTag');

        $this->name = "Embedded Tweets";
        $this->className = 'sc_twitteroembed';
        $this->tagName = 'sctwitteroembed';

        $options = new JRegistry();
        $options->set('oembed_url', 'https://api.twitter.com/1/statuses/oembed.json');

        $url = $this->getParamValueEx('url', null, null, '');
        if(!is_numeric($url) && !empty($url))
            $options->set('url', $url);

        $options->set('maxwidth', $this->getParamValueEx('maxwidth', null, null, '550'));

        $headers = array();
        $headers['Content-Type'] = 'application/json';
        $options->set('headers', $headers);

        $this->options = $options;
    }

    protected function buildExtraQuery()
    {
        $url = '';

        $id = $this->getParamValueEx('url', null, null, '');
        if(is_numeric($id) && !empty($id))
            $url .= '&id='.urlencode($id);

        $related = $this->getParamValueEx('related', null, null, '');
        $link_color = $this->getParamValueEx('link_color', null, null, '');
        $widget_type = $this->getParamValueEx('widget_type', null, null, '');

        $url .= '&hide_media='.urlencode($this->getParamValueEx('hide_media', null, null, 'false'));
        $url .= '&hide_thread='.urlencode($this->getParamValueEx('hide_thread', null, null, 'false'));
        $url .= '&align='.urlencode($this->getParamValueEx('align', null, null, 'none'));
        $url .= '&theme='.urlencode($this->getParamValueEx('theme', null, null, 'light'));
        $url .= '&dnt='.urlencode($this->getParamValueEx('dnt', null, null, 'false'));

        if(!empty($related))
            $url .= '&related='.urlencode($related);
        if(!empty($link_color))
            $url .= '&link_color='.urlencode($link_color);
        if(!empty($widget_type))
            $url .= '&widget_type='.urlencode($widget_type);

        $url .= '&omit_script=true';
        return $url;
    }
}

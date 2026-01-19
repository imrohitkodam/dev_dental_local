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

class JFBConnectProviderTwitterWidgetMoment extends JFBConnectProviderWidgetOembed
{
    public $examples = array (
        '{SCTwitterMoment url=https://twitter.com/i/moments/650667182356082688 maxwidth=550}'
    );

    function __construct($provider, $fields)
    {
        parent::__construct($provider, $fields, 'scTwitterMomentTag');

        $this->name = "Moment";
        $this->className = 'sc_twittermoment';
        $this->tagName = 'sctwittermoment';
        $this->systemName = "moment";

        $options = new JRegistry();
        $options->set('oembed_url', 'https://publish.twitter.com/oembed');

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

        $limit = $this->getParamValueEx('limit', null, null, '');
        if(!empty($limit))
            $url .= '&limit='.urlencode($limit);

        $url .= '&dnt='.urlencode($this->getParamValueEx('dnt', null, null, 'false'));
        $url .= '&omit_script=true';
        return $url;
    }
}

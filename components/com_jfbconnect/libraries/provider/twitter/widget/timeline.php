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

class JFBConnectProviderTwitterWidgetTimeline extends JFBConnectWidget
{
    var $name = "Timeline";
    var $systemName = "timeline";
    var $className = "sc_twittertimeline";
    var $tagName = "sctwittertimeline";
    var $examples = array (
        '{SCTwitterTimeline}',
        '{SCTwitterTimeline types=user username=sourcecoast tweet-limit=5}'
    );

    protected function getTagHtml()
    {
        $username = $this->getParamValueEx('username', null, null, 'sourcecoast');
        $options = array();
        $chromes = array();

        $show_header = $this->getParamValueEx('show_header', null, null, '1');
        if(!$show_header) $chromes[] = 'noheader';

        $show_footer = $this->getParamValueEx('show_footer', null, null, '1');
        if(!$show_footer) $chromes[] = 'nofooter';

        $show_borders = $this->getParamValueEx('show_borders', null, null, '1');
        if(!$show_borders) $chromes[] = 'noborders';

        $show_scrollbar = $this->getParamValueEx('show_scrollbar', null, null, '1');
        if(!$show_scrollbar) $chromes[] = 'noscrollbar';

        $transparent = $this->getParamValueEx('transparent', null, null, '0');
        if($transparent) $chromes[] = 'transparent';

        $options[] = 'data-chrome="'.implode(" ", $chromes).'"';

        $width = $this->getParamValueEx('width', null, null, 'auto');
        if($width)
            $options[] = 'data-width="'.$width.'"';

        $height = $this->getParamValueEx('height', null, null, '600');
        if($height)
            $options[] = 'data-height="'.$height.'"';

        $show_replies = $this->getParamValueEx('show_replies', null, null, '0');
        if($show_replies)
            $options[] = 'data-show-replies="true"';

        $theme = $this->getParamValueEx('theme', null, null, 'light');
        if($theme == 'dark')
            $options[] = 'data-theme="dark"';

        $tweet_limit = $this->getParamValueEx('tweet_limit', null, null, '5');
        if($tweet_limit < 1 || $tweet_limit > 20) $tweet_limit = 5; //set to default if not within the range 1-20
        $options[] = 'data-tweet-limit="'.$tweet_limit.'"';

        $link_color = $this->getParamValueEx('link_color', null, null, '#2b7bb9');
        $options[] = 'data-link-color="'.$link_color.'"';

        $border_color = $this->getParamValueEx('border_color', null, null, '');
        if($border_color)
            $options[] = 'data-border-color="'.$border_color.'"';

        $type = $this->getParamValueEx('types', null, null, 'user');
        switch($type)
        {
            case 'user':
                $href = 'href="https://twitter.com/'.$username.'"';
                $title = 'Tweets by @'.$username;
                break;
            case 'likes':
                $href = 'href="https://twitter.com/'.$username.'/likes"';
                $title = 'Tweets Liked by @'.$username;
                break;
            case 'list':
                $list_slug = $this->getParamValueEx('list_name', null, null, '');
                $href = 'href="https://twitter.com/'.$username.'/lists/'.$list_slug.'"';
                $title = 'A Twitter List by '.$username;
                break;
            case 'collection':
                $collection_id = $this->getParamValueEx('collection_id', null, null, '');
                $href = 'href="https://twitter.com/'.$username.'/timelines/'.$collection_id.'"';
                $title = 'Curated tweets by '.$username;
                break;
            case 'search':
                $widget_id = $this->getParamValueEx('widget_id', null, null, '');
                // we set the options when creating the widget at https://twitter.com/settings/widgets
                // so we set the options as empty here as we only need the widget id
                $options = array();
                $options[] = 'data-widget-id="'.$widget_id.'"';
                $href = '';
                $title = 'Tweets';
                break;
        }

        $tag = '<a class="twitter-timeline" '.$href.' ';
        $tag .= implode(" ", $options);
        $tag .= '>'.$title.'</a>';

        return $tag;
    }
}

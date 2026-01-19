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

class JFBConnectProviderPinterestWidgetShare extends JFBConnectProviderPinterestWidget
{
    var $name = "Share";
    var $systemName = "share";
    var $className = "pinterest sc_pinterest";
    var $tagName = "scpinterestshare";
    var $examples = array (
        '{SCPinterestShare href=http://www.sourcecoast.com image=http://www.sourcecoast.com/templates/sourcecoast/images/logo.png pin_count=above desc=Learn more about JFBConnect}'
    );

    public function getTagHtml()
    {
        if($this->fields->exists('image') && $this->getParamValue('image') != "")
        {
            self::$needsJavascript = true;

            $url = $this->getParamValueEx('href', 'url', null, JFBConnectUtilities::getStrippedUrl());
            $layout = $this->getParamValue('layout');

            $custom = $this->getParamValueEx('custom_image', '', null, '');

            $url = rawurlencode($url);
            $image = rawurlencode($this->getParamValue('image'));
            $desc = rawurlencode($this->getParamValue('desc'));

            $tagButtonText = '<a href="//pinterest.com/pin/create/button/?url=' . $url;
            if ($image)
                $tagButtonText .= '&media=' . $image;
            if ($desc)
                $tagButtonText .= '&description=' . $desc;
            $tagButtonText .= '"';

            if($custom != '')
            {
                $pinImageSrc = $custom;
                $tagButtonText .= ' data-pin-custom="true"';
            }
            else //Using a custom image overrides other image styles
            {
                $size = $this->getParamValue('size');
                if($layout)
                {
                    $tagButtonText .= JFBCFactory::easytags()->getShareButtonLayout('pinterest', $layout, '"');
                }
                else
                {
                    $tagButtonText .= $this->getField('pin_count', 'data-pin-config', null, 'none', 'data-pin-count');
                }

                $tall = $this->getParamValueEx('tall', '', 'boolean', 'false');
                $color = $this->getParamValue('color');
                $round = $this->getParamValueEx('round', '', 'boolean', 'false');
                $pinImageSrc = $this->getPinImage($tall, $color, $round);

                $tagButtonText .= $this->getField('color', null, null, '', 'data-pin-color');
                if($round == 'true')
                    $tagButtonText .= $this->getField('round', null, 'boolean', 'false', 'data-pin-round');
                if($tall == 'true' || $size == 'large')
                    $tagButtonText .= $this->getField('tall', null, 'boolean', 'false', 'data-pin-tall');

            }

            $tagButtonText .= ' data-pin-do="buttonPin" ><img src="'.$pinImageSrc.'" alt="Share on Pinterest"/></a>';
        } else
            $tagButtonText = '';
        return $tagButtonText;
    }

    private function getPinImage($isTall, $color, $isRound)
    {
        if($isRound)
        {
            if($isTall) //Large Round
                $image = 'pinit_fg_en_round_red_32';
            else //Regular Round
                $image = 'pinit_fg_en_round_red_16';
        }
        else
        {
            if($isTall)
                $num = '28';
            else
                $num = '20';

            //Gray Regular Rect - pinit_fg_en_rect_gray_20
            //Gray Large Rect - pinit_fg_en_rect_gray_28
            //White Regular Rect - pinit_fg_en_rect_white_20
            //White Regular Rect - pinit_fg_en_rect_white_28
            //Red Regular Rect - pinit_fg_en_rect_red_20
            //Red Large Rect - pinit_fg_en_rect_red_28
            $image = 'pinit_fg_en_rect_'.$color.'_'.$num;
        }

        return '//assets.pinterest.com/images/pidgets/'.$image.'.png';
    }
}

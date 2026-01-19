<?php
/**
 * @package         Conditions
 * @version         25.11.2254
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

namespace RegularLabs\Component\Conditions\Administrator\Condition\Other;

defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri as JUri;
use RegularLabs\Component\Conditions\Administrator\Condition\Condition;
use RegularLabs\Library\RegEx;
use RegularLabs\Library\StringHelper;

class Url extends Condition
{
    public function pass(): bool
    {
        $regex          = $this->params->regex ?? false;
        $case_sensitive = $this->params->case_sensitive ?? false;

        if ( ! is_array($this->selection))
        {
            $this->selection = explode("\n", $this->selection);
        }

        if (count($this->selection) == 1)
        {
            $this->selection = explode("\n", $this->selection[0]);
        }

        $url = JUri::getInstance();
        $url = $url->toString();

        $urls = [
            StringHelper::html_entity_decoder(urldecode($url)),
            urldecode($url),
            StringHelper::html_entity_decoder($url),
            $url,
        ];
        $urls = array_unique($urls);

        foreach ($urls as $url)
        {
            if ( ! $case_sensitive)
            {
                $url = StringHelper::strtolower($url);
            }

            foreach ($this->selection as $selection)
            {
                $selection = trim($selection);

                if ($selection == '')
                {
                    continue;
                }

                if ($regex)
                {
                    $url_part = str_replace(['#', '&amp;'], ['\#', '(&amp;|&)'], $selection);

                    if (@RegEx::match($url_part, $url, $match, $case_sensitive ? 's' : 'si'))
                    {
                        return true;
                    }

                    continue;
                }

                if ( ! $case_sensitive)
                {
                    $selection = StringHelper::strtolower($selection);
                }

                if (str_contains($url, $selection))
                {
                    return true;
                }
            }
        }

        return false;
    }
}

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

namespace RegularLabs\Component\Conditions\Administrator\Condition\Content\Article;

defined('_JEXEC') or die;

use RegularLabs\Component\Conditions\Administrator\Condition\Content\Content;
use RegularLabs\Library\ArrayHelper as RL_Array;
use RegularLabs\Library\RegEx as RL_RegEx;

class ContentKeyword extends Content
{
    public function pass(): bool
    {
        if ( ! $this->isArticle())
        {
            return false;
        }

        if (empty($this->selection))
        {
            return false;
        }

        $item = $this->getArticle();

        $texts = [];

        $text_fields = ['title', 'introtext', 'fulltext'];

        foreach ($text_fields as $field)
        {
            if ( ! isset($item->{$field}))
            {
                return false;
            }

            $texts[] = $item->{$field};
        }

        if (empty($texts))
        {
            return false;
        }

        $texts         = RL_Array::implode($texts, ' ');
        $keywords      = RL_RegEx::quote($this->makeArray($this->selection));
        $case_sensitve = $this->params->case_sensitve ?? false;

        $options = $case_sensitve ? 'si' : 's';

        return RL_RegEx::match('\b' . $keywords . '\b', $texts, $match, $options);
    }
}

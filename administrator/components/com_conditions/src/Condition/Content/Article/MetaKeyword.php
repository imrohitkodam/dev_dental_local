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

class MetaKeyword extends Content
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

        $keywords = $item->metakey ?: '';

        if (empty($keywords))
        {
            return false;
        }

        $case_sensitve   = $this->params->case_sensitve ?? false;
        $this->selection = RL_Array::implode($this->selection, ',');

        if ( ! $case_sensitve)
        {
            $this->selection = strtolower($this->selection);
            $keywords        = strtolower($keywords);
        }

        $this->selection = RL_Array::toArray($this->selection);
        $keywords        = RL_Array::toArray($keywords);

        return RL_Array::find($this->selection, $keywords);
    }
}

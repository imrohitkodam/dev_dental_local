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
use RegularLabs\Component\Conditions\Administrator\Helper\Date as DateHelper;

class Date extends Content
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

        $item  = $this->getArticle();
        $field = $this->selection;

        if ( ! isset($item->{$field}))
        {
            return false;
        }

        $date = DateHelper::getString($item->{$field});

        return DateHelper::pass($this->params, $date);
    }
}

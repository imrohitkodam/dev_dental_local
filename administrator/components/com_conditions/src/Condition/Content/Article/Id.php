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
use RegularLabs\Component\Conditions\Administrator\Condition\HasArraySelection;

class Id extends Content
{
    use HasArraySelection;

    public function pass(): bool
    {
        if ( ! $this->isArticle())
        {
            return false;
        }

        return $this->passSimple($this->request->id);
    }
}

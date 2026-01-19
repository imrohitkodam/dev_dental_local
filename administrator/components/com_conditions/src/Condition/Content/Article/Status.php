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

use Joomla\CMS\Factory as JFactory;
use RegularLabs\Component\Conditions\Administrator\Condition\Content\Content;
use RegularLabs\Component\Conditions\Administrator\Condition\HasArraySelection;

class Status extends Content
{
    use HasArraySelection;

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

        $publish_state = $item->state;

        $now = JFactory::getDate()->format('Y-m-d H:i:s');

        if ($publish_state === 1)
        {
            $isUnpublished = $item->publish_up > $now
                || ( ! is_null($item->publish_down) && $item->publish_down < $now);

            $publish_state = $isUnpublished ? 0 : 1;
        }

        $states = [
            0  => 'unpublished',
            1  => 'published',
            2  => 'archived',
            -2 => 'trashed',
        ];

        return $this->passSimple([$publish_state, $states[$publish_state] ?? $publish_state]);
    }
}

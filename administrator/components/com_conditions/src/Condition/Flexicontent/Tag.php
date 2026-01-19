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

namespace RegularLabs\Component\Conditions\Administrator\Condition\Flexicontent;

defined('_JEXEC') or die;

use RegularLabs\Component\Conditions\Administrator\Condition\HasArraySelection;
use RegularLabs\Library\Input as RL_Input;

class Tag extends Flexicontent
{
    use HasArraySelection;

    public function pass(): bool
    {
        if ($this->request->option !== 'com_flexicontent')
        {
            return false;
        }

        $page_types = $this->params->page_types ?? [];

        if (
            (in_array('tags', $page_types) && $this->request->view == 'tags')
            || (in_array('items', $page_types) && in_array($this->request->view, ['item', 'items']))
        )
        {
            return false;
        }

        if (in_array('tags', $page_types) && $this->request->view == 'tags')
        {
            $query = $this->db->getQuery(true)
                ->select('t.name')
                ->from('#__flexicontent_tags AS t')
                ->where('t.id = ' . (int) trim(RL_Input::getInt('id', 0)))
                ->where('t.published = 1');
            $this->db->setQuery($query);
            $tag  = $this->db->loadResult();
            $tags = [$tag];

            return $this->passSimple($tags, null, true);
        }

        $query = $this->db->getQuery(true)
            ->select('t.name')
            ->from('#__flexicontent_tags_item_relations AS x')
            ->join('LEFT', '#__flexicontent_tags AS t ON t.id = x.tid')
            ->where('x.itemid = ' . (int) $this->request->id)
            ->where('t.published = 1');
        $this->db->setQuery($query);
        $tags = $this->db->loadColumn();

        return $this->passSimple($tags, null, true);
    }
}

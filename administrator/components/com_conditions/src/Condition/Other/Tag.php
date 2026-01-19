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

use RegularLabs\Component\Conditions\Administrator\Condition\Condition;
use RegularLabs\Component\Conditions\Administrator\Condition\HasArraySelection;
use RegularLabs\Component\Conditions\Administrator\Helper\Cache;

class Tag extends Condition
{
    use HasArraySelection;

    public function pass(): bool
    {
        if ( ! $this->request->id)
        {
            return false;
        }

        if (in_array($this->request->option, ['com_content', 'com_flexicontent']))
        {
            return $this->passTagsContent();
        }

        if (
            $this->request->option != 'com_tags'
            || $this->request->view != 'tag'
        )
        {
            return false;
        }

        return $this->passTag($this->request->id);
    }

    private function getTag(int $tag_id): ?object
    {
        $query = $this->db->getQuery(true)
            ->select(['t.id', 't.alias', 't.title', 't.parent_id'])
            ->from('#__tags as t')
            ->where('t.id = ' . (int) $tag_id);
        $this->db->setQuery($query);

        return $this->db->loadObject();
    }

    private function getTagsParents(int $id = 0): array
    {
        if ( ! $id)
        {
            return [];
        }

        $cache = new Cache;

        if ($cache->exists())
        {
            return $cache->get();
        }

        $parents = [];

        while ($id)
        {
            $tag = $this->getTag($id);

            // Break if no parent is found or parent already found before for some reason
            if ( ! $tag || ! $tag->id || isset($parents[$tag->id]))
            {
                break;
            }

            $id = $tag->parent_id ?? 0;

            $parents[$tag->id] = $tag;
        }

        // Remove the root tag
        unset($parents[1]);

        return $cache->set($parents);
    }

    private function passTag(int $tag_id): bool
    {
        $tag = $this->getTag($tag_id);

        $pass = $this->tagIsInSelection($tag);

        if ($pass)
        {
            // If passed, return false if assigned to only children
            // Else return true
            return (int) $this->params->include_children !== 2;
        }

        if ( ! $this->params->include_children)
        {
            return false;
        }

        // Return true if a parent is in the selection
        $parents = $this->getTagsParents($tag->parent_id);

        foreach ($parents as $parent)
        {
            if ($this->tagIsInSelection($parent))
            {
                return true;
            }
        }

        return false;
    }

    private function passTagList(array $tags): bool
    {
        if ($this->params->match_all ?? false)
        {
            return $this->passTagListMatchAll($tags);
        }

        foreach ($tags as $tag)
        {
            if ( ! $this->passTag($tag->id))
            {
                continue;
            }

            return true;
        }

        return false;
    }

    private function passTagListMatchAll(array $tags): bool
    {
        foreach ($this->selection as $id)
        {
            if ( ! $this->passTagMatchAll($id, $tags))
            {
                return false;
            }
        }

        return true;
    }

    private function passTagMatchAll(int|string $id, array $tags): bool
    {
        foreach ($tags as $tag)
        {
            if ($tag->id == $id || $tag->alias == $id || $tag->title == $id)
            {
                return true;
            }
        }

        return false;
    }

    private function passTagsContent(): bool
    {
        $is_item     = in_array($this->request->view, ['', 'article', 'item']);
        $is_category = in_array($this->request->view, ['category']);

        switch (true)
        {
            case ($is_item):
                $prefix = 'com_content.article';
                break;

            case ($is_category):
                $prefix = 'com_content.category';
                break;

            default:
                return false;
        }

        // Load the tags.
        $query = $this->db->getQuery(true)
            ->select($this->db->quoteName('t.id'))
            ->select($this->db->quoteName('t.title'))
            ->from('#__tags AS t')
            ->join(
                'INNER', '#__contentitem_tag_map AS m'
                . ' ON m.tag_id = t.id'
                . ' AND m.type_alias = ' . $this->db->quote($prefix)
                . ' AND m.content_item_id = ' . (int) $this->request->id
            );
        $this->db->setQuery($query);
        $tags = $this->db->loadObjectList();

        if (empty($tags))
        {
            return false;
        }

        return $this->passTagList($tags);
    }

    private function tagIsInSelection(object $tag): bool
    {
        return $tag
            && (
                in_array($tag->id ?? 0, $this->selection)
                || in_array($tag->alias ?? '', $this->selection)
                || in_array($tag->title ?? '', $this->selection)
            );
    }
}

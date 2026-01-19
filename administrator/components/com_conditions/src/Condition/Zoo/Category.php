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

namespace RegularLabs\Component\Conditions\Administrator\Condition\Zoo;

defined('_JEXEC') or die;

use RegularLabs\Component\Conditions\Administrator\Condition\HasArraySelection;
use RegularLabs\Library\Input as RL_Input;

class Category extends Zoo
{
    use HasArraySelection;

    public function pass(): bool
    {
        if ($this->request->option !== 'com_zoo')
        {
            return false;
        }

        if (empty($this->selection))
        {
            return false;
        }

        $view = $this->request->view ?: $this->request->task;

        $page_types  = $this->params->page_types ?? [];
        $is_category = $view == 'category';
        $is_item     = $view == 'item';

        if (
            ! (in_array('categories', $page_types) && $is_category)
            && ! (in_array('items', $page_types) && $is_item)
            && ! (RL_Input::getInt('rl_qp') && ! empty($this->getCategoryIds()))
        )
        {
            return false;
        }

        $category_ids = $this->getCategoryIds($is_category);

        $pass = $this->passSimple($category_ids);

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

        $parent_ids = [];

        foreach ($category_ids as $category_id)
        {
            $parent_ids = [...$parent_ids, ...$this->getCategoryParentIds((int) $category_id)];
        }

        return $this->passSimple($parent_ids);
    }

    private function getCategoryIds(bool $is_category = false): array
    {
        if ($is_category)
        {
            return (array) $this->request->id;
        }

        if ($this->article && isset($this->article->catid))
        {
            return [$this->article->catid];
        }

        $menuparams = $this->getMenuItemParams($this->request->Itemid);

        $view = $this->request->view ?: $this->request->task;

        switch ($view)
        {
            case 'frontpage':
                if ($this->request->id)
                {
                    return [$this->request->id];
                }

                if ( ! isset($menuparams->application))
                {
                    return [];
                }

                return ['app' . $menuparams->application];

            case 'category':
                $cats = [];

                if ($this->request->id)
                {
                    $cats[] = $this->request->id;
                }
                elseif (isset($menuparams->category))
                {
                    $cats[] = $menuparams->category;
                }

                if (empty($cats[0]))
                {
                    return [];
                }

                $query = $this->db->getQuery(true)
                    ->select('c.application_id')
                    ->from('#__zoo_category AS c')
                    ->where('c.id = ' . (int) $cats[0]);
                $this->db->setQuery($query);
                $cats[] = 'app' . $this->db->loadResult();

                return $cats;

            case 'item':
                $id = $this->request->id;

                if ( ! $id && isset($menuparams->item_id))
                {
                    $id = $menuparams->item_id;
                }

                if ( ! $id)
                {
                    return [];
                }

                $query = $this->db->getQuery(true)
                    ->select('c.category_id')
                    ->from('#__zoo_category_item AS c')
                    ->where('c.item_id = ' . (int) $id)
                    ->where('c.category_id != 0');
                $this->db->setQuery($query);
                $cats = $this->db->loadColumn();

                $query = $this->db->getQuery(true)
                    ->select('i.application_id')
                    ->from('#__zoo_item AS i')
                    ->where('i.id = ' . (int) $id);
                $this->db->setQuery($query);
                $cats[] = 'app' . $this->db->loadResult();

                return $cats;

            default:
                return false;
        }
    }

    private function getCategoryParentIds(int $id = 0): array
    {
        return $this->getParentIds($id, 'zoo_category', 'parent', 'id');
    }
}

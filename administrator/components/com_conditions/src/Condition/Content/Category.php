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

namespace RegularLabs\Component\Conditions\Administrator\Condition\Content;

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use RegularLabs\Component\Conditions\Administrator\Condition\HasArraySelection;
use RegularLabs\Library\Article as RL_Article;
use RegularLabs\Library\Input as RL_Input;

class Category extends Content
{
    use HasArraySelection;

    public function pass(): bool
    {
        // components that use the com_content secs/cats
        $components = ['com_content', 'com_flexicontent'];

        if ( ! in_array($this->request->option, $components)
            && empty($this->category_id)
        )
        {
            return false;
        }

        if (empty($this->selection))
        {
            return false;
        }

        $page_types  = $this->params->page_types ?? [];
        $is_content  = in_array($this->request->option, ['com_content', 'com_flexicontent']);
        $is_category = in_array($this->request->view, ['category']);
        $is_item     = in_array($this->request->view, ['', 'article', 'item', 'form']);

        if (
            ! (in_array('categories', $page_types) && $is_content && $is_category)
            && ! (in_array('articles', $page_types) && $is_content && $is_item)
            && ! (in_array('others', $page_types) && ! ($is_content && ($is_category || $is_item)))
            && ! (RL_Input::getInt('rl_qp') && ! empty($this->getCategoryIds()))
            && empty($this->category_id)
        )
        {
            return false;
        }

        $pass = false;

        if (
            in_array('others', $page_types)
            && ! ($is_content && ($is_category || $is_item))
            && $this->article
        )
        {
            if ( ! isset($this->article->id) && isset($this->article->slug))
            {
                $this->article->id = (int) $this->article->slug;
            }

            if ( ! isset($this->article->catid) && isset($this->article->catslug))
            {
                $this->article->catid = (int) $this->article->catslug;
            }

            $this->request->id   = $this->article->id;
            $this->request->view = 'article';
        }

        $category_ids = $this->getCategoryIds($is_category);

        foreach ($category_ids as $category_id)
        {
            if ( ! $category_id)
            {
                continue;
            }

            $pass = in_array($category_id, $this->selection);

            if ($pass && (int) $this->params->include_children === 2)
            {
                $pass = false;
                continue;
            }

            if ( ! $pass && $this->params->include_children)
            {
                $parent_ids = $this->getCategoryParentIds((int) $category_id);
                $parent_ids = array_diff($parent_ids, [1]);

                foreach ($parent_ids as $parent_id)
                {
                    if (in_array($parent_id, $this->selection))
                    {
                        $pass = true;
                        break;
                    }
                }

                unset($parent_ids);
            }
        }

        return $pass;
    }

    private function getCategoryIds(bool $is_category = false): array
    {
        if ($is_category)
        {
            return (array) $this->request->id;
        }

        if ( ! empty($this->category_id))
        {
            return [$this->category_id];
        }

        $app = JFactory::getApplication();

        $category_id = $app->getUserState('com_content.edit.article.data.catid');

        if ( ! $category_id)
        {
            if ( ! $this->article && $this->request->id)
            {
                $this->article = RL_Article::get($this->request->id);
            }

            if ($this->article && isset($this->article->catid))
            {
                return (array) $this->article->catid;
            }
        }

        if ( ! $category_id)
        {
            $category_id = $app->getUserState('com_content.articles.filter.category_id');
        }

        if ( ! $category_id)
        {
            $category_id = RL_Input::getInt('catid');
        }

        $menuparams = $this->getMenuItemParams($this->request->Itemid);

        if ($this->request->view == 'featured')
        {
            $menuparams = $this->getMenuItemParams($this->request->Itemid);

            return (array) ($menuparams->featured_categories ?? $category_id);
        }

        return (array) ($menuparams->catid ?? $category_id);
    }

    private function getCategoryParentIds(int $id = 0): array
    {
        return $this->getParentIds($id, 'categories');
    }
}

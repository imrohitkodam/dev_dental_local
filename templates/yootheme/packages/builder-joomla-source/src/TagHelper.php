<?php

namespace YOOtheme\Builder\Joomla\Source;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\Component\Tags\Administrator\Table\TagTable;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;
use function YOOtheme\app;

class TagHelper
{
    /**
     * Gets the tags.
     *
     * @param int[] $ids
     *
     * @return object[]
     */
    public static function get($ids)
    {
        $tags = [];

        // Get a level row instance.
        $table = new TagTable(app(DatabaseDriver::class));

        foreach ((array) $ids as $id) {
            $table->load($id);

            if ($table->get('published') != 1) {
                continue;
            }

            if (!in_array($table->get('access'), Factory::getUser()->getAuthorisedViewLevels())) {
                continue;
            }

            $tags[] = (object) $table->getProperties(true);
        }

        return $tags;
    }

    public static function query($args = [])
    {
        $model = new TagsModel(['ignore_request' => true]);
        $params = ComponentHelper::getParams('com_tags');
        $params->set('show_pagination_limit', false);
        $params->set('published', 1);

        $model->setState('tag.parent_id', !empty($args['parent_id']) ? $args['parent_id'] : 0);
        $model->setState('tag.language', Multilanguage::isEnabled() ? 'current_language' : 'all');

        $props = [
            'limit' => 'maximum',
            'order' => 'all_tags_orderby',
            'order_direction' => 'all_tags_orderby_direction',
            'offset' => 'list.start',
        ];

        foreach (array_intersect_key($props, $args) as $key => $prop) {
            $params->set($prop, $args[$key]);
        }

        $model->setState('params', $params);

        return $model->getItems();
    }

    public static function filterTags($tags, $parentId)
    {
        $parent = current(static::get($parentId));

        return $parent
            ? array_filter($tags, fn($tag) => $tag->lft > $parent->lft && $tag->rgt < $parent->rgt)
            : [];
    }

    public static function getItems($tagId, $args)
    {
        $model = new TagModel(['ignore_request' => true]);
        $model->setState('tag.id', $tagId);
        $model->setState('tag.state', 1);
        $model->setState('tag.language', 'current_language');

        $model->setState(
            'params',
            new Registry([
                'include_children' => $args['include_children'] ?? false,
            ]),
        );

        if (!empty($args['order'])) {
            $args['order'] = "c.{$args['order']}";
        }

        $args['typesr'] = array_filter($args['typesr'] ?? []);
        if (empty($args['typesr'])) {
            unset($args['typesr']);
        }

        $props = [
            'typesr' => 'tag.typesr',
            'offset' => 'list.start',
            'limit' => 'list.limit',
            'order' => 'list.ordering',
            'order_direction' => 'list.direction',
            'include_children' => 'filter.include_children',
            'order_alphanum' => 'list.alphanum',
        ];

        foreach (array_intersect_key($props, $args) as $key => $prop) {
            $model->setState($prop, $args[$key]);
        }

        return $model->getItems();
    }
}

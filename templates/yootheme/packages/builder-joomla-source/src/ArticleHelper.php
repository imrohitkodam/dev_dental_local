<?php

namespace YOOtheme\Builder\Joomla\Source;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Event\Content\BeforeDisplayEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;
use function YOOtheme\app;

class ArticleHelper
{
    /**
     * Gets the articles.
     *
     * @param int[] $ids
     * @param array $args
     *
     * @return object[]
     */
    public static function get($ids, array $args = [])
    {
        return $ids ? static::query(['article' => (array) $ids] + $args) : [];
    }

    /**
     * Query articles.
     *
     * @param array $args
     *
     * @return object[]
     */
    public static function query(array $args = [])
    {
        $model = new ArticlesModel(['ignore_request' => true]);
        $model->setState('params', ComponentHelper::getParams('com_content'));
        $model->setState('filter.access', true);
        $model->setState('filter.published', 1);
        $model->setState('filter.language', empty($args['lang']) && Multilanguage::isEnabled());
        $model->setState('filter.subcategories', false);
        $model->setState('filter.max_category_levels', -1);

        $args += [
            'article_operator' => 'IN',
            'cat_operator' => 'IN',
            'tag_operator' => 'IN',
            'users_operator' => 'IN',
        ];

        if (!empty($args['order'])) {
            if ($args['order'] === 'rand') {
                $args['order'] = app(DatabaseDriver::class)->getQuery(true)->Rand();
            } elseif ($args['order'] === 'front') {
                $args['order'] = 'fp.ordering';
            } else {
                $args['order'] = "a.{$args['order']}";
            }
        }

        $props = [
            'offset' => 'list.start',
            'limit' => 'list.limit',
            'order' => 'list.ordering',
            'order_direction' => 'list.direction',
            'order_alphanum' => 'list.alphanum',
            'featured' => 'filter.featured',
            'subcategories' => 'filter.subcategories',
            'max_category_levels' => 'filter.max_category_levels',
            'tags' => 'filter.tags',
            'tag_operator' => 'filter.tag_operator',
            'include_child_categories' => 'filter.include_child_categories',
            'include_child_tags' => 'filter.include_child_tags',
            'lang' => 'filter.lang',
        ];

        foreach (array_intersect_key($props, $args) as $key => $prop) {
            $model->setState($prop, $args[$key]);
        }

        if (!empty($args['article'])) {
            $model->setState('filter.article_id', (array) $args['article']);
            $model->setState('filter.article_id.include', $args['article_operator'] === 'IN');
        }

        if (!empty($args['catid'])) {
            $model->setState('filter.category_id', $args['catid']);
            $model->setState('filter.category_id.include', $args['cat_operator'] === 'IN');
        }

        if (!empty($args['users'])) {
            $model->setState('filter.author_id', (array) $args['users']);
            $model->setState('filter.author_id.include', $args['users_operator'] === 'IN');
        }

        return $model->getItems();
    }

    public static function applyPageNavigation($article)
    {
        if (empty($article->pagination)) {
            $joomla = Factory::getApplication();

            if (method_exists($joomla, 'bootPlugin')) {
                $plugin = $joomla->bootPlugin('pagenavigation', 'content');
            } elseif (!($plugin = static::importPlugin('pagenavigation', 'content'))) {
                return null;
            }

            $plugin->params = new Registry(['display' => 0]);

            $params = clone $article->params;
            $params->set('show_item_navigation', true);

            $args = ['com_content.article', $article, $params, 0];

            if (version_compare(JVERSION, '5.3', '>=')) {
                $args = [new BeforeDisplayEvent('onContentBeforeDisplay', $args)];
            }

            $plugin->onContentBeforeDisplay(...$args);
        }

        return !empty($article->prev) || !empty($article->next);
    }

    /**
     * Only needed for Joomla 3.x because it has no Application::bootPlugin() method.
     *
     * @param string $plugin
     * @param string $type
     *
     * @return ?object
     */
    protected static function importPlugin($plugin, $type)
    {
        $path = JPATH_PLUGINS . "{$type}/{$plugin}/{$plugin}.php";
        $class = 'Plg' . str_replace('-', '', $type) . $plugin;

        if (is_file($path)) {
            require_once $path;
        }

        if (!class_exists($class)) {
            return null;
        }

        return (new \ReflectionClass($class))->newInstanceWithoutConstructor();
    }
}

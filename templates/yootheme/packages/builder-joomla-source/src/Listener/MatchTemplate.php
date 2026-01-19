<?php

namespace YOOtheme\Builder\Joomla\Source\Listener;

use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\Database\DatabaseDriver;
use YOOtheme\Builder\Joomla\Source\TagHelper;

class MatchTemplate
{
    public string $language;
    protected DatabaseDriver $db;

    public function __construct(?Document $document, DatabaseDriver $db)
    {
        $this->language = $document->language ?? 'en-gb';
        $this->db = $db;
    }

    public function handle($view, $tpl): ?array
    {
        if ($tpl) {
            return null;
        }

        $layout = $view->getLayout();
        $context = $view->get('context');

        if ($context === 'com_content.article' && $layout === 'default') {
            $item = $view->get('item');

            return [
                'type' => $context,
                'query' => [
                    'catid' => fn($ids, $query) => $this->matchCategory(
                        $item->catid,
                        $ids,
                        $query['include_child_categories'] ?? false,
                        'content',
                    ),
                    'tag' => fn($ids, $query) => $this->matchTag(
                        $item->tags->itemTags,
                        $ids,
                        $query['include_child_tags'] ?? false,
                    ),
                    'lang' => $this->language,
                ],
                'params' => ['item' => $item],
                'editUrl' => $item->params->get('access-edit')
                    ? Route::_(
                        RouteHelper::getFormRoute($item->id) .
                            '&return=' .
                            base64_encode(Uri::getInstance()),
                    )
                    : null,
            ];
        }

        if ($context === 'com_content.category' && $layout === 'blog') {
            $category = $view->get('category');
            $pagination = $view->get('pagination');

            return [
                'type' => $context,
                'query' => [
                    'catid' => fn($ids, $query) => $this->matchCategory(
                        $category,
                        $ids,
                        $query['include_child_categories'] ?? false,
                        'content',
                    ),
                    'tag' => fn($ids, $query) => $this->matchTag(
                        TagHelper::get($view->get('State')->get('filter.tag', [])),
                        $ids,
                        $query['include_child_tags'] ?? false,
                    ),
                    'pages' => $pagination->pagesCurrent === 1 ? 'first' : 'except_first',
                    'lang' => $this->language,
                ],
                'params' => [
                    'category' => $category,
                    'items' => array_merge($view->get('lead_items'), $view->get('intro_items')),
                    'pagination' => $pagination,
                ],
            ];
        }

        if ($context === 'com_content.featured') {
            $pagination = $view->get('pagination');

            return [
                'type' => $context,
                'query' => [
                    'pages' => $pagination->pagesCurrent === 1 ? 'first' : 'except_first',
                    'lang' => $this->language,
                ],
                'params' => ['items' => $view->get('items'), 'pagination' => $pagination],
            ];
        }

        if ($context === 'com_tags.tag') {
            $pagination = $view->get('pagination');
            $tags = $view->get('item');

            return [
                'type' => $context,
                'query' => [
                    'tag' => fn($ids, $query) => $this->matchTag(
                        $tags,
                        $ids,
                        $query['include_child_tags'] ?? false,
                    ),
                    'pages' => $pagination->pagesCurrent === 1 ? 'first' : 'except_first',
                    'lang' => $this->language,
                ],
                'params' => [
                    'tags' => $tags,
                    'items' => $view->get('items'),
                    'pagination' => $pagination,
                ],
            ];
        }

        if ($context === 'com_tags.tags') {
            $pagination = $view->get('pagination');

            return [
                'type' => $context,
                'query' => [
                    'lang' => $this->language,
                    'pages' => $pagination->pagesCurrent === 1 ? 'first' : 'except_first',
                ],
                'params' => [
                    'tags' => $view->get('items'),
                    'pagination' => $pagination,
                ],
            ];
        }

        if ($context === 'com_contact.contact') {
            $item = $view->get('item');
            return [
                'type' => $context,
                'query' => [
                    'catid' => fn($ids, $query) => $this->matchCategory(
                        $item->catid,
                        $ids,
                        $query['include_child_categories'] ?? false,
                        'contact',
                    ),
                    'tag' => fn($ids, $query) => $this->matchTag(
                        $item->tags->itemTags,
                        $ids,
                        $query['include_child_tags'] ?? false,
                    ),
                    'lang' => $this->language,
                ],
                'params' => ['item' => $item],
            ];
        }

        if ($context === 'com_finder.search') {
            $pagination = $view->get('pagination');
            $input = Factory::getApplication()->input;

            return [
                'type' => $input->getBool('live-search') ? '_search' : $context,
                'query' => [
                    'pages' => $pagination->pagesCurrent === 1 ? 'first' : 'except_first',
                    'lang' => $this->language,
                ],
                'params' => [
                    'search' => [
                        'searchword' => $view->get('query')->input ?: '',
                        'total' => $pagination->total,
                    ],
                    'items' => $view->get('results') ?? [],
                    'pagination' => $pagination,
                ],
            ];
        }

        if ($view->getName() === '404') {
            return [
                'type' => 'error-404',
                'query' => ['lang' => $this->language],
            ];
        }

        return null;
    }

    protected function matchCategory($category, $categoryIds, $includeChildren, $extension): bool
    {
        $match = in_array(is_object($category) ? $category->id : $category, $categoryIds);

        if (!$includeChildren || ($match && $includeChildren === 'include')) {
            return $match;
        }

        if ($match && $includeChildren === 'only') {
            return false;
        }

        if (!is_object($category)) {
            $category = Categories::getInstance($extension)->get($category);
        }

        return $category && array_intersect(array_keys($category->getPath()), $categoryIds);
    }

    protected function matchTag($tags, $tagIds, $includeChildren): bool
    {
        $match = (bool) array_intersect(array_column($tags, 'id'), $tagIds);

        if (!$includeChildren || ($match && $includeChildren === 'include')) {
            return $match;
        }

        if ($match && $includeChildren === 'only') {
            return false;
        }

        if (array_intersect(array_column($tags, 'parent_id'), $tagIds)) {
            return true;
        }

        $tags = array_filter($tags, fn($tag) => substr_count($tag->path, '/') >= 2);

        if (!$tags) {
            return false;
        }

        $query = sprintf(
            'SELECT 1 FROM #__tags WHERE id IN (%s) AND (%s) LIMIT 1',
            join(',', $tagIds),
            join(' OR ', array_map(fn($tag) => "(lft < {$tag->lft} AND rgt > {$tag->rgt})", $tags)),
        );

        return (bool) $this->db->setQuery($query)->loadResult();
    }
}

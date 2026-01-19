<?php

namespace YOOtheme\Builder\Joomla;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Categories\Categories;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\User\User;
use Joomla\Component\Content\Administrator\Extension\ContentComponent;
use Joomla\Component\Content\Administrator\Model\ArticleModel;
use Joomla\Component\Content\Administrator\Model\ArticlesModel;
use Joomla\Database\DatabaseDriver;
use YOOtheme\Builder;
use YOOtheme\Config;
use YOOtheme\Http\Request;
use YOOtheme\Http\Response;
use YOOtheme\Theme\Joomla\Listener\LoadArticleForm;

class PageController
{
    protected Config $config;
    protected CMSApplication $joomla;
    protected DatabaseDriver $db;

    public function __construct(Config $config, CMSApplication $joomla, DatabaseDriver $db)
    {
        $this->db = $db;
        $this->joomla = $joomla;
        $this->config = $config;
    }

    public function getPages(Request $request, Response $response, LoadArticleForm $article)
    {
        $home = $this->getHome();
        $categories = Categories::getInstance('content');
        $uncategorized = intval($this->config->get('~theme.page_category'));

        /** @var ArticlesModel $articles */
        $articles = $this->getModel('articles');
        $articles->setState('list.start', 0);
        $articles->setState('list.limit', 50);

        if ($language = $request->getQueryParam('lang')) {
            $articles->setState('filter.language', $language);
        }

        if ($search = $request->getQueryParam('search')) {
            $articles->setState('filter.search', $search);
        } elseif ($uncategorized) {
            $articles->setState('filter.category_id', $uncategorized);
        } else {
            return $response->withJson([]);
        }

        $items = [];

        foreach ($articles->getItems() as $item) {
            $category = $categories->get($item->catid);
            $root = array_key_first($category->getPath());
            $type = $root !== $item->catid ? $categories->get($root) : $category;
            $status = [-2 => 'trashed', 0 => 'unpublished', 1 => 'published', 2 => 'archived'];

            $items[] = [
                'id' => $item->id,
                'title' => $item->title,
                'status' => $status[$item->state] ?? '',
                'language' => $item->language,
                'url' => $article->getRoute($item),
                'home' => $item->id === $home,
                'type' => [
                    'id' => $type->slug,
                    'title' => $type->title,
                    'page' => $type->id === $uncategorized,
                ],
                'category' => [
                    'id' => $item->catid,
                    'title' => $item->category_title,
                ],
            ];
        }

        return $response->withJson($items);
    }

    public function savePage(Request $request, Response $response, Builder $builder, User $user)
    {
        $request
            ->abortIf(!($page = $request->getParam('page')), 400)
            ->abortIf(!($page = base64_decode($page)), 400)
            ->abortIf(!($page = json_decode($page)), 400);

        /** @var ArticleModel $model */
        $model = $this->getModel('article');
        $article = $model->getItem($page->id);

        \Closure::bind(
            fn() => $this->preprocessData('com_content.article', $article),
            $model,
            $model,
        )();

        $data = ['introtext' => '', 'fulltext' => ''] + (array) $article;

        if (version_compare(JVERSION, '4.0', '<')) {
            unset($data['featured']);
        }

        if ($page->content) {
            $content = json_encode($page->content);
            $fulltext = json_encode($builder->withParams(['context' => 'save'])->load($content));
            $introtext = $builder
                ->withParams(['context' => 'content'])
                ->render($content, ['prefix' => 'page']);

            $data['fulltext'] = "<!-- {$fulltext} -->";
            $data['introtext'] = $introtext;
        }

        $request->abortIf(!$this->allowEdit($user, $article), 403, 'Insufficient User Rights.');

        $collision = ArticleHelper::getCollision($article);

        if (
            !$request->getParam('overwrite') &&
            $collision['contentHash'] !== $page->collision->contentHash
        ) {
            return $response->withJson(['hasCollision' => true, 'collision' => $collision]);
        }

        if ($tags = $article->tags->tags) {
            $data['tags'] = explode(',', $tags);
        } else {
            // Joomla 3 (can be removed with 4+)
            unset($data['tags']);
        }

        $model->save($data);

        // reload article after save
        $article = $model->getItem($page->id);

        return $response->withJson([
            'id' => $page->id,
            'collision' => ArticleHelper::getCollision($article),
        ]);
    }

    protected function allowEdit(User $user, $article)
    {
        $asset = "com_content.article.{$article->id}";

        return $user->authorise('core.edit', $asset) ||
            ($user->authorise('core.edit.own', $asset) && $user->get('id') == $article->created_by);
    }

    protected function getModel($name)
    {
        if (version_compare(JVERSION, '4.0', '<')) {
            BaseDatabaseModel::addIncludePath(
                JPATH_ADMINISTRATOR . '/components/com_content/models',
                'ContentModel',
            );

            return BaseDatabaseModel::getInstance($name, 'ContentModel', [
                'ignore_request' => true,
            ]);
        }

        /** @var ContentComponent $component */
        $component = $this->joomla->bootComponent('com_content');

        return $component
            ->getMVCFactory()
            ->createModel($name, 'administrator', ['ignore_request' => true]);
    }

    protected function getHome()
    {
        $home = $this->joomla->getMenu('site')->getDefault();
        $view = $home->query['view'] ?? '';
        $option = $home->query['option'] ?? '';

        return $view === 'article' && $option === 'com_content' ? intval($home->query['id']) : null;
    }
}

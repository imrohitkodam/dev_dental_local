<?php

namespace YOOtheme\Builder\Joomla\Listener;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use YOOtheme\Builder;
use YOOtheme\Builder\Joomla\ArticleHelper;
use YOOtheme\Config;

class RenderBuilderPage
{
    public bool $first = true;
    public Config $config;
    public Builder $builder;
    public CMSApplication $joomla;

    public function __construct(Config $config, Builder $builder, CMSApplication $joomla)
    {
        $this->config = $config;
        $this->builder = $builder;
        $this->joomla = $joomla;
    }

    public function handle($event): void
    {
        $context = $event->getArgument('context');
        $article = $event->getArgument('subject');
        $params = $event->getArgument('params');

        if (
            !$this->first ||
            !ArticleHelper::isArticleView() ||
            $context !== 'com_content.article'
        ) {
            return;
        }

        // Make sure this is executed only once
        $this->first = false;

        $content = ArticleHelper::matchContent($article->fulltext);

        if ($params->get('access-edit') && $this->config->get('app.isCustomizer')) {
            if ($page = $this->config->get('req.customizer.page')) {
                $content = !empty($page['content']) ? json_encode($page['content']) : null;
            }

            $this->config->add('customizer.page', [
                'id' => $article->id,
                'title' => $article->title,
                'content' => $content ? $this->builder->load($content) : $content,
                'preview' => !empty($page),
                'collision' => ArticleHelper::getCollision($article),
            ]);
        }

        if ($content) {
            /**
             * Redirect to login page if access-view is false.
             */
            if (!$params->get('access-view')) {
                $this->joomla->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'notice');
                $this->joomla->redirect(
                    Route::_(
                        'index.php?option=com_users&view=login&return=' .
                            base64_encode(Uri::getInstance()),
                    ),
                    403,
                );
            }

            // Render builder output
            $article->text =
                $this->builder->render($content, [
                    'prefix' => 'page',
                    'article' => $article,
                    'template' => 'com_content.article',
                ]) ?? '';

            // Used to determine active builder layout in html/helpers.php
            $this->config->set('app.isBuilder', true);
        }
    }
}

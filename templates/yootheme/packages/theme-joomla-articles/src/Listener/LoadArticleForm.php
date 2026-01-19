<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Router\SiteRouter;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;
use Sh404sefHelperGeneral;
use YOOtheme\Config;
use YOOtheme\Metadata;
use YOOtheme\Path;
use YOOtheme\Theme\Joomla\ApiKey;
use YOOtheme\Url;

class LoadArticleForm
{
    public Config $config;
    public ApiKey $apiKey;
    public Metadata $metadata;
    public SiteRouter $router;
    public DatabaseDriver $db;

    public function __construct(
        Config $config,
        ApiKey $apiKey,
        Metadata $metadata,
        SiteRouter $router,
        DatabaseDriver $db
    ) {
        $this->config = $config;
        $this->apiKey = $apiKey;
        $this->metadata = $metadata;
        $this->router = $router;
        $this->db = $db;
    }

    public function handle($event): void
    {
        $context = $event->getArgument('context');
        $article = $event->getArgument('data');

        if ($context !== 'com_content.article') {
            return;
        }

        // on error $article is an array instead of object
        $article = (object) $article;
        $template = $this->getTemplate();

        if (empty($template->id)) {
            return;
        }

        $values = [
            'context' => $context,
            'apikey' => $this->apiKey->get(),
            'url' => Url::route('customizer', [
                'templateStyle' => $template->id,
                'section' => 'builder',
                'format' => 'html',
                'site' => $this->getRoute($article),
            ]),
        ];

        $this->metadata->set(
            'script:articles-data',
            sprintf(
                'window.yootheme ||= {}; var $articles = yootheme.articles = %s;',
                json_encode($values),
            ),
        );

        $this->metadata->set('script:articles', [
            'src' => Path::get('../../app/articles.min.js', __DIR__),
            'defer' => true,
        ]);
    }

    public function getRoute($article)
    {
        $route = RouteHelper::getArticleRoute(
            $article->id,
            $article->catid ?? 0,
            $article->language,
        );

        // Workaround for sh404sef to get article link with language code
        if (
            defined('SH404SEF_IS_RUNNING') &&
            class_exists(Sh404sefHelperGeneral::class) &&
            $this->config->get('app.isAdmin')
        ) {
            $site = Sh404sefHelperGeneral::getSefFromNonSef($route);
        } else {
            $site = (string) $this->router->build($route);
        }

        // Workaround for bug in Joomla 3.7
        if (str_starts_with($site, $base = Uri::root(true) . '/administrator')) {
            $site = Uri::root(true) . substr($site, strlen($base));
        }

        return $site ?: '/';
    }

    protected function getTemplate()
    {
        $this->db->setQuery(
            'SELECT id, params from #__template_styles WHERE client_id = 0 ORDER BY home DESC',
        );

        foreach ($this->db->loadObjectList() as $templ) {
            $params = new Registry($templ->params);

            if ($params->get('yootheme')) {
                return $templ;
            }
        }
    }
}

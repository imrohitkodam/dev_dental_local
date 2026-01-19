<?php

namespace YOOtheme\Builder\Joomla\Source\Listener;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Document\RawDocument;
use YOOtheme\Builder\Templates\TemplateHelper;
use YOOtheme\Config;
use function YOOtheme\app;

class LoadSearchTemplate
{
    public const MAX_ITEMS_PER_PAGE = 100;

    public Config $config;
    public CMSApplication $joomla;

    public function __construct(Config $config, CMSApplication $joomla)
    {
        $this->config = $config;
        $this->joomla = $joomla;
    }

    public function afterInitialiseDocument(): void
    {
        if (
            $this->joomla instanceof SiteApplication &&
            $this->joomla->input->getCmd('option') === 'com_finder' &&
            $this->joomla->input->getCmd('view') === 'search' &&
            $this->joomla->input->getBool('live-search') &&
            ($template = app(TemplateHelper::class)->match([
                'type' => '_search',
                'query' => ['lang' => $this->joomla->getDocument()->language],
            ])) &&
            ($results = (int) ($template['params']['live_search_results'] ?? 0))
        ) {
            $this->joomla
                ->getParams()
                ->set('list_limit', (string) min($results, static::MAX_ITEMS_PER_PAGE));
        }
    }

    public function afterDispatch(): void
    {
        $document = $this->joomla->getDocument();

        if (
            $document instanceof HtmlDocument &&
            $this->config->get('app.template.type') === '_search'
        ) {
            $doc = new RawDocument();
            $doc->setBuffer($document->getBuffer('component') ?? '');
            $this->joomla->loadDocument($doc);
        }
    }
}

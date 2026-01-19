<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Document\HtmlDocument;
use YOOtheme\Config;

class LoadConfigCache
{
    public array $keys = [
        'app.isBuilder',
        'app.template.type',
        '~theme.page_layout',
        'header.section.transparent',
    ];
    public bool $caching;
    public Config $config;
    public ?HtmlDocument $document;

    public function __construct(Config $config, CMSApplication $joomla, ?Document $document)
    {
        $this->config = $config;
        $this->document = $document instanceof HtmlDocument ? $document : null;
        $this->caching = $this->document && $joomla->get('caching');
    }

    /**
     * Add to Joomla caching.
     */
    public function handle(): void
    {
        if (!$this->caching) {
            return;
        }

        foreach ($this->keys as $key) {
            $value = $this->config->get($key);

            if (isset($value)) {
                $this->document->_custom[$key] = $value;
            }
        }
    }

    /**
     * Load from Joomla caching.
     */
    public function load(): void
    {
        if (!$this->caching) {
            return;
        }

        foreach ($this->keys as $key) {
            $value = $this->document->_custom[$key] ?? null;

            if (isset($value)) {
                $this->config->set($key, $value);
                unset($this->document->_custom[$key]);
            }
        }
    }
}

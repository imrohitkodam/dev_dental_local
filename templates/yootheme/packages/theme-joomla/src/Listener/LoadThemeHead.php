<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Plugin\PluginHelper;
use YOOtheme\Config;
use YOOtheme\Event;

class LoadThemeHead
{
    public Config $config;
    public Document $document;
    public Language $language;
    public ?SiteApplication $joomla;

    public function __construct(
        Config $config,
        Document $document,
        Language $language,
        ?SiteApplication $joomla
    ) {
        $this->config = $config;
        $this->joomla = $joomla;
        $this->document = $document;
        $this->language = $language;
    }

    public function handle(): void
    {
        if (!$this->isThemeActive()) {
            return;
        }

        $this->language->load('tpl_yootheme', $this->config->get('theme.rootDir'));
        $this->config->add('~theme', [
            'direction' => $this->document->getDirection(),
            'page_class' => $this->joomla->getParams()->get('pageclass_sfx'),
        ]);

        if (
            version_compare(JVERSION, '4.0', '<') &&
            PluginHelper::isEnabled('content', 'emailcloak')
        ) {
            $this->fixEmailCloak($this->document);
        }

        $custom = $this->config->get('~theme.custom_js', '');

        if ($custom && $this->document instanceof HtmlDocument) {
            $this->addCustomScript($this->document, $custom);
        }

        if ($this->config->get('~theme.jquery') || str_contains($custom, 'jQuery')) {
            HTMLHelper::_('jquery.framework');
        }

        Event::emit('theme.head');
    }

    protected function isThemeActive(): bool
    {
        return isset($this->joomla) &&
            $this->joomla->input->getCmd('tmpl') !== 'component' &&
            $this->joomla->input->getCmd('option') !== 'com_ajax' &&
            $this->config->get('theme.active');
    }

    protected function fixEmailCloak(Document $document): void
    {
        $document->addScriptDeclaration("document.addEventListener('DOMContentLoaded', function() {
            Array.prototype.slice.call(document.querySelectorAll('a span[id^=\"cloak\"]')).forEach(function(span) {
                span.innerText = span.textContent;
            });
        });");
    }

    protected function addCustomScript(HtmlDocument $document, $script): void
    {
        $script = trim($script);

        // Check for </script> for backwards compatibility (Will be dropped in the future)
        if (!str_starts_with($script, '<') || str_starts_with($script, '</script>')) {
            $attrs = $this->config->get('app.isCustomizer') ? ' data-preview="diff"' : '';
            $script = "<script{$attrs}>{$script}</script>";
        }

        $document->addCustomTag($script);
    }
}

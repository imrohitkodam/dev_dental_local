<?php

namespace YOOtheme\Theme\Joomla;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Editor\Editor;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;
use YOOtheme\Config;
use YOOtheme\ConfigObject;
use YOOtheme\Url;

class EditorConfig extends ConfigObject
{
    /**
     * Constructor.
     */
    public function __construct(
        CMSApplication $joomla,
        Config $config,
        Document $document,
        Language $language
    ) {
        $root = Uri::root();
        $editor = Editor::getInstance();
        $element = $joomla->get('editor');
        $language->load("plg_editors_{$element}");

        // current editor plugin
        $plugin = Table::getInstance('Extension');
        $plugin->load([
            'folder' => 'editors',
            'element' => $element,
        ]);

        // Prevent xtd editor buttons from adding assets to the current document
        if (version_compare(JVERSION, '4.0', '<')) {
            Factory::$document = clone $document;
        } else {
            $manager = $document->getWebAssetManager();

            // Add media select to allow Media (button)
            $manager->useScript('webcomponent.media-select');

            if (version_compare(JVERSION, '5.0', '>=')) {
                $manager->useScript('editors');
            }
        }

        parent::__construct([
            'id' => 'editor-xtd',
            'title' => $language->_($plugin->name ?? 'Editor'),
            'iframe' => Url::route('theme/editor', ['format' => 'html', 'tmpl' => 'component']),
            'buttons' => $this->getButtons($editor),
            'settings' => $this->getSettings() + [
                'branding' => false,
                'content_css' => version_compare(JVERSION, '4.0', '<')
                    ? "{$root}templates/system/css/editor.css"
                    : "{$root}media/system/css/editor.min.css",
                'directionality' => $config->get('locale.rtl') ? 'rtl' : 'ltr',
                'document_base_url' => $root,
                'entity_encoding' => 'raw',
                'insert_button_items' => '', // e.g. 'hr charmap',
                'plugins' => 'link autolink hr lists charmap paste',
                'toolbar1' =>
                    'formatselect bold italic bullist numlist blockquote alignleft aligncenter alignright link insert strikethrough hr pastetext removeformat charmap outdent indent',
            ],
        ]);

        if (version_compare(JVERSION, '4.0', '<')) {
            // Recover document
            Factory::$document = $document;
        }
    }

    protected function getSettings(): array
    {
        $tinymce = PluginHelper::getPlugin('editors', 'tinymce');
        $params = $tinymce ? json_decode($tinymce->params, true) : [];

        if (!empty($params['newlines'])) {
            $settings = [
                'forced_root_block' => '',
                'force_p_newlines' => false,
                'force_br_newlines' => true,
            ];
        } else {
            $settings = [
                'forced_root_block' => 'p',
                'force_p_newlines' => true,
                'force_br_newlines' => false,
            ];
        }

        return $settings;
    }

    protected function getButtons(Editor $editor): array
    {
        $buttons = $editor->getButtons('editor-xtd', ['pagebreak', 'readmore', 'widgetkit']);

        if (version_compare(JVERSION, '5.0', '>=')) {
            $buttons = array_map(
                fn($button) => [
                    'text' => $button->get('text'),
                    'link' => $button->get('link'),
                    'options' => $button->getOptions(),
                ],
                array_filter($buttons, fn($button) => $button->get('action') === 'modal'),
            );
        } else {
            $buttons = array_filter($buttons, fn($button) => !empty($button->modal));
        }

        return array_values($buttons);
    }
}

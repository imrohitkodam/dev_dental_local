<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Application\CMSApplication;
use YOOtheme\Config;
use YOOtheme\Theme\Joomla\EditorConfig;

class LoadEditorData
{
    public Config $config;
    public EditorConfig $editor;
    public CMSApplication $joomla;

    public function __construct(Config $config, EditorConfig $editor, CMSApplication $joomla)
    {
        $this->config = $config;
        $this->editor = $editor;
        $this->joomla = $joomla;
    }

    public function handle(): void
    {
        // skip visual editor
        if (in_array($this->joomla->get('editor'), ['none', 'codemirror'])) {
            return;
        }

        $this->config->add('customizer', ['editor' => $this->editor->getArrayCopy()]);
    }
}

<?php

namespace YOOtheme\Theme\Joomla;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Editor\Editor;
use Joomla\CMS\HTML\HTMLHelper;

class EditorController
{
    public static function render(CMSApplication $joomla, HtmlDocument $document)
    {
        $type = $joomla->get('editor');
        $editor = Editor::getInstance($type);
        $exclude = ['pagebreak', 'readmore', 'widgetkit'];

        // core.js needs to initialize Joomla.editors early
        HTMLHelper::_('behavior.core');

        ob_start();

        echo "<form>{$editor->display('content', '', '100%', '550', '', '30', $exclude)}</form>";

        $document->setBuffer(ob_get_clean(), [
            'type' => 'component',
            'name' => null,
            'title' => null,
        ]);
    }
}

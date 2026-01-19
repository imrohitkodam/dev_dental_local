<?php

namespace YOOtheme;

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use YOOtheme\Builder\Templates\TemplateHelper;

/**
 * @var Config $config
 * @var View $view
 * @var TemplateHelper $templates
 */
[$config, $view, $templates] = app(Config::class, View::class, TemplateHelper::class);

$liveSearch = preg_match('/^(logo|navbar|header)/', $module->position) && $templates->match([
        'type' => '_search',
        'query' => ['lang' => $app->getDocument()->language],
    ]);
$autosuggest = !$liveSearch && $params->get('show_autosuggest', 1);

$fields = [[
    'tag' => 'input',
    'name' => 'q',
    'class' => $autosuggest ? ['js-finder-search-query'] : [],
    'value' => $app->input->getCmd('option') === 'com_finder' ? urldecode($app->input->getString('q', '')) : false,
    'placeholder' => Text::_('TPL_YOOTHEME_SEARCH'),
    'required' => true,
    'aria-label' => Text::_('TPL_YOOTHEME_SEARCH'),
]];

$uri = Uri::getInstance(Route::_($route));
$uri->delVar('q');

$query = $uri->getQuery(true);

// Search Filter
if ($filter = $config('~theme.com_finder_filter')) {
    $query += ['f' => $filter];
}

// Create hidden input elements for each part of the URI.
foreach ($query as $name => $value) {
    $fields[] = ['tag' => 'input', 'type' => 'hidden', 'name' => $name, 'value' => $value];
}

echo $view('~theme/templates/search', [

    'position' => $module->position,

    'tag' => $module->attrs,

    'attrs' => [
        'id' => "search-{$module->id}",
        'action' => Route::_($route),
        'method' => 'get',
        'role' => 'search',
        'class' => ['js-finder-searchform'],
    ],

    'fields' => $fields,

    'iconClass' => [
        'uk-position-z-index' => $autosuggest, // Needed because of `awesomplete` HTML class has a `z-index`
    ],

    'language' => $app->getDocument()->language,

]);

// This segment of code sets up the autocompleter.
if ($autosuggest) {
    $document = $app->getDocument();

    if (version_compare(JVERSION, '4.0', '<')) {
        HTMLHelper::_('behavior.core');
        $document->addScript(
            Url::to(Path::get('~theme/html/com_finder/assets/awesomplete/js/awesomplete.min.js')),
            ['version' => 'auto']
        );
        $document->addScript(
            Url::to(Path::get('~theme/html/com_finder/assets/com_finder/js/finder.min.js')),
            ['version' => 'auto'],
            ['defer' => true]
        );
    } else {
        $assetManager = $document->getWebAssetManager();
        $assetManager->usePreset('awesomplete');
        $assetManager->disableStyle('awesomplete');
        $assetManager->getRegistry()->addExtensionRegistryFile('com_finder');
        $assetManager->useScript('com_finder.finder');

        Text::script('JLIB_JS_AJAX_ERROR_OTHER');
        Text::script('JLIB_JS_AJAX_ERROR_PARSE');
    }
    $document->addScriptOptions('finder-search', ['url' => Route::_('index.php?option=com_finder&task=suggestions.suggest&format=json&tmpl=component')]);
}

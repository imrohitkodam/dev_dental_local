<?php

namespace YOOtheme\Theme\Joomla;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\User\User;
use Joomla\Database\DatabaseDriver;
use YOOtheme\Config;
use YOOtheme\Event;
use YOOtheme\Http\Request;
use YOOtheme\Path;
use YOOtheme\Url;

class CustomizerController
{
    public static function index(
        Request $request,
        User $user,
        Config $config,
        Document $document,
        CMSApplication $joomla
    ) {
        $request->abortIf(!$document instanceof HtmlDocument, 400, 'Bad Request');

        HTMLHelper::_('behavior.keepalive');

        // init customizer
        Event::emit('customizer.init');

        // init config
        $config->add('customizer', [
            'config' => $config('~theme'),
            'return' => $request->getQueryParam('return') ?: Url::to('administrator/index.php'),
        ]);

        // api key editable?
        if (
            !$user->authorise('core.edit', 'com_installer') ||
            !$user->authorise('core.manage', 'com_installer')
        ) {
            $config->del('customizer.sections.settings.fields.settings.items.api-key');
        }

        // set system template
        $joomla->set('theme', 'system');
        $joomla->input->set('tmpl', 'component');

        // set document title/icon
        $document->setTitle("Website Builder - {$joomla->get('sitename')}");
        $document->addFavicon(Url::to(Path::get('../assets/images/favicon.png', __DIR__)));
        $document->setBuffer('<div id="customizer"></div>', [
            'type' => 'component',
            'name' => null,
            'title' => null,
        ]);
    }

    public static function save(Request $request, User $user, Config $config, DatabaseDriver $db)
    {
        $request->abortIf(
            !$user->authorise('core.edit', 'com_templates'),
            403,
            'Insufficient User Rights.',
        );

        // get config values
        $values = Event::emit('config.save|filter', $request->getParam('config', []));

        // fetch current style params
        $params = $db
            ->setQuery(
                sprintf('SELECT params FROM #__template_styles WHERE id = %d', $config('theme.id')),
            )
            ->loadResult();

        // prepare style params
        $params =
            ['config' => json_encode($values, JSON_UNESCAPED_SLASHES)] +
            (json_decode($params, true) ?: []);

        // update style params
        $style = (object) [
            'id' => $config('theme.id'),
            'params' => json_encode($params, JSON_UNESCAPED_SLASHES),
        ];

        $db->updateObject('#__template_styles', $style, 'id');

        return 'success';
    }
}

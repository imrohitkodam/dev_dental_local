<?php

namespace YOOtheme\Builder\Joomla\Listener;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use YOOtheme\Config;

class RenderBuilderButton
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function handle($event): void
    {
        if (!$this->config->get('app.isBuilder')) {
            return;
        }

        [$view] = $event->getArguments();

        $layout = $view->getLayout();
        $context = $view->get('context');

        if ($context !== 'com_content.article' || $layout !== 'default') {
            return;
        }

        $article = $view->get('item');
        $content = $article->text;

        if ($article->params->get('access-edit') && !$this->config->get('app.isCustomizer')) {
            $url = Route::_(
                RouteHelper::getFormRoute($article->id) .
                    '&return=' .
                    base64_encode(Uri::getInstance()),
            );
            $content .=
                "<a style=\"position: fixed!important\" class=\"uk-position-medium uk-position-bottom-right uk-position-z-index uk-button uk-button-primary\" href=\"{$url}\">" .
                Text::_('JACTION_EDIT') .
                '</a>';
        }

        $view->set('_output', $content);
    }
}

<?php

namespace YOOtheme\Builder\Joomla\Source\Listener;

use Joomla\CMS\Language\Text;
use YOOtheme\Builder;
use YOOtheme\Builder\Templates\TemplateHelper;
use YOOtheme\Config;
use YOOtheme\Event;
use function YOOtheme\app;

class LoadTemplate
{
    public Config $config;
    public Builder $builder;

    public function __construct(Config $config, Builder $builder)
    {
        $this->config = $config;
        $this->builder = $builder;
    }

    public function handle($event): void
    {
        [$htmlView, $tpl] = $event->getArguments();
        $view = Event::emit('builder.template', $htmlView, $tpl);

        if (empty($view['type'])) {
            return;
        }

        // get template from customizer request?
        $template = $this->config->get('req.customizer.template');

        if ($this->config->get('app.isCustomizer')) {
            $this->config->set('customizer.view', $view['type']);
        }

        if ($this->config->get('app.isBuilder') && empty($template)) {
            return;
        }

        // get visible template
        $visible = app(TemplateHelper::class)->match($view);

        // set template identifier
        if ($this->config->get('app.isCustomizer')) {
            $this->config->add('customizer.template', [
                'id' => $template['id'] ?? null,
                'visible' => $visible['id'] ?? null,
            ]);
        }

        if ($template ??= $visible) {
            // get output from builder
            $output = $this->builder->render(
                json_encode($template['layout'] ?? []),
                ($view['params'] ?? []) + [
                    'prefix' => "template-{$template['id']}",
                    'template' => $template['type'],
                ],
            );

            // append frontend edit button?
            if ($output && isset($view['editUrl']) && !$this->config->get('app.isCustomizer')) {
                $output .=
                    "<a style=\"position: fixed!important\" class=\"uk-position-medium uk-position-bottom-right uk-position-z-index uk-button uk-button-primary\" href=\"{$view['editUrl']}\">" .
                    Text::_('JACTION_EDIT') .
                    '</a>';
            }

            $htmlView->set('_output', $output ?? '');
            $this->config->set('app.isBuilder', true);
            $this->config->set('app.template', $template);
        }
    }
}

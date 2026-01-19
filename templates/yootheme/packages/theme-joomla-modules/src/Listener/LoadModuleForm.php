<?php

namespace YOOtheme\Theme\Joomla\Listener;

class LoadModuleForm
{
    public static function handle($event): void
    {
        $form = $event->getArgument('subject');
        $data = $event->getArgument('data');

        if (
            !in_array($form->getName(), [
                'com_config.modules',
                'com_modules.module',
                'com_advancedmodules.module',
            ])
        ) {
            return;
        }

        // copy params config to yoo_config
        if (!isset($data->params['yoo_config']) && isset($data->params['config'])) {
            $data->params['yoo_config'] = $data->params['config'];
        }

        // add yoo_config hidden input field
        $form->load(
            '<form><fields name="params"><fieldset name="advanced"><field name="yoo_config" type="hidden" default="{}" /></fieldset></fields></form>',
        );
    }
}

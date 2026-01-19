<?php

namespace YOOtheme\Builder\Joomla\Fields\Listener;

use YOOtheme\Builder\BuilderConfig;
use YOOtheme\Builder\Joomla\Fields\FieldsHelper;
use function YOOtheme\trans;

class LoadBuilderConfig
{
    /**
     * @param BuilderConfig $config
     */
    public static function handle($config): void
    {
        $fields = [];

        foreach (FieldsHelper::getFields('com_content.article') as $field) {
            if (
                $field->fieldparams->get('multiple') ||
                $field->fieldparams->get('repeat') ||
                $field->type === 'repeatable'
            ) {
                continue;
            }

            $fields[] = ['value' => "field:{$field->id}", 'text' => $field->title];
        }

        if ($fields) {
            $config->push('sources.articleOrderOptions', [
                'label' => trans('Custom Fields'),
                'options' => $fields,
            ]);
        }
    }
}

<?php

namespace YOOtheme\Builder\Joomla\Fields\Listener;

use YOOtheme\Builder\Joomla\Fields\FieldsHelper;
use YOOtheme\Builder\Joomla\Fields\Type;

class LoadSourceTypes
{
    public static function handle($source): void
    {
        $types = [
            'User' => 'com_users.user',
            'Article' => 'com_content.article',
            'Category' => 'com_content.categories',
            'Contact' => 'com_contact.contact',
        ];

        $source->objectType('SqlField', Type\SqlFieldType::config());
        $source->objectType('ValueField', Type\ValueFieldType::config());
        $source->objectType('MediaField', Type\MediaFieldType::config());
        $source->objectType('ChoiceField', Type\ChoiceFieldType::config());
        $source->objectType('ChoiceFieldString', Type\ChoiceFieldStringType::config());

        foreach ($types as $type => $context) {
            // has custom fields?
            if ($fields = FieldsHelper::getFields($context)) {
                static::configFields($source, $type, $context, $fields);
            }
        }
    }

    protected static function configFields($source, $type, $context, array $fields): void
    {
        // add field on type
        $source->objectType(
            $type,
            $config = [
                'fields' => [
                    'field' => [
                        'type' => ($fieldType = "{$type}Fields"),
                        'extensions' => [
                            'call' => Type\FieldsType::class . '::field',
                        ],
                    ],
                ],
            ],
        );

        if ($type === 'Article') {
            $source->objectType('TagItem', $config);
            $source->objectType('SmartSearchItem', $config);
        }

        // configure field type
        $source->objectType($fieldType, Type\FieldsType::config($source, $type, $context, $fields));
    }
}

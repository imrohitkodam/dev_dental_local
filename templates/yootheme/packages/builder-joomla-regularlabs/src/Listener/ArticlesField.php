<?php

namespace YOOtheme\Builder\Joomla\RegularLabs\Listener;

use Joomla\CMS\Plugin\PluginHelper;
use YOOtheme\Builder\Joomla\Fields\Type\FieldsType;
use YOOtheme\Builder\Joomla\Source\ArticleHelper;
use YOOtheme\Builder\Source;

class ArticlesField
{
    /**
     * @param array $config
     * @param object $field
     * @param Source $source
     * @param string $context
     */
    public function config($config, $field, $source, $context)
    {
        if (!PluginHelper::isEnabled('fields', 'articles')) {
            return $config;
        }

        if ($field->type !== 'articles') {
            return $config;
        }

        return [
            [
                'type' => $field->fieldparams->get('multiple')
                    ? ['listOf' => 'Article']
                    : 'Article',
                'extensions' => [
                    'call' => [
                        'func' => __CLASS__ . '::resolve',
                        'args' => [
                            'context' => $context,
                            'field' => $field->name,
                            'id' => $field->id,
                        ],
                    ],
                ],
            ] + $config,
        ];
    }

    public static function resolve($item, $args)
    {
        $field = isset($item->id)
            ? FieldsType::getField($args['field'], $item, $args['context'])
            : FieldsType::getSubfield($args['id'] ?? 0, $args['context']);

        if (!$field) {
            return;
        }

        $fieldValue = $field->rawvalue ?? ($item["field{$args['id']}"] ?? null);

        if (!$fieldValue) {
            return;
        }

        $ordering = $field->fieldparams->get('articles_ordering', 'title');
        $direction = $field->fieldparams->get('articles_ordering_direction', 'ASC');
        $ordering2 = $field->fieldparams->get('articles_ordering_2', 'created');
        $direction2 = $field->fieldparams->get('articles_ordering_direction_2', 'DESC');

        $value = ArticleHelper::get($fieldValue, [
            'order' => "{$ordering} {$direction}, a.{$ordering2}",
            'order_direction' => $direction2,
        ]);

        if ($field->fieldparams['multiple']) {
            return $value;
        }

        return array_shift($value);
    }
}

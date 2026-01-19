<?php

namespace YOOtheme\Builder\Joomla\Fields;

use Joomla\Component\Fields\Administrator\Helper\FieldsHelper as BaseFieldHelper;

class FieldsHelper
{
    public static function getFields($context, $item = null, $includeSubformFields = false)
    {
        return class_exists(BaseFieldHelper::class)
            ? array_filter(
                BaseFieldHelper::getFields($context, $item, false, null, $includeSubformFields),
                fn($field) => $field->state == 1,
            )
            : [];
    }
}

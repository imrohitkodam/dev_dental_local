<?php

namespace YOOtheme\Builder\Joomla\Fields\Type;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Component\Users\Administrator\Helper\UsersHelper;
use Joomla\Database\DatabaseDriver;
use YOOtheme\Arr;
use YOOtheme\Builder\Joomla\Fields\FieldsHelper;
use YOOtheme\Builder\Source;
use YOOtheme\Config;
use YOOtheme\Event;
use YOOtheme\Path;
use YOOtheme\Str;
use function YOOtheme\app;
use function YOOtheme\trans;

class FieldsType
{
    /**
     * @var string
     */
    protected $context;

    /**
     * Constructor.
     *
     * @param string $context
     */
    public function __construct($context)
    {
        $this->context = $context;
    }

    /**
     * @param Source $source
     * @param string $type
     * @param string $context
     * @param array  $fields
     *
     * @return array
     */
    public static function config(Source $source, $type, $context, array $fields)
    {
        return [
            'fields' => array_filter(
                array_reduce(
                    $fields,
                    fn($fields, $field) => $fields +
                        static::configFields(
                            $field,
                            [
                                'type' => 'String',
                                'name' => strtr($field->name, '-', '_'),
                                'metadata' => [
                                    'label' => $field->title,
                                    'group' => $field->group_title ?: trans('Fields'),
                                ],
                                'extensions' => [
                                    'call' => "{$type}.fields@resolve",
                                ],
                            ],
                            $source,
                            $context,
                            $type,
                        ),
                    [],
                ),
            ),

            'extensions' => [
                'bind' => [
                    "{$type}.fields" => [
                        'class' => __CLASS__,
                        'args' => ['$context' => $context],
                    ],
                ],
            ],
        ];
    }

    protected static function configFields($field, array $config, Source $source, $context, $type)
    {
        $config = is_callable($callback = [__CLASS__, "config{$field->type}"])
            ? $callback($field, $config, $source, $context, $type)
            : static::configGenericField($field, $config);

        $config =
            Event::emit('source.com_fields.field|filter', $config, $field, $source, $context) ?: [];

        return array_is_list($config)
            ? array_combine(array_column($config, 'name'), $config)
            : [$config['name'] => $config];
    }

    protected static function configGenericField($field, array $config)
    {
        if ($field->fieldparams->get('multiple')) {
            return ['type' => ['listOf' => 'ValueField']] + $config;
        }

        return $config;
    }

    protected static function configText($field, array $config)
    {
        return array_replace_recursive($config, [
            'metadata' => ['filters' => ['limit', 'preserve']],
        ]);
    }

    protected static function configTextarea($field, array $config)
    {
        return array_replace_recursive($config, [
            'metadata' => ['filters' => ['limit', 'preserve']],
        ]);
    }

    protected static function configEditor($field, array $config)
    {
        return array_replace_recursive($config, [
            'metadata' => ['filters' => ['limit', 'preserve']],
        ]);
    }

    protected static function configCalendar($field, array $config)
    {
        return array_replace_recursive($config, ['metadata' => ['filters' => ['date']]]);
    }

    protected static function configUser($field, array $config)
    {
        return ['type' => 'User'] + $config;
    }

    /**
     * Remove when Joomla 3 support is dropped.
     */
    protected static function configRepeatable($field, array $config, Source $source)
    {
        $fields = [];

        foreach ((array) $field->fieldparams->get('fields') as $params) {
            $fields[$params->fieldname] = [
                'type' => $params->fieldtype === 'media' ? 'MediaField' : 'String',
                'name' => Str::snakeCase($params->fieldname),
                'metadata' => [
                    'label' => $params->fieldname,
                    'filters' => !in_array($params->fieldtype, ['media', 'number'])
                        ? ['limit', 'preserve']
                        : [],
                ],
            ];
        }

        if ($fields) {
            $name = Str::camelCase(['Field', $field->name], true);
            $source->objectType($name, compact('fields'));

            return [
                'type' => ['listOf' => $name],
                'metadata' => array_merge($config['metadata'], ['label' => $field->title]),
            ] + $config;
        }
    }

    protected static function configSubform($field, array $config, Source $source, $context, $type)
    {
        $fields = [];

        foreach ((array) $field->fieldparams->get('options', []) as $option) {
            $subField = static::getSubfield($option->customfield, $context);

            if (!$subField) {
                continue;
            }

            // Joomla update from 3 to 4 changed Repeatable fields to Subform fields,
            // created subform only fields for the subfields and
            // prefixed them with the field name and an underscore
            $prefix = "{$field->name}_";
            $name = str_starts_with($subField->name, $prefix)
                ? substr($subField->name, strlen($prefix))
                : $subField->name;

            $fields += static::configFields(
                $subField,
                [
                    'type' => 'String',
                    'name' => Str::snakeCase($name),
                    'metadata' => [
                        'label' => $subField->title,
                    ],
                    'extensions' => [
                        'call' => [
                            'func' => "{$type}.fields@resolveSubfield",
                            'args' => ['context' => $context, 'id' => $option->customfield],
                        ],
                    ],
                ],
                $source,
                $context,
                $type,
            );
        }

        if ($fields = array_filter($fields)) {
            $name = Str::camelCase(['Field', $field->name], true);
            $source->objectType($name, compact('fields'));

            return ($field->fieldparams->get('repeat')
                ? ['type' => ['listOf' => $name]]
                : [
                    'type' => $name,
                    'metadata' => array_merge($config['metadata'], ['label' => $field->title]),
                ]) + $config;
        }
    }

    protected static function configMedia($field, array $config)
    {
        return ['type' => 'MediaField'] + $config;
    }

    protected static function configSql($field, array $config)
    {
        return [
            'type' => $field->fieldparams->get('multiple') ? ['listOf' => 'SqlField'] : 'SqlField',
        ] + $config;
    }

    protected static function configList($field, array $config)
    {
        if ($field->fieldparams->get('multiple')) {
            return [
                [
                    'type' => ['listOf' => 'ChoiceField'],
                ] + $config,
                [
                    'name' => "{$config['name']}String",
                    'type' => 'ChoiceFieldString',
                ] + $config,
            ];
        }

        return ['type' => 'ChoiceField'] + $config;
    }

    protected static function configRadio($field, array $config)
    {
        return ['type' => 'ChoiceField'] + $config;
    }

    protected static function configCheckboxes($field, array $config)
    {
        return [
            [
                'type' => ['listOf' => 'ChoiceField'],
            ] + $config,
            [
                'name' => "{$config['name']}String",
                'type' => 'ChoiceFieldString',
            ] + $config,
        ];
    }

    public static function field($item, $args, $ctx, $info)
    {
        return $item;
    }

    public function resolve($item, $args, $ctx, $info)
    {
        $name = str_replace('String', '', strtr($info->fieldName, '_', '-'));

        if (!isset($item->id) || !($field = static::getField($name, $item, $this->context))) {
            return;
        }

        return $this->resolveField($field, $field->rawvalue);
    }

    public function resolveField($field, $value)
    {
        if (is_callable($callback = [$this, "resolve{$field->type}"])) {
            return $callback($field);
        }

        return $this->resolveGenericField($field, $value);
    }

    public function resolveGenericField($field, $value)
    {
        if ($field->fieldparams->exists('multiple')) {
            $value = (array) $value;

            if ($field->fieldparams['multiple']) {
                return array_map(
                    fn($value) => is_scalar($value) ? ['value' => $value] : $value,
                    $value,
                );
            } else {
                return array_shift($value);
            }
        }

        return $field->rawvalue;
    }

    public function resolveUser($field)
    {
        return Factory::getUser($field->rawvalue);
    }

    /**
     * Remove when Joomla 3 support is dropped.
     */
    public function resolveRepeatable($field)
    {
        $fields = [];
        foreach ($field->fieldparams->get('fields', []) as $subField) {
            $fields[$subField->fieldname] = $subField->fieldtype;
        }

        return array_map(function ($vals) use ($fields) {
            $values = [];

            foreach ($vals as $name => $value) {
                if (Arr::get($fields, $name) === 'media') {
                    $values[Str::snakeCase($name)] = ['imagefile' => $value];
                } else {
                    $values[Str::snakeCase($name)] = $value;
                }
            }

            return $values;
        }, array_values(json_decode($field->rawvalue, true) ?: []));
    }

    public function resolveSubform($field)
    {
        return is_string($field->rawvalue) ? json_decode($field->rawvalue, true) : $field->rawvalue;
    }

    public function resolveSubfield($value, $args, $ctx, $info)
    {
        $subfield = clone static::getSubfield($args['id'], $this->context);

        if (!$subfield || empty($value["field{$args['id']}"])) {
            return;
        }

        $subfield->rawvalue = $subfield->value = $value["field{$args['id']}"];

        return $this->resolveField($subfield, $subfield->rawvalue);
    }

    public function resolveList($field)
    {
        return $this->resolveSelect($field, $field->fieldparams->get('multiple'));
    }

    public function resolveCheckboxes($field)
    {
        return $this->resolveSelect($field, true);
    }

    public function resolveRadio($field)
    {
        return $this->resolveSelect($field);
    }

    public function resolveSelect($field, $multiple = false)
    {
        $result = [];

        foreach ($field->fieldparams->get('options', []) as $option) {
            if (in_array($option->value, (array) $field->rawvalue ?: [])) {
                if ($multiple) {
                    $result[] = $option;
                    continue;
                }

                return $option;
            }
        }

        return $result;
    }

    public function resolveImagelist($field)
    {
        $config = app(Config::class);
        $root = Path::relative(
            $config('app.rootDir'),
            Path::join($config('app.uploadDir'), $field->fieldparams->get('directory')),
        );

        return $this->resolveGenericField(
            $field,
            array_map(
                fn($value) => Path::join($root, $value),
                array_filter((array) $field->rawvalue, fn($value) => $value && $value != -1),
            ),
        );
    }

    public function resolveMedia($field)
    {
        $value = $field->rawvalue;

        if (is_array($value)) {
            return $value;
        }

        if (!is_string($value)) {
            return;
        }

        if (str_starts_with($value, '{')) {
            return json_decode($value, true);
        }

        return ['imagefile' => $value, 'alt_text' => ''];
    }

    public function resolveUsergrouplist($field)
    {
        return $this->resolveGenericField(
            $field,
            array_intersect_key($this->getUserGroups(), array_flip((array) $field->rawvalue)),
        );
    }

    public function resolveSql($field)
    {
        if ($field->rawvalue === '') {
            return;
        }

        /** @var DatabaseDriver $db */
        $db = app(DatabaseDriver::class);
        $query = $field->fieldparams->get('query', '');
        $condition = array_reduce(
            (array) $field->rawvalue,
            fn($carry, $value) => $value ? $carry . ", {$db->quote($value)}" : $carry,
        );

        // Run the query with a having condition because it supports aliases
        $db->setQuery(
            sprintf(
                'SELECT value, text FROM (%s) as a having value in (%s)',
                preg_replace('/[\s;]*$/', '', $query),
                trim($condition, ','),
            ),
        );

        $items = $db->loadObjectList();

        return $field->fieldparams->get('multiple') ? $items : array_pop($items);
    }

    protected function getUserGroups()
    {
        $data = [];

        foreach (UsersHelper::getGroups() as $group) {
            $data[$group->value] = Text::_(preg_replace('/^(- )+/', '', $group->text));
        }

        return $data;
    }

    public static function getField($name, $item, $context)
    {
        $fields = static::getFields($item, $context);

        return $fields[$name] ?? null;
    }

    protected static function getFields($item, $context)
    {
        if (isset($item->_fields)) {
            return $item->_fields;
        }

        PluginHelper::importPlugin('fields');

        $fields = [];

        foreach ($item->jcfields ?? FieldsHelper::getFields($context, $item) as $field) {
            if ($item && !isset($item->jcfields)) {
                Factory::getApplication()->triggerEvent('onCustomFieldsBeforePrepareField', [
                    $context,
                    $item,
                    &$field,
                ]);
            }

            $fields[$field->name] = $field;
        }

        if (isset($item)) {
            $item->_fields = $fields;
        }

        return $fields;
    }

    public static function getSubfield($id, $context)
    {
        static $fields = [];

        if (!isset($fields[$context])) {
            $fields[$context] = [];
            foreach (FieldsHelper::getFields($context, null, true) as $field) {
                $fields[$context][$field->id] = $field;
            }
        }

        return $fields[$context][$id] ?? null;
    }
}

<?php

namespace YOOtheme\Builder\Joomla\Source\Listener;

use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\Database\DatabaseDriver;
use YOOtheme\Builder\BuilderConfig;
use YOOtheme\Builder\Joomla\Source\UserHelper;
use function YOOtheme\trans;

class LoadBuilderConfig
{
    public DatabaseDriver $db;

    public function __construct(DatabaseDriver $db)
    {
        $this->db = $db;
    }

    /**
     * @param BuilderConfig $config
     */
    public function handle($config): void
    {
        $config->merge([
            'languages' => array_map(
                fn($lang) => [
                    'value' => $lang->value == '*' ? '' : strtolower($lang->value),
                    'text' => $lang->text,
                ],
                Multilanguage::isEnabled()
                    ? HTMLHelper::_('contentlanguage.existing', true, true)
                    : [],
            ),

            'templates' => static::getTemplates(),

            'categories' => array_map(
                fn($category) => ['value' => (string) $category->value, 'text' => $category->text],
                HTMLHelper::_('category.options', 'com_content'),
            ),

            'root_categories' => array_map(
                fn($category) => ['value' => (string) $category->id, 'text' => $category->title],
                Categories::getInstance('content')->get()->getChildren(),
            ),

            'com_contact.categories' => array_map(
                fn($category) => ['value' => (string) $category->value, 'text' => $category->text],
                HTMLHelper::_('category.options', 'com_contact'),
            ),

            'com_finder.filters' => array_map(
                fn($filter) => ['value' => $filter->value, 'text' => $filter->text],
                $this->getSearchFilters(),
            ),

            'tags' => array_map(
                fn($tag) => ['value' => (string) $tag->value, 'text' => $tag->text],
                HTMLHelper::_('tag.options'),
            ),

            'authors' => array_map(
                fn($user) => ['value' => (string) $user->value, 'text' => $user->text],
                UserHelper::getAuthorList(),
            ),

            'usergroups' => array_map(
                fn($group) => ['value' => (string) $group->value, 'text' => $group->text],
                HTMLHelper::_('user.groups'),
            ),
        ]);
    }

    protected static function getTemplates(): array
    {
        return array_merge(
            [
                'com_content.article' => [
                    'label' => trans('Single Article'),
                    'fieldset' => [
                        'default' => [
                            'fields' => [
                                'catid' => static::getCategoryField(),
                                'include_child_categories' => static::getIncludeChildCategoriesField(
                                    trans(
                                        'The template is only assigned to articles from the selected categories. Use the <kbd>shift</kbd> or <kbd>ctrl/cmd</kbd> key to select multiple categories.',
                                    ),
                                ),
                                'tag' => static::getTagField(),
                                'include_child_tags' => static::getIncludeChildTagsField(
                                    trans(
                                        'The template is only assigned to articles with the selected tags. Use the <kbd>shift</kbd> or <kbd>ctrl/cmd</kbd> key to select multiple tags.',
                                    ),
                                ),
                                'lang' => static::getLanguageField(),
                            ],
                        ],
                    ],
                ],

                'com_content.category' => [
                    'label' => trans('Category Blog'),
                    'fieldset' => [
                        'default' => [
                            'fields' => [
                                'catid' => static::getCategoryField(),
                                'include_child_categories' => static::getIncludeChildCategoriesField(
                                    trans(
                                        'The template is only assigned to the selected categories. Use the <kbd>shift</kbd> or <kbd>ctrl/cmd</kbd> key to select multiple categories.',
                                    ),
                                ),
                                'tag' => static::getTagField(),
                                'include_child_tags' => static::getIncludeChildTagsField(
                                    trans(
                                        'The template is only assigned to categories if the selected tags are set in the menu item. Use the <kbd>shift</kbd> or <kbd>ctrl/cmd</kbd> key to select multiple tags.',
                                    ),
                                ),
                                'pages' => static::getPagesField(),
                                'lang' => static::getLanguageField(),
                            ],
                        ],
                    ],
                ],

                'com_content.featured' => [
                    'label' => trans('Featured Articles'),
                    'fieldset' => [
                        'default' => [
                            'fields' => [
                                'pages' => static::getPagesField(),
                                'lang' => static::getLanguageField(),
                            ],
                        ],
                    ],
                ],

                'com_tags.tag' => [
                    'label' => trans('Tagged Items'),
                    'fieldset' => [
                        'default' => [
                            'fields' => [
                                'tag' => static::getTagField(),
                                'include_child_tags' => static::getIncludeChildTagsField(
                                    trans(
                                        'The template is only assigned to the view if the selected tags are set in the menu item. Use the <kbd>shift</kbd> or <kbd>ctrl/cmd</kbd> key to select multiple tags.',
                                    ),
                                ),
                                'pages' => static::getPagesField(),
                                'lang' => static::getLanguageField(),
                            ],
                        ],
                    ],
                ],

                'com_tags.tags' => [
                    'label' => trans('List All Tags'),
                    'fieldset' => [
                        'default' => [
                            'fields' => [
                                'pages' => static::getPagesField(),
                                'lang' => static::getLanguageField(),
                            ],
                        ],
                    ],
                ],

                'com_contact.contact' => [
                    'label' => trans('Single Contact'),
                    'fieldset' => [
                        'default' => [
                            'fields' => [
                                'catid' => static::getCategoryField('com_contact.categories'),
                                'include_child_categories' => static::getIncludeChildCategoriesField(
                                    trans(
                                        'The template is only assigned to contacts from the selected categories. Use the <kbd>shift</kbd> or <kbd>ctrl/cmd</kbd> key to select multiple categories.',
                                    ),
                                ),
                                'tag' => static::getTagField(),
                                'include_child_tags' => static::getIncludeChildTagsField(
                                    trans(
                                        'The template is only assigned to contacts with the selected tags. Use the <kbd>shift</kbd> or <kbd>ctrl/cmd</kbd> key to select multiple tags.',
                                    ),
                                ),
                                'lang' => static::getLanguageField(),
                            ],
                        ],
                    ],
                ],
            ],
            ComponentHelper::isEnabled('com_search')
                ? [
                    'com_search.search' => [
                        'label' => trans('Search'),
                        'fieldset' => [
                            'default' => [
                                'fields' => [
                                    'lang' => static::getLanguageField(),
                                ],
                            ],
                        ],
                    ],
                ]
                : [],
            [
                'com_finder.search' => [
                    'label' => trans('Smart Search'),
                    'fieldset' => [
                        'default' => [
                            'fields' => [
                                'pages' => static::getPagesField(),
                                'lang' => static::getLanguageField(),
                            ],
                        ],
                    ],
                ],

                '_search' => [
                    'label' => trans('Live Search'),
                    'fieldset' => [
                        'default' => [
                            'fields' => [
                                'lang' => static::getLanguageField(),
                            ],
                        ],
                        'params' => [
                            'fields' => [
                                'live_search_results' => [
                                    'label' => trans('Items per Page'),
                                    'type' => 'number',
                                    'description' => trans('Set the number of items per page.'),
                                    'attrs' => [
                                        'placeholder' => trans('Default'),
                                        'min' => '1',
                                        'max' => LoadSearchTemplate::MAX_ITEMS_PER_PAGE,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],

                'error-404' => [
                    'label' => trans('Error 404'),
                    'fieldset' => [
                        'default' => [
                            'fields' => [
                                'lang' => static::getLanguageField(),
                            ],
                        ],
                    ],
                ],
            ],
        );
    }

    protected function getSearchFilters(): array
    {
        $query = $this->db
            ->getQuery(true)
            ->select('f.title AS text, f.filter_id AS value')
            ->from($this->db->quoteName('#__finder_filters') . ' AS f')
            ->where('f.state = 1')
            ->order('f.title ASC');

        return $this->db->setQuery($query)->loadObjectList();
    }

    protected static function getCategoryField($categories = 'categories'): array
    {
        return [
            'label' => trans('Limit by Categories'),
            'type' => 'select',
            'default' => [],
            'options' => [['evaluate' => "yootheme.builder['{$categories}']"]],
            'attrs' => [
                'multiple' => true,
                'class' => 'uk-height-small',
            ],
        ];
    }

    protected static function getTagField(): array
    {
        return [
            'label' => trans('Limit by Tags'),
            'type' => 'select',
            'default' => [],
            'options' => [['evaluate' => 'yootheme.builder.tags']],
            'attrs' => [
                'multiple' => true,
                'class' => 'uk-height-small',
            ],
        ];
    }

    protected static function getIncludeChildCategoriesField($description): array
    {
        return [
            'type' => 'select',
            'description' => $description,
            'options' => [
                trans('Exclude child categories') => '',
                trans('Include child categories') => 'include',
                trans('Only include child categories') => 'only',
            ],
        ];
    }

    protected static function getIncludeChildTagsField($description): array
    {
        return [
            'type' => 'select',
            'description' => $description,
            'options' => [
                trans('Exclude child tags') => '',
                trans('Include child tags') => 'include',
                trans('Only include child tags') => 'only',
            ],
        ];
    }

    protected static function getLanguageField(): array
    {
        return [
            'label' => trans('Limit by Language'),
            'type' => 'select',
            'defaultIndex' => 0,
            'options' => [['evaluate' => 'yootheme.builder.languages']],
            'show' => 'yootheme.builder.languages.length > 1 || lang',
        ];
    }

    protected static function getPagesField(): array
    {
        return [
            'label' => trans('Limit by Page Number'),
            'description' => trans('The template is only assigned to the selected pages.'),
            'type' => 'select',
            'options' => [
                trans('All pages') => '',
                trans('First page') => 'first',
                trans('All except first page') => 'except_first',
            ],
        ];
    }
}

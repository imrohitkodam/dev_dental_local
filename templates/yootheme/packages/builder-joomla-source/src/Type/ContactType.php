<?php

namespace YOOtheme\Builder\Joomla\Source\Type;

use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\Component\Contact\Site\Helper\RouteHelper;
use YOOtheme\Builder\Joomla\Source\ArticleHelper;
use YOOtheme\Path;
use YOOtheme\View;
use function YOOtheme\app;
use function YOOtheme\trans;

class ContactType
{
    /**
     * @return array
     */
    public static function config()
    {
        return [
            'fields' => [
                'name' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Name'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'image' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Image'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'email_to' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Email'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'con_position' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Contacts Position'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'address' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Address'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'suburb' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('City or Suburb'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'state' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('State or County'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'postcode' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Postal/ZIP Code'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'country' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Country'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'telephone' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Telephone'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'mobile' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Mobile'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'fax' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Fax'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'webpage' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Website'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'text' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Miscellaneous Information'),
                        'filters' => ['limit', 'preserve'],
                    ],
                ],

                'created' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Created Date'),
                        'filters' => ['date'],
                    ],
                ],

                'modified' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Modified Date'),
                        'filters' => ['date'],
                    ],
                ],

                'category' => [
                    'type' => 'Category',
                    'metadata' => [
                        'label' => trans('Category'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::category',
                    ],
                ],

                'user' => [
                    'type' => 'User',
                    'metadata' => [
                        'label' => trans('User'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::user',
                    ],
                ],

                'tagString' => [
                    'type' => 'String',
                    'args' => [
                        'separator' => [
                            'type' => 'String',
                        ],
                        'show_link' => [
                            'type' => 'Boolean',
                        ],
                        'link_style' => [
                            'type' => 'String',
                        ],
                    ],
                    'metadata' => [
                        'label' => trans('Tags'),
                        'arguments' => [
                            'separator' => [
                                'label' => trans('Separator'),
                                'description' => trans('Set the separator between tags.'),
                                'default' => ', ',
                            ],
                            'show_link' => [
                                'label' => trans('Link'),
                                'type' => 'checkbox',
                                'default' => true,
                                'text' => trans('Show link'),
                            ],
                            'link_style' => [
                                'label' => trans('Link Style'),
                                'description' => trans('Set the link style.'),
                                'type' => 'select',
                                'default' => '',
                                'options' => [
                                    'Default' => '',
                                    'Muted' => 'link-muted',
                                    'Text' => 'link-text',
                                    'Heading' => 'link-heading',
                                    'Reset' => 'link-reset',
                                ],
                                'enable' => 'arguments.show_link',
                            ],
                        ],
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::tagString',
                    ],
                ],

                'link' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Link'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::link',
                    ],
                ],

                'articles' => [
                    'type' => [
                        'listOf' => 'Article',
                    ],
                    'metadata' => [
                        'label' => trans('Articles'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::articles',
                    ],
                ],

                'tags' => [
                    'type' => [
                        'listOf' => 'Tag',
                    ],
                    'metadata' => [
                        'label' => trans('Tags'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::tags',
                    ],
                ],

                'hits' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Hits'),
                    ],
                ],

                'alias' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Alias'),
                    ],
                ],

                'id' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('ID'),
                    ],
                ],
            ],

            'metadata' => [
                'type' => true,
                'label' => trans('Contact'),
            ],
        ];
    }

    public static function category($contact)
    {
        return Categories::getInstance('contact', ['countItems' => true])->get($contact->catid);
    }

    public static function user($contact)
    {
        return Factory::getUser($contact->user_id);
    }

    public static function tags($contact)
    {
        return $contact->tags->itemTags ??
            (new TagsHelper())->getItemTags('com_contact.contact', $contact->id);
    }

    public static function tagString($contact, array $args)
    {
        $tags = static::tags($contact);
        $args += ['separator' => ', ', 'show_link' => true, 'link_style' => ''];

        return app(View::class)->render(
            Path::get('../../templates/tags', __DIR__),
            compact('tags', 'args'),
        );
    }

    public static function link($contact)
    {
        return RouteHelper::getContactRoute($contact->id, $contact->catid, $contact->language);
    }

    public static function articles($contact)
    {
        if (empty($contact->articles)) {
            return;
        }

        $ids = array_column($contact->articles, 'id');
        $articles = ArticleHelper::get($ids);

        usort($articles, fn($a, $b) => array_search($a->id, $ids) - array_search($b->id, $ids));

        return $articles;
    }
}

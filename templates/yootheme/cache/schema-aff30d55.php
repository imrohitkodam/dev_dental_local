<?php

return [
  'kind' => 'Document', 
  'definitions' => [[
      'kind' => 'DirectiveDefinition', 
      'name' => [
        'kind' => 'Name', 
        'value' => 'bind'
      ], 
      'arguments' => [[
          'kind' => 'InputValueDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'id'
          ], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'InputValueDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'class'
          ], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'InputValueDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'args'
          ], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ]], 
      'repeatable' => false, 
      'locations' => [[
          'kind' => 'Name', 
          'value' => 'OBJECT'
        ], [
          'kind' => 'Name', 
          'value' => 'ENUM_VALUE'
        ], [
          'kind' => 'Name', 
          'value' => 'FIELD_DEFINITION'
        ]]
    ], [
      'kind' => 'DirectiveDefinition', 
      'name' => [
        'kind' => 'Name', 
        'value' => 'call'
      ], 
      'arguments' => [[
          'kind' => 'InputValueDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'func'
          ], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'InputValueDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'args'
          ], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ]], 
      'repeatable' => false, 
      'locations' => [[
          'kind' => 'Name', 
          'value' => 'ENUM_VALUE'
        ], [
          'kind' => 'Name', 
          'value' => 'FIELD_DEFINITION'
        ]]
    ], [
      'kind' => 'DirectiveDefinition', 
      'name' => [
        'kind' => 'Name', 
        'value' => 'slice'
      ], 
      'arguments' => [[
          'kind' => 'InputValueDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'offset'
          ], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'Int'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'InputValueDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'limit'
          ], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'Int'
            ]
          ], 
          'directives' => []
        ]], 
      'repeatable' => false, 
      'locations' => [[
          'kind' => 'Name', 
          'value' => 'FIELD'
        ], [
          'kind' => 'Name', 
          'value' => 'FRAGMENT_SPREAD'
        ], [
          'kind' => 'Name', 
          'value' => 'INLINE_FRAGMENT'
        ]]
    ], [
      'kind' => 'ObjectTypeDefinition', 
      'name' => [
        'kind' => 'Name', 
        'value' => 'Query'
      ], 
      'interfaces' => [], 
      'directives' => [], 
      'fields' => [[
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'search'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'Search'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Search\\Type\\SearchQueryType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'searchItems'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'offset'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'limit'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'ListType', 
            'type' => [
              'kind' => 'NamedType', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'SearchItem'
              ]
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Search\\Type\\SearchItemsQueryType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'article'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'Article'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ArticleQueryType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'prevArticle'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'Article'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ArticleQueryType::resolvePreviousArticle', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'nextArticle'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'Article'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ArticleQueryType::resolveNextArticle', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'category'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'Category'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\CategoryQueryType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'contact'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'Contact'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ContactQueryType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'articlesSingle'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'offset'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'Article'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ArticlesQueryType::resolveSingle', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'articles'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'offset'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'limit'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'ListType', 
            'type' => [
              'kind' => 'NamedType', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'Article'
              ]
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ArticlesQueryType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'smartSearch'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'SmartSearch'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\SmartSearchQueryType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'smartSearchItem'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'catid'
              ], 
              'type' => [
                'kind' => 'ListType', 
                'type' => [
                  'kind' => 'NamedType', 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'String'
                  ]
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'offset'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'SmartSearchItem'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\SmartSearchItemsQueryType::resolveSingle', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'smartSearchItems'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'catid'
              ], 
              'type' => [
                'kind' => 'ListType', 
                'type' => [
                  'kind' => 'NamedType', 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'String'
                  ]
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'offset'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'limit'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'ListType', 
            'type' => [
              'kind' => 'NamedType', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'SmartSearchItem'
              ]
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\SmartSearchItemsQueryType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'tagsSingle'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'offset'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'Tag'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\TagsQueryType::resolveSingle', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'tags'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'offset'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'limit'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'ListType', 
            'type' => [
              'kind' => 'NamedType', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'Tag'
              ]
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\TagsQueryType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'tagItemsSingle'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'offset'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'TagItem'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\TagItemsQueryType::resolveSingle', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'tagItems'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'offset'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'limit'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'ListType', 
            'type' => [
              'kind' => 'NamedType', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'TagItem'
              ]
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\TagItemsQueryType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'customArticle'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'id'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'catid'
              ], 
              'type' => [
                'kind' => 'ListType', 
                'type' => [
                  'kind' => 'NamedType', 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'String'
                  ]
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'include_child_categories'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'cat_operator'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'tags'
              ], 
              'type' => [
                'kind' => 'ListType', 
                'type' => [
                  'kind' => 'NamedType', 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'String'
                  ]
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'include_child_tags'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'tag_operator'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'users'
              ], 
              'type' => [
                'kind' => 'ListType', 
                'type' => [
                  'kind' => 'NamedType', 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'String'
                  ]
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'users_operator'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'featured'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'offset'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'order'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'order_direction'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'order_alphanum'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Boolean'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'Article'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\CustomArticleQueryType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'customArticles'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'catid'
              ], 
              'type' => [
                'kind' => 'ListType', 
                'type' => [
                  'kind' => 'NamedType', 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'String'
                  ]
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'cat_operator'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'include_child_categories'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'tags'
              ], 
              'type' => [
                'kind' => 'ListType', 
                'type' => [
                  'kind' => 'NamedType', 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'String'
                  ]
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'tag_operator'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'include_child_tags'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'users'
              ], 
              'type' => [
                'kind' => 'ListType', 
                'type' => [
                  'kind' => 'NamedType', 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'String'
                  ]
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'users_operator'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'featured'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'offset'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'limit'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'order'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'order_direction'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'order_alphanum'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Boolean'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'ListType', 
            'type' => [
              'kind' => 'NamedType', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'Article'
              ]
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\CustomArticlesQueryType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'customCategory'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'id'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'Category'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\CustomCategoryQueryType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'customCategories'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'catid'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'offset'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'limit'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'order'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'order_direction'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'ListType', 
            'type' => [
              'kind' => 'NamedType', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'Category'
              ]
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\CustomCategoriesQueryType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'customTag'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'id'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'Tag'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\CustomTagQueryType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'customTags'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'parent_id'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'offset'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'limit'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'order'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'order_direction'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'ListType', 
            'type' => [
              'kind' => 'NamedType', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'Tag'
              ]
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\CustomTagsQueryType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'customMenuItem'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'menu'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'id'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'MenuItem'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\CustomMenuItemQueryType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'customMenuItems'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'id'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'parent'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'heading'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'include_heading'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Boolean'
                ]
              ], 
              'defaultValue' => [
                'kind' => 'BooleanValue', 
                'value' => true
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'ids'
              ], 
              'type' => [
                'kind' => 'ListType', 
                'type' => [
                  'kind' => 'NamedType', 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'String'
                  ]
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'ListType', 
            'type' => [
              'kind' => 'NamedType', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'MenuItem'
              ]
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\CustomMenuItemsQueryType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'customUser'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'id'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'User'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\CustomUserQueryType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'customUsers'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'groups'
              ], 
              'type' => [
                'kind' => 'ListType', 
                'type' => [
                  'kind' => 'NamedType', 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'String'
                  ]
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'offset'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'limit'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'order'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'order_direction'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'ListType', 
            'type' => [
              'kind' => 'NamedType', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'User'
              ]
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\CustomUsersQueryType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'site'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'Site'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\SiteQueryType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'file'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'pattern'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'offset'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'order'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'order_direction'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'File'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Source\\Filesystem\\Type\\FileQueryType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'files'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'pattern'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'offset'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'limit'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'order'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'order_direction'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'ListType', 
            'type' => [
              'kind' => 'NamedType', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'File'
              ]
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Source\\Filesystem\\Type\\FilesQueryType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ]]
    ], [
      'kind' => 'ObjectTypeDefinition', 
      'name' => [
        'kind' => 'Name', 
        'value' => 'Search'
      ], 
      'interfaces' => [], 
      'directives' => [], 
      'fields' => [[
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'searchword'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'total'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'Int'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'error'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ]]
    ], [
      'kind' => 'ObjectTypeDefinition', 
      'name' => [
        'kind' => 'Name', 
        'value' => 'SearchItem'
      ], 
      'interfaces' => [], 
      'directives' => [], 
      'fields' => [[
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'title'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'text'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'created'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Search\\Type\\SearchItemType::created', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'category'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'Category'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Search\\Type\\SearchItemType::category', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'href'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ]]
    ], [
      'kind' => 'ObjectTypeDefinition', 
      'name' => [
        'kind' => 'Name', 
        'value' => 'Category'
      ], 
      'interfaces' => [], 
      'directives' => [], 
      'fields' => [[
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'title'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'description'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'params'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'CategoryParams'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\CategoryType::params', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'link'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\CategoryType::link', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'parent'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'Category'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\CategoryType::parent', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'tagString'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'parent_id'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'separator'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'show_link'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Boolean'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'link_style'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\CategoryType::tagString', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'categories'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'ListType', 
            'type' => [
              'kind' => 'NamedType', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'Category'
              ]
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\CategoryType::categories', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'articles'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'subcategories'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Boolean'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'offset'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'limit'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'order'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'order_direction'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'order_alphanum'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Boolean'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'ListType', 
            'type' => [
              'kind' => 'NamedType', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'Article'
              ]
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\CategoryType::articles', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'tags'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'parent_id'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'ListType', 
            'type' => [
              'kind' => 'NamedType', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'Tag'
              ]
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\CategoryType::tags', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'numitems'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'alias'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'id'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ]]
    ], [
      'kind' => 'ObjectTypeDefinition', 
      'name' => [
        'kind' => 'Name', 
        'value' => 'CategoryParams'
      ], 
      'interfaces' => [], 
      'directives' => [], 
      'fields' => [[
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'image'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'image_alt'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ]]
    ], [
      'kind' => 'ObjectTypeDefinition', 
      'name' => [
        'kind' => 'Name', 
        'value' => 'Article'
      ], 
      'interfaces' => [], 
      'directives' => [], 
      'fields' => [[
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'title'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'content'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'show_intro_text'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Boolean'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ArticleType::content', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'teaser'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'show_excerpt'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Boolean'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'show_content'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Boolean'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ArticleType::teaser', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'publish_up'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'created'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'modified'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'featured'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'Boolean'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'metaString'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'format'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'separator'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'link_style'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'show_publish_date'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Boolean'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'show_author'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Boolean'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'show_taxonomy'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'parent_id'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'date_format'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ArticleType::metaString', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'category'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'Category'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ArticleType::category', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'tagString'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'parent_id'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'separator'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'show_link'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Boolean'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'link_style'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ArticleType::tagString', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'images'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'ArticleImages'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ArticleType::images', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'link'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ArticleType::link', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'author'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'User'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ArticleType::author', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'hits'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'rating'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'rating_count'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'urls'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'ArticleUrls'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ArticleType::urls', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'event'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'ArticleEvent'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ArticleType::event', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'tags'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'parent_id'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'ListType', 
            'type' => [
              'kind' => 'NamedType', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'Tag'
              ]
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ArticleType::tags', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'alias'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'id'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'relatedArticles'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'category'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'tags'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'author'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'offset'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'limit'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'order'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'order_direction'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'order_alphanum'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Boolean'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'ListType', 
            'type' => [
              'kind' => 'NamedType', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'Article'
              ]
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ArticleType::relatedArticles', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ]]
    ], [
      'kind' => 'ObjectTypeDefinition', 
      'name' => [
        'kind' => 'Name', 
        'value' => 'ArticleImages'
      ], 
      'interfaces' => [], 
      'directives' => [], 
      'fields' => [[
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'image_intro'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ImagesType::image', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'image_intro_alt'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'image_intro_caption'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'image_fulltext'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ImagesType::image', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'image_fulltext_alt'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'image_fulltext_caption'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ]]
    ], [
      'kind' => 'ObjectTypeDefinition', 
      'name' => [
        'kind' => 'Name', 
        'value' => 'User'
      ], 
      'interfaces' => [], 
      'directives' => [], 
      'fields' => [[
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'name'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'username'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'email'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'registerDate'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'lastvisitDate'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'link'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\UserType::link', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'userGroupString'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'separator'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\UserType::userGroupString', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'id'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ]]
    ], [
      'kind' => 'ObjectTypeDefinition', 
      'name' => [
        'kind' => 'Name', 
        'value' => 'ArticleUrls'
      ], 
      'interfaces' => [], 
      'directives' => [], 
      'fields' => [[
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'urla'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ArticleUrlsType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'urlatext'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'urlb'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ArticleUrlsType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'urlbtext'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'urlc'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ArticleUrlsType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'urlctext'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ]]
    ], [
      'kind' => 'ObjectTypeDefinition', 
      'name' => [
        'kind' => 'Name', 
        'value' => 'ArticleEvent'
      ], 
      'interfaces' => [], 
      'directives' => [], 
      'fields' => [[
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'afterDisplayTitle'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ArticleEventType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'beforeDisplayContent'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ArticleEventType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'afterDisplayContent'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ArticleEventType::resolve', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ]]
    ], [
      'kind' => 'ObjectTypeDefinition', 
      'name' => [
        'kind' => 'Name', 
        'value' => 'Tag'
      ], 
      'interfaces' => [], 
      'directives' => [], 
      'fields' => [[
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'title'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'description'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'images'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'Images'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\TagType::images', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'hits'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'link'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\TagType::link', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'tags'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'ListType', 
            'type' => [
              'kind' => 'NamedType', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'Tag'
              ]
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\TagType::tags', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'items'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'include_children'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Boolean'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'typesr'
              ], 
              'type' => [
                'kind' => 'ListType', 
                'type' => [
                  'kind' => 'NamedType', 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'String'
                  ]
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'offset'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'limit'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Int'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'order'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'order_direction'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'order_alphanum'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Boolean'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'ListType', 
            'type' => [
              'kind' => 'NamedType', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'TagItem'
              ]
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\TagType::items', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'alias'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'id'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ]]
    ], [
      'kind' => 'ObjectTypeDefinition', 
      'name' => [
        'kind' => 'Name', 
        'value' => 'Images'
      ], 
      'interfaces' => [], 
      'directives' => [], 
      'fields' => [[
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'image_intro'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ImagesType::image', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'image_intro_alt'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'image_intro_caption'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'image_fulltext'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ImagesType::image', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'image_fulltext_alt'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'image_fulltext_caption'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ]]
    ], [
      'kind' => 'ObjectTypeDefinition', 
      'name' => [
        'kind' => 'Name', 
        'value' => 'TagItem'
      ], 
      'interfaces' => [], 
      'directives' => [], 
      'fields' => [[
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'core_title'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'content'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\TagItemType::content', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'teaser'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'show_excerpt'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Boolean'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\TagItemType::teaser', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'core_publish_up'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'core_created_time'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'core_modified_time'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'metaString'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'format'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'separator'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'link_style'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'show_publish_date'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Boolean'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'show_author'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Boolean'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'show_taxonomy'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'parent_id'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'date_format'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\TagItemType::metaString', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'category'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'Category'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\TagItemType::category', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'images'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'Images'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\TagItemType::images', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'link'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'author'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'User'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\TagItemType::author', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'event'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'ArticleEvent'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\TagItemType::event', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'content_type_title'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'core_alias'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'id'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ]]
    ], [
      'kind' => 'ObjectTypeDefinition', 
      'name' => [
        'kind' => 'Name', 
        'value' => 'Contact'
      ], 
      'interfaces' => [], 
      'directives' => [], 
      'fields' => [[
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'name'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'image'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'email_to'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'con_position'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'address'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'suburb'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'state'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'postcode'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'country'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'telephone'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'mobile'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'fax'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'webpage'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'text'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'created'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'modified'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'category'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'Category'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ContactType::category', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'user'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'User'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ContactType::user', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'tagString'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'separator'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'show_link'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Boolean'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'link_style'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ContactType::tagString', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'link'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ContactType::link', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'articles'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'ListType', 
            'type' => [
              'kind' => 'NamedType', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'Article'
              ]
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ContactType::articles', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'tags'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'ListType', 
            'type' => [
              'kind' => 'NamedType', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'Tag'
              ]
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\ContactType::tags', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'hits'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'alias'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'id'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ]]
    ], [
      'kind' => 'ObjectTypeDefinition', 
      'name' => [
        'kind' => 'Name', 
        'value' => 'SmartSearch'
      ], 
      'interfaces' => [], 
      'directives' => [], 
      'fields' => [[
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'searchword'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'total'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'Int'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'link'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\SmartSearchType::link', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ]]
    ], [
      'kind' => 'ObjectTypeDefinition', 
      'name' => [
        'kind' => 'Name', 
        'value' => 'SmartSearchItem'
      ], 
      'interfaces' => [], 
      'directives' => [], 
      'fields' => [[
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'title'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'description'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'publish_start_date'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'metaString'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'format'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'separator'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'link_style'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'show_publish_date'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Boolean'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'show_author'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Boolean'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'show_taxonomy'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'parent_id'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ], [
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'date_format'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'String'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\SmartSearchItemType::metaString', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'category'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'Category'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\SmartSearchItemType::category', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'images'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'ArticleImages'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\SmartSearchItemType::images', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'route'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'author'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'User'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\SmartSearchItemType::author', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ]]
    ], [
      'kind' => 'ObjectTypeDefinition', 
      'name' => [
        'kind' => 'Name', 
        'value' => 'MenuItem'
      ], 
      'interfaces' => [], 
      'directives' => [], 
      'fields' => [[
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'title'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'image'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\MenuItemType::data', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'icon'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\MenuItemType::data', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'subtitle'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\MenuItemType::data', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'link'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\MenuItemType::link', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'active'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'Boolean'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\MenuItemType::active', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'type'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\MenuItemType::type', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'parent'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'MenuItem'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\MenuItemType::parent', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'children'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'ListType', 
            'type' => [
              'kind' => 'NamedType', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'MenuItem'
              ]
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Joomla\\Source\\Type\\MenuItemType::children', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'alias'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'id'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ]]
    ], [
      'kind' => 'ObjectTypeDefinition', 
      'name' => [
        'kind' => 'Name', 
        'value' => 'Site'
      ], 
      'interfaces' => [], 
      'directives' => [], 
      'fields' => [[
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'title'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'page_title'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'page_locale'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Source\\Type\\SiteType::resolvePageLocale', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'page_url'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'query'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Boolean'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Source\\Type\\SiteType::resolvePageUrl', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'is_guest'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'Int'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'user'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'User'
            ]
          ], 
          'directives' => []
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'request'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'Request'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Source\\Type\\SiteType::resolveRequest', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ]]
    ], [
      'kind' => 'ObjectTypeDefinition', 
      'name' => [
        'kind' => 'Name', 
        'value' => 'Request'
      ], 
      'interfaces' => [], 
      'directives' => [], 
      'fields' => [[
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'url'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Source\\Type\\RequestType::resolveUrl', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'method'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Source\\Type\\RequestType::resolveMethod', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'scheme'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Source\\Type\\RequestType::resolveScheme', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'host'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Source\\Type\\RequestType::resolveHost', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'port'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Source\\Type\\RequestType::resolvePort', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'path'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Source\\Type\\RequestType::resolvePath', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'query'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Source\\Type\\RequestType::resolveQuery', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ]]
    ], [
      'kind' => 'ObjectTypeDefinition', 
      'name' => [
        'kind' => 'Name', 
        'value' => 'File'
      ], 
      'interfaces' => [], 
      'directives' => [], 
      'fields' => [[
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'name'
          ], 
          'arguments' => [[
              'kind' => 'InputValueDefinition', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'title_case'
              ], 
              'type' => [
                'kind' => 'NamedType', 
                'name' => [
                  'kind' => 'Name', 
                  'value' => 'Boolean'
                ]
              ], 
              'directives' => []
            ]], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Source\\Filesystem\\Type\\FileType::name', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'basename'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Source\\Filesystem\\Type\\FileType::basename', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'dirname'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Source\\Filesystem\\Type\\FileType::dirname', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'url'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Source\\Filesystem\\Type\\FileType::url', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'path'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Source\\Filesystem\\Type\\FileType::path', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'content'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Source\\Filesystem\\Type\\FileType::content', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'size'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Source\\Filesystem\\Type\\FileType::size', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'extension'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Source\\Filesystem\\Type\\FileType::extension', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'mimetype'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Source\\Filesystem\\Type\\FileType::mimetype', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'accessed'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Source\\Filesystem\\Type\\FileType::accessed', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'changed'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Source\\Filesystem\\Type\\FileType::changed', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ], [
          'kind' => 'FieldDefinition', 
          'name' => [
            'kind' => 'Name', 
            'value' => 'modified'
          ], 
          'arguments' => [], 
          'type' => [
            'kind' => 'NamedType', 
            'name' => [
              'kind' => 'Name', 
              'value' => 'String'
            ]
          ], 
          'directives' => [[
              'kind' => 'Directive', 
              'name' => [
                'kind' => 'Name', 
                'value' => 'call'
              ], 
              'arguments' => [[
                  'kind' => 'Argument', 
                  'value' => [
                    'kind' => 'StringValue', 
                    'value' => 'YOOtheme\\Builder\\Source\\Filesystem\\Type\\FileType::modified', 
                    'block' => false
                  ], 
                  'name' => [
                    'kind' => 'Name', 
                    'value' => 'func'
                  ]
                ]]
            ]]
        ]]
    ], [
      'kind' => 'ScalarTypeDefinition', 
      'name' => [
        'kind' => 'Name', 
        'value' => 'Object'
      ], 
      'directives' => []
    ]]
];
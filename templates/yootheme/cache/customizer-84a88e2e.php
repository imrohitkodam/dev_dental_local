<?php // $file = /home/customer/www/dev.dental-channel.co.uk/public_html/templates/yootheme/packages/builder-templates/config/customizer.json

return [
  'sections' => [
    'builder-templates' => [
      'title' => 'Templates', 
      'priority' => 25, 
      'views' => $config->get('theme.views'), 
      'fieldset' => [
        'default' => [
          'fields' => [
            'name' => [
              'label' => 'Name', 
              'description' => 'Define a name to easily identify the template.', 
              'attrs' => [
                'required' => true, 
                'autofocus' => true
              ]
            ], 
            'status' => [
              'label' => 'Status', 
              'description' => 'Disable the template and publish it later.', 
              'type' => 'checkbox', 
              'text' => 'Disable template', 
              'attrs' => [
                'true-value' => 'disabled', 
                'false-value' => ''
              ]
            ], 
            'type' => [
              'label' => 'Page', 
              'description' => 'Choose the page to which the template is assigned.', 
              'type' => 'select', 
              'default' => '', 
              'options' => [[
                  'text' => 'None', 
                  'value' => ''
                ], [
                  'evaluate' => 'yootheme.builder.templateOptions'
                ]]
            ]
          ]
        ]
      ]
    ]
  ]
];

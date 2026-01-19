<?php // $file = /var/www/ttpl-rt-234-php82.local/public/dev_dental_1/templates/yootheme/packages/builder/elements/list_item/element.json

return [
  '@import' => $filter->apply('path', './element.php', $file), 
  'name' => 'list_item', 
  'title' => 'Item', 
  'width' => 500, 
  'placeholder' => [
    'props' => [
      'content' => 'Lorem ipsum dolor sit amet.', 
      'image' => '', 
      'icon' => ''
    ]
  ], 
  'templates' => [
    'render' => $filter->apply('path', './templates/template.php', $file), 
    'content' => $filter->apply('path', './templates/content.php', $file)
  ], 
  'fields' => [
    'content' => [
      'label' => 'Content', 
      'type' => 'editor', 
      'root' => true, 
      'source' => true
    ], 
    'image' => $config->get('builder.image'), 
    'image_alt' => $config->get('builder.image_alt'), 
    'icon' => [
      'label' => 'Icon', 
      'description' => 'Instead of using a custom image, you can click on the pencil to pick an icon from the icon library.', 
      'type' => 'icon', 
      'source' => true, 
      'enable' => '!image'
    ], 
    'image_focal_point' => [
      'label' => 'Focal Point', 
      'description' => 'Set a focal point to adjust the image focus when cropping.', 
      'type' => 'select', 
      'options' => [
        'Top Left' => 'top-left', 
        'Top Center' => 'top-center', 
        'Top Right' => 'top-right', 
        'Center Left' => 'center-left', 
        'Center Center' => '', 
        'Center Right' => 'center-right', 
        'Bottom Left' => 'bottom-left', 
        'Bottom Center' => 'bottom-center', 
        'Bottom Right' => 'bottom-right'
      ], 
      'source' => true, 
      'enable' => 'image'
    ], 
    'icon_color' => [
      'label' => 'Icon Color', 
      'description' => 'Set the icon color.', 
      'type' => 'select', 
      'options' => [
        'None' => '', 
        'Muted' => 'muted', 
        'Emphasis' => 'emphasis', 
        'Primary' => 'primary', 
        'Secondary' => 'secondary', 
        'Success' => 'success', 
        'Warning' => 'warning', 
        'Danger' => 'danger'
      ], 
      'enable' => '!image'
    ], 
    'link' => $config->get('builder.link'), 
    'link_target' => $config->get('builder.link_target'), 
    'status' => $config->get('builder.statusItem'), 
    'source' => $config->get('builder.source')
  ], 
  'fieldset' => [
    'default' => [
      'type' => 'tabs', 
      'fields' => [[
          'title' => 'Content', 
          'fields' => ['content', 'image', 'image_alt', 'icon', 'link', 'link_target']
        ], [
          'title' => 'Settings', 
          'fields' => [[
              'label' => 'Image', 
              'type' => 'group', 
              'divider' => true, 
              'fields' => ['image_focal_point']
            ], [
              'label' => 'Icon', 
              'type' => 'group', 
              'fields' => ['icon_color']
            ]]
        ], $config->get('builder.advancedItem')]
    ]
  ]
];

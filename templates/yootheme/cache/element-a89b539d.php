<?php // $file = /var/www/ttpl-rt-234-php82.local/public/dev_dental_1/templates/yootheme/packages/builder/elements/nav_item/element.json

return [
  '@import' => $filter->apply('path', './element.php', $file), 
  'name' => 'nav_item', 
  'title' => 'Item', 
  'width' => 500, 
  'placeholder' => [
    'props' => [
      'content' => 'Item'
    ]
  ], 
  'updates' => $filter->apply('path', './updates.php', $file), 
  'templates' => [
    'render' => $filter->apply('path', './templates/template.php', $file), 
    'content' => $filter->apply('path', './templates/content.php', $file)
  ], 
  'fields' => [
    'content' => [
      'label' => 'Content', 
      'source' => true
    ], 
    'meta' => [
      'label' => 'Subtitle', 
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
    'link' => [
      'label' => 'Link', 
      'type' => 'link', 
      'description' => 'Enter or pick a link, an image or a video file.', 
      'attrs' => [
        'placeholder' => 'http://'
      ], 
      'source' => true
    ], 
    'link_target' => [
      'type' => 'checkbox', 
      'text' => 'Open the link in a new window'
    ], 
    'type' => [
      'label' => 'Type', 
      'description' => 'Select the item type.', 
      'type' => 'select', 
      'options' => [
        'Item' => '', 
        'Heading' => 'heading', 
        'Divider' => 'divider'
      ], 
      'source' => true
    ], 
    'active' => [
      'label' => 'Active', 
      'description' => 'Highlight the item as the active item.', 
      'type' => 'checkbox', 
      'text' => 'Enable active state', 
      'source' => true, 
      'enable' => '!$match(type, \'divider|header\')'
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
    'status' => $config->get('builder.statusItem'), 
    'source' => $config->get('builder.source')
  ], 
  'fieldset' => [
    'default' => [
      'type' => 'tabs', 
      'fields' => [[
          'title' => 'Content', 
          'fields' => ['content', 'meta', 'image', 'image_alt', 'icon', 'link', 'link_target']
        ], [
          'title' => 'Settings', 
          'fields' => [[
              'label' => 'Item', 
              'type' => 'group', 
              'divider' => true, 
              'fields' => ['type', 'active']
            ], [
              'label' => 'Image', 
              'type' => 'group', 
              'fields' => ['image_focal_point']
            ]]
        ], $config->get('builder.advancedItem')]
    ]
  ]
];

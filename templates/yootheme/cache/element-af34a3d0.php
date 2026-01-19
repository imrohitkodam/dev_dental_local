<?php // $file = /var/www/ttpl-rt-234-php82.local/public/dev_dental_1/templates/yootheme/packages/builder/elements/popover_item/element.json

return [
  '@import' => $filter->apply('path', './element.php', $file), 
  'name' => 'popover_item', 
  'title' => 'Item', 
  'width' => 500, 
  'placeholder' => [
    'props' => [
      'title' => 'Title', 
      'meta' => '', 
      'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', 
      'image' => ''
    ]
  ], 
  'templates' => [
    'render' => $filter->apply('path', './templates/template.php', $file), 
    'content' => $filter->apply('path', './templates/content.php', $file)
  ], 
  'fields' => [
    'title' => [
      'label' => 'Title', 
      'source' => true
    ], 
    'meta' => [
      'label' => 'Meta', 
      'source' => true
    ], 
    'content' => [
      'label' => 'Content', 
      'type' => 'editor', 
      'source' => true
    ], 
    'image' => $config->get('builder.image'), 
    'image_alt' => $config->get('builder.image_alt'), 
    'link' => $config->get('builder.link'), 
    'link_text' => [
      'label' => 'Link Text', 
      'description' => 'Set a different link text for this item.', 
      'source' => true, 
      'enable' => 'link'
    ], 
    'link_aria_label' => [
      'label' => 'Link ARIA Label', 
      'description' => 'Set a different link ARIA label for this item.', 
      'source' => true, 
      'enable' => 'link'
    ], 
    'position_x' => [
      'label' => 'Left', 
      'description' => 'Enter the horizontal position of the marker in percent.', 
      'type' => 'range', 
      'attrs' => [
        'min' => 0, 
        'max' => 100, 
        'step' => 1
      ], 
      'source' => true
    ], 
    'position_y' => [
      'label' => 'Top', 
      'description' => 'Enter the vertical position of the marker in percent.', 
      'type' => 'range', 
      'attrs' => [
        'min' => 0, 
        'max' => 100, 
        'step' => 1
      ], 
      'source' => true
    ], 
    'drop_position' => [
      'label' => 'Position', 
      'description' => 'Select a different position for this item.', 
      'type' => 'select', 
      'options' => [
        'None' => '', 
        'Top' => 'top-center', 
        'Bottom' => 'bottom-center', 
        'Left' => 'left-center', 
        'Right' => 'right-center'
      ], 
      'source' => true
    ], 
    'item_element' => $config->get('builder.html_element_item'), 
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
          'fields' => ['title', 'meta', 'content', 'image', 'image_alt', 'link', 'link_text', 'link_aria_label']
        ], [
          'title' => 'Settings', 
          'fields' => [[
              'label' => 'Marker', 
              'type' => 'group', 
              'divider' => true, 
              'fields' => ['position_x', 'position_y']
            ], [
              'label' => 'Popover', 
              'type' => 'group', 
              'divider' => true, 
              'fields' => ['drop_position', 'item_element']
            ], [
              'label' => 'Image', 
              'type' => 'group', 
              'fields' => ['image_focal_point']
            ]]
        ], $config->get('builder.advancedItem')]
    ]
  ]
];

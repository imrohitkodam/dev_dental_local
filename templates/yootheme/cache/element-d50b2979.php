<?php // $file = /home/customer/www/dev.dental-channel.co.uk/public_html/templates/yootheme/packages/builder/elements/map_item/element.json

return [
  '@import' => $filter->apply('path', './element.php', $file), 
  'name' => 'map_item', 
  'title' => 'Item', 
  'width' => 500, 
  'placeholder' => [
    'props' => [
      'location' => '53.5503, 10.0006'
    ]
  ], 
  'templates' => [
    'render' => $filter->apply('path', './templates/template.php', $file), 
    'content' => $filter->apply('path', './templates/content.php', $file)
  ], 
  'fields' => [
    'location' => [
      'label' => 'Location', 
      'type' => 'location', 
      'source' => true
    ], 
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
    'marker_icon' => [
      'label' => 'Marker Icon', 
      'type' => 'image', 
      'source' => true
    ], 
    'marker_icon_width' => [
      'label' => 'Width', 
      'type' => 'number', 
      'attrs' => [
        'placeholder' => 'auto'
      ], 
      'enable' => 'marker_icon'
    ], 
    'marker_icon_height' => [
      'label' => 'Height', 
      'type' => 'number', 
      'attrs' => [
        'placeholder' => 'auto'
      ], 
      'enable' => 'marker_icon'
    ], 
    'hide' => [
      'label' => 'Marker', 
      'type' => 'checkbox', 
      'text' => 'Hide marker'
    ], 
    'show_popup' => [
      'label' => 'Behavior', 
      'type' => 'checkbox', 
      'text' => 'Show popup on load'
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
          'fields' => ['location', 'title', 'meta', 'content', 'image', 'image_alt', 'link', 'link_text', 'link_aria_label', 'marker_icon', [
              'description' => 'Setting just one value preserves the original proportions. The image will be resized and cropped automatically, and where possible, high resolution images will be auto-generated.', 
              'name' => '_marker_dimension', 
              'type' => 'grid', 
              'width' => '1-2', 
              'fields' => ['marker_icon_width', 'marker_icon_height']
            ]]
        ], [
          'title' => 'Settings', 
          'fields' => [[
              'label' => 'Marker', 
              'type' => 'group', 
              'divider' => true, 
              'fields' => ['hide']
            ], [
              'label' => 'Popup', 
              'type' => 'group', 
              'divider' => true, 
              'fields' => ['show_popup', 'item_element']
            ], [
              'label' => 'Image', 
              'type' => 'group', 
              'fields' => ['image_focal_point']
            ]]
        ], $config->get('builder.advancedItem')]
    ]
  ]
];

<?php // $file = /home/customer/www/dev.dental-channel.co.uk/public_html/templates/yootheme/packages/builder/elements/layout/element.json

return [
  'name' => 'layout', 
  'title' => 'Layout', 
  'container' => true, 
  'updates' => $filter->apply('path', './updates.php', $file), 
  'templates' => [
    'render' => $filter->apply('path', './templates/template.php', $file), 
    'content' => $filter->apply('path', './templates/content.php', $file)
  ]
];

<?php

namespace YOOtheme;

use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

$view = app(View::class);

if (!$params->get('showLast', 1)) {
    array_pop($list);
} elseif ($list) {
    $list[count($list) - 1]->link = '';
}

echo $view('~theme/templates/breadcrumbs', [
    'items' => $list,
    'schemaorgId' => Uri::root() . '#/schema/BreadcrumbList/' . (int) $module->id
]);

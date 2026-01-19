<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die();

if (!Factory::getUser()->authorise('core.edit', 'com_templates')) {
    return;
}

$query = "SELECT * FROM #__template_styles WHERE client_id=0 AND home='1'";
if (!($templ = Factory::getDbo()->setQuery($query)->loadObject())) {
    return;
}

$templ->params = new Registry($templ->params);

if (!$templ->params->get('yootheme')) {
    return;
}

if (!PluginHelper::isEnabled('system', 'yootheme')) {
    return;
}

$app->getDocument()->addStyleSheet(
    Uri::root(true) . '/administrator/modules/mod_yootheme_link/assets/icon.css',
);

require ModuleHelper::getLayoutPath(
    'mod_yootheme_link',
    $params->get('layout', 'default') . (version_compare(JVERSION, '4.0', '<') ? '_legacy' : ''),
);

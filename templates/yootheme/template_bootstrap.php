<?php

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;

$options = ['com_ajax', 'com_content', 'com_templates', 'com_modules', 'com_advancedmodules'];

if (
    Factory::getApplication()->isClient('site') ||
    in_array(ApplicationHelper::getComponentName(), $options, true)
) {
    // bootstrap application
    $app = require __DIR__ . '/bootstrap.php';
    $app->load(
        __DIR__ .
            '/{packages/{platform-joomla,' .
            'theme{,-analytics,-cookie,-highlight,-settings},' .
            'builder{,-source*,-templates,-newsletter},' .
            'styler,theme-joomla*,builder-joomla*}' .
            '/bootstrap.php,config.php}',
    );
}

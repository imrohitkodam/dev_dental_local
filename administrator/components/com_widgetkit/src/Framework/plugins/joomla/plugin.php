<?php

$config = array(

    'name' => 'framework/joomla',

    'main' => 'YOOtheme\\Widgetkit\\Framework\\Joomla\\JoomlaPlugin',

    'autoload' => array(

        'YOOtheme\\Widgetkit\\Framework\\Joomla\\' => 'src'

    )

);

return defined('_JEXEC') ? $config : false;

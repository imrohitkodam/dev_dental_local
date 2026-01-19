<?php

defined('_JEXEC') or die;

class plgSystemWidgetkit extends JPlugin
{
    public function onAfterInitialise()
    {
        @include JPATH_ADMINISTRATOR.'/components/com_widgetkit/widgetkit-app.php';
    }
}

<?php

namespace YOOtheme\Widgetkit\Framework\Joomla;

use YOOtheme\Widgetkit\Framework\Csrf\DefaultCsrfProvider;

class CsrfProvider extends DefaultCsrfProvider
{
    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        return \JFactory::getSession()->getToken();
    }
}

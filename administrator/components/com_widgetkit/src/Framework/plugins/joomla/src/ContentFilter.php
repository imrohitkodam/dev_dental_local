<?php

namespace YOOtheme\Widgetkit\Framework\Joomla;

use YOOtheme\Widgetkit\Framework\Filter\FilterInterface;

class ContentFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function filter($value)
    {
        return \JHtmlContent::prepare($value);
    }
}

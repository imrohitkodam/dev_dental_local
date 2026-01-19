<?php

/*
 * @package     XT Transitional Package from FrameworkOnFramework
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2024 Extly, CB. All rights reserved.
 *              Based on Akeeba's FrameworkOnFramework
 * @license     https://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * @see         https://www.extly.com
 */

// Protect from unauthorized access
defined('XTF0F_INCLUDED') || exit;

/**
 * Field header for Published (enabled) columns
 *
 * @since    2.0
 */
class XTF0FFormHeaderPublished extends XTF0FFormHeaderFieldselectable
{
    /**
     * Create objects for the options
     *
     * @return array The array of option objects
     */
    protected function getOptions()
    {
        $config = [
            'published'		 => 1,
            'unpublished'	 => 1,
            'archived'		 => 0,
            'trash'			 => 0,
            'all'			 => 0,
        ];

        $stack = [];

        if ('false' == $this->element['show_published']) {
            $config['published'] = 0;
        }

        if ('false' == $this->element['show_unpublished']) {
            $config['unpublished'] = 0;
        }

        if ('true' == $this->element['show_archived']) {
            $config['archived'] = 1;
        }

        if ('true' == $this->element['show_trash']) {
            $config['trash'] = 1;
        }

        if ('true' == $this->element['show_all']) {
            $config['all'] = 1;
        }

        $options = JHtml::_('jgrid.publishedOptions', $config);

        reset($options);

        return $options;
    }
}

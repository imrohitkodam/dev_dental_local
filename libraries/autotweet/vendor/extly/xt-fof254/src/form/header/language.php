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
 * Language field header
 *
 * @since    2.0
 */
class XTF0FFormHeaderLanguage extends XTF0FFormHeaderFieldselectable
{
    /**
     * Method to get the filter options.
     *
     * @return array the filter option objects
     *
     * @since   2.0
     */
    protected function getOptions()
    {
        // Initialize some field attributes.
        $client = (string) $this->element['client'];

        if ('site' !== $client && 'administrator' !== $client) {
            $client = 'site';
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(
            parent::getOptions(), JLanguageHelper::createLanguageList($this->value, constant('JPATH_'.strtoupper($client)), true, true)
        );

        return $options;
    }
}

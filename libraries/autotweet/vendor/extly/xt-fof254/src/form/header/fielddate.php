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
 * Generic field header, with text input (search) filter
 *
 * @since    2.0
 */
class XTF0FFormHeaderFielddate extends XTF0FFormHeaderField
{
    /**
     * Get the filter field
     *
     * @return string The HTML
     */
    protected function getFilter()
    {
        // Initialize some field attributes.
        $format = $this->element['format'] ? (string) $this->element['format'] : '%Y-%m-%d';
        $attributes = [];

        if ($this->element['size']) {
            $attributes['size'] = (int) $this->element['size'];
        }

        if ($this->element['maxlength']) {
            $attributes['maxlength'] = (int) $this->element['maxlength'];
        }

        if ($this->element['filterclass']) {
            $attributes['class'] = (string) $this->element['filterclass'];
        }

        if ('true' === (string) $this->element['readonly']) {
            $attributes['readonly'] = 'readonly';
        }

        if ('true' === (string) $this->element['disabled']) {
            $attributes['disabled'] = 'disabled';
        }

        if ($this->element['onchange']) {
            $attributes['onchange'] = (string) $this->element['onchange'];
        } else {
            $onchange = 'document.adminForm.submit()';
        }

        if ((string) $this->element['placeholder'] !== '' && (string) $this->element['placeholder'] !== '0') {
            $attributes['placeholder'] = JText::_((string) $this->element['placeholder']);
        }

        $name = $this->element['searchfieldname'] ?: $this->name;

        if ($this->element['searchfieldname']) {
            $model = $this->form->getModel();
            $searchvalue = $model->getState((string) $this->element['searchfieldname']);
        } else {
            $searchvalue = $this->value;
        }

        // Get some system objects.
        $config = XTF0FPlatform::getInstance()->getConfig();
        $user = JFactory::getUser();

        // If a known filter is given use it.
        switch (strtoupper((string) $this->element['filter'])) {
            case 'SERVER_UTC':
                // Convert a date to UTC based on the server timezone.
                if ((int) $this->value !== 0) {
                    // Get a date object based on the correct timezone.
                    $date = XTF0FPlatform::getInstance()->getDate($searchvalue, 'UTC');
                    $date->setTimezone(new DateTimeZone($config->get('offset')));

                    // Transform the date string.
                    $searchvalue = $date->format('Y-m-d H:i:s', true, false);
                }

                break;

            case 'USER_UTC':
                // Convert a date to UTC based on the user timezone.
                if ((int) $searchvalue !== 0) {
                    // Get a date object based on the correct timezone.
                    $date = XTF0FPlatform::getInstance()->getDate($this->value, 'UTC');
                    $date->setTimezone(new DateTimeZone($user->getParam('timezone', $config->get('offset'))));

                    // Transform the date string.
                    $searchvalue = $date->format('Y-m-d H:i:s', true, false);
                }

                break;
        }

        return JHtml::_('calendar', $searchvalue, $name, $name, $format, $attributes);
    }

    /**
     * Get the buttons HTML code
     *
     * @return string The HTML
     */
    protected function getButtons()
    {
        return '';
    }
}

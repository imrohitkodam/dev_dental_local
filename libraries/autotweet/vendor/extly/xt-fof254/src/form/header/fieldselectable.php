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
 * Generic field header, with drop down filters
 *
 * @since    2.0
 */
class XTF0FFormHeaderFieldselectable extends XTF0FFormHeaderField
{
    /**
     * Create objects for the options
     *
     * @return array The array of option objects
     */
    protected function getOptions()
    {
        $options = [];

        // Get the field $options
        foreach ($this->element->children() as $option) {
            // Only add <option /> elements.
            if ('option' !== $option->getName()) {
                continue;
            }

            // Create a new option object based on the <option /> element.
            $options[] = JHtml::_(
                'select.option',
                (string) $option['value'],
                JText::alt(
                    trim((string) $option),
                    preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)
                ),
                'value', 'text', ('true' === (string) $option['disabled'])
            );
        }

        // Do we have a class and method source for our options?
        $source_file = empty($this->element['source_file']) ? '' : (string) $this->element['source_file'];
        $source_class = empty($this->element['source_class']) ? '' : (string) $this->element['source_class'];
        $source_method = empty($this->element['source_method']) ? '' : (string) $this->element['source_method'];
        $source_key = empty($this->element['source_key']) ? '*' : (string) $this->element['source_key'];
        $source_value = empty($this->element['source_value']) ? '*' : (string) $this->element['source_value'];
        $source_translate = empty($this->element['source_translate']) ? 'true' : (string) $this->element['source_translate'];
        $source_translate = in_array(strtolower($source_translate), ['true', 'yes', '1', 'on']);

        $source_format = empty($this->element['source_format']) ? '' : (string) $this->element['source_format'];

        if ($source_class && $source_method) {
            // Maybe we have to load a file?
            if ($source_file !== '' && $source_file !== '0') {
                $source_file = XTF0FTemplateUtils::parsePath($source_file, true);

                if (XTF0FPlatform::getInstance()->getIntegrationObject('filesystem')->fileExists($source_file)) {
                    include_once $source_file;
                }
            }

            // Make sure the class exists
            // ...and so does the option
            if (class_exists($source_class, true) && in_array($source_method, get_class_methods($source_class))) {
                // Get the data from the class
                if ('optionsobject' === $source_format) {
                    $options = array_merge($options, $source_class::$source_method());
                } else {
                    $source_data = $source_class::$source_method();

                    // Loop through the data and prime the $options array
                    foreach ($source_data as $k => $v) {
                        $key = ($source_key === '' || $source_key === '0' || ('*' === $source_key)) ? $k : $v[$source_key];
                        $value = ($source_value === '' || $source_value === '0' || ('*' === $source_value)) ? $v : $v[$source_value];

                        if ($source_translate) {
                            $value = JText::_($value);
                        }

                        $options[] = JHtml::_('select.option', $key, $value, 'value', 'text');
                    }
                }
            }
        }

        reset($options);

        return $options;
    }
}

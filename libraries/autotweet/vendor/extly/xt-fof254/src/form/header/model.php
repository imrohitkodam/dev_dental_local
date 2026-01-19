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

if (!class_exists('JFormFieldSql')) {
    require_once JPATH_LIBRARIES.'/joomla/form/fields/sql.php';
}

/**
 * Form Field class for XTF0F
 * Generic list from a model's results
 *
 * @since    2.0
 */
class XTF0FFormHeaderModel extends XTF0FFormHeaderFieldselectable
{
    /**
     * Method to get the field options.
     *
     * @return array the field option objects
     */
    protected function getOptions()
    {
        $options = [];

        // Initialize some field attributes.
        $key = $this->element['key_field'] ? (string) $this->element['key_field'] : 'value';
        $value = $this->element['value_field'] ? (string) $this->element['value_field'] : (string) $this->element['name'];
        $applyAccess = $this->element['apply_access'] ? (string) $this->element['apply_access'] : 'false';
        $modelName = (string) $this->element['model'];
        $nonePlaceholder = (string) $this->element['none'];
        $translate = empty($this->element['translate']) ? 'true' : (string) $this->element['translate'];
        $translate = in_array(strtolower($translate), ['true', 'yes', '1', 'on']);

        if ($nonePlaceholder !== '' && $nonePlaceholder !== '0') {
            $options[] = JHtml::_('select.option', null, JText::_($nonePlaceholder));
        }

        // Process field atrtibutes
        $applyAccess = strtolower($applyAccess);
        $applyAccess = in_array($applyAccess, ['yes', 'on', 'true', '1']);

        // Explode model name into model name and prefix
        $parts = XTF0FInflector::explode($modelName);
        $mName = ucfirst(array_pop($parts));
        $mPrefix = XTF0FInflector::implode($parts);

        // Get the model object
        $config = ['savestate' => 0];
        $xtf0FModel = XTF0FModel::getTmpInstance($mName, $mPrefix, $config);

        if ($applyAccess) {
            $xtf0FModel->applyAccessFiltering();
        }

        // Process state variables
        foreach ($this->element->children() as $stateoption) {
            // Only add <option /> elements.
            if ('state' !== $stateoption->getName()) {
                continue;
            }

            $stateKey = (string) $stateoption['key'];
            $stateValue = (string) $stateoption;

            $xtf0FModel->setState($stateKey, $stateValue);
        }

        // Set the query and get the result list.
        $items = $xtf0FModel->getItemList(true);

        // Build the field options.
        if (!empty($items)) {
            foreach ($items as $item) {
                if (true == $translate) {
                    $options[] = JHtml::_('select.option', $item->$key, JText::_($item->$value));
                } else {
                    $options[] = JHtml::_('select.option', $item->$key, $item->$value);
                }
            }
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}

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

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for XTF0F
 * Components installed on the site
 *
 * @since    2.1
 */
class XTF0FFormFieldComponents extends JFormFieldList implements XTF0FFormField
{
    public $client_ids = null;

    /** @var XTF0FTable The item being rendered in a repeatable form field */
    public $item;

    /** @var int A monotonically increasing number, denoting the row number in a repeatable view */
    public $rowid;

    protected $static;

    protected $repeatable;

    /**
     * Method to get certain otherwise inaccessible properties from the form field object.
     *
     * @param string $name the property name for which to the the value
     *
     * @return mixed the property value or null
     *
     * @since   2.1
     */
    public function __get($name)
    {
        switch ($name) {
            case 'static':
                if (empty($this->static)) {
                    $this->static = $this->getStatic();
                }

                return $this->static;
                break;

            case 'repeatable':
                if (empty($this->repeatable)) {
                    $this->repeatable = $this->getRepeatable();
                }

                return $this->repeatable;
                break;

            default:
                return parent::__get($name);
        }
    }

    /**
     * Get the rendering of this field type for static display, e.g. in a single
     * item view (typically a "read" task).
     *
     * @since 2.1
     *
     * @return string The field HTML
     */
    public function getStatic()
    {
        $class = $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';

        return '<span id="'.$this->id.'" '.$class.'>'.
            htmlspecialchars(XTF0FFormFieldList::getOptionName($this->getOptions(), $this->value), \ENT_COMPAT, 'UTF-8').
            '</span>';
    }

    /**
     * Get the rendering of this field type for a repeatable (grid) display,
     * e.g. in a view listing many item (typically a "browse" task)
     *
     * @since 2.1
     *
     * @return string The field HTML
     */
    public function getRepeatable()
    {
        $class = $this->element['class'] ? (string) $this->element['class'] : '';

        return '<span class="'.$this->id.' '.$class.'">'.
            htmlspecialchars(XTF0FFormFieldList::getOptionName($this->getOptions(), $this->value), \ENT_COMPAT, 'UTF-8').
            '</span>';
    }

    /**
     * Translate a list of objects with JText::_().
     *
     * @param array  $item The array of objects
     * @param string $type The extension type (e.g. component)
     *
     * @since   2.1
     *
     * @return string $text  The translated name of the extension
     *
     * @see administrator/com_installer/models/extension.php
     */
    public function translate($item, $type)
    {
        $xtf0FPlatform = XTF0FPlatform::getInstance();

        // Map the manifest cache to $item. This is needed to get the name from the
        // manifest_cache and NOT from the name column, else some JText::_() translations fails.
        $mData = json_decode($item->manifest_cache);

        if ($mData) {
            foreach ($mData as $key => $value) {
                if ('type' == $key) {
                    // Ignore the type field
                    continue;
                }

                $item->$key = $value;
            }
        }

        $lang = $xtf0FPlatform->getLanguage();

        if ($type === 'component') {
            $source = JPATH_ADMINISTRATOR.'/components/'.$item->element;
            if (!($lang->load($item->element . '.sys', JPATH_ADMINISTRATOR, null, false, false)
                || $lang->load($item->element . '.sys', $source, null, false, false)
                || $lang->load($item->element . '.sys', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false))) {
                $lang->load($item->element . '.sys', $source, $lang->getDefault(), false, false);
            }
        }

        $text = JText::_($item->name);

        return $text;
    }

    /**
     * Get a list of all installed components and also translates them.
     *
     * The manifest_cache is used to get the extension names, since JInstaller is also
     * translating those names in stead of the name column. Else some of the translations
     * fails.
     *
     * @since    2.1
     *
     * @return array an array of JHtml options
     */
    protected function getOptions()
    {
        $xtf0FDatabaseDriver = XTF0FPlatform::getInstance()->getDbo();

        // Check for client_ids override
        $client_ids = $this->client_ids ?? $this->element['client_ids'];

        $client_ids = explode(',', $client_ids);

        // Calculate client_ids where clause
        foreach ($client_ids as &$client_id) {
            $client_id = (int) trim($client_id);
            $client_id = $xtf0FDatabaseDriver->q($client_id);
        }

        $xtf0FDatabaseQuery = $xtf0FDatabaseDriver->getQuery(true)
            ->select(
                [
                    $xtf0FDatabaseDriver->qn('name'),
                    $xtf0FDatabaseDriver->qn('element'),
                    $xtf0FDatabaseDriver->qn('client_id'),
                    $xtf0FDatabaseDriver->qn('manifest_cache'),
                ]
            )
            ->from($xtf0FDatabaseDriver->qn('#__extensions'))
            ->where($xtf0FDatabaseDriver->qn('type').' = '.$xtf0FDatabaseDriver->q('component'))
            ->where($xtf0FDatabaseDriver->qn('client_id').' IN ('.implode(',', $client_ids).')');
        $xtf0FDatabaseDriver->setQuery($xtf0FDatabaseQuery);
        $components = $xtf0FDatabaseDriver->loadObjectList('element');

        // Convert to array of objects, so we can use sortObjects()
        // Also translate component names with JText::_()
        $aComponents = [];
        $user = JFactory::getUser();

        foreach ($components as $component) {
            // Don't show components in the list where the user doesn't have access for
            // TODO: perhaps add an option for this
            if (!$user->authorise('core.manage', $component->element)) {
                continue;
            }

            $oData = (object) [
                'value'	=> $component->element,
                'text' 	=> $this->translate($component, 'component'),
            ];
            $aComponents[$component->element] = $oData;
        }

        // Reorder the components array, because the alphabetical
        // ordering changed due to the JText::_() translation
        uasort(
            $aComponents,
            fn($a, $b) => strcasecmp($a->text, $b->text)
        );

        return $aComponents;
    }
}

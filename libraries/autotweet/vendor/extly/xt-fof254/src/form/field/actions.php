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
 * Supports a generic list of options.
 *
 * @since    2.0
 */
class XTF0FFormFieldActions extends JFormFieldList implements XTF0FFormField
{
    /** @var int A monotonically increasing number, denoting the row number in a repeatable view */
    public $rowid;

    /** @var XTF0FTable The item being rendered in a repeatable form field */
    public $item;

    protected $static;

    protected $repeatable;

    /**
     * Method to get certain otherwise inaccessible properties from the form field object.
     *
     * @param string $name the property name for which to the the value
     *
     * @return mixed the property value or null
     *
     * @since   2.0
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
     * @since 2.0
     *
     * @return string The field HTML
     */
    public function getStatic()
    {
        throw new Exception(self::class.' cannot be used in single item display forms');
    }

    /**
     * Get the rendering of this field type for a repeatable (grid) display,
     * e.g. in a view listing many item (typically a "browse" task)
     *
     * @since 2.0
     *
     * @return string The field HTML
     */
    public function getRepeatable()
    {
        if (!($this->item instanceof XTF0FTable)) {
            throw new Exception(self::class.' needs a XTF0FTable to act upon');
        }

        $config = $this->getConfig();

        // Initialise
        $prefix = '';
        $checkbox = 'cb';
        $publish_up = null;
        $publish_down = null;
        $enabled = true;

        $html = '<div class="btn-group">';

        // Render a published field
        if ($publishedFieldName = $this->item->getColumnAlias('enabled')) {
            if ($config['published'] || $config['unpublished']) {
                // Generate a XTF0FFormFieldPublished field
                $publishedField = $this->getPublishedField($publishedFieldName);

                // Render the publish button
                $html .= $publishedField->getRepeatable();
            }

            if ($config['archived']) {
                $archived = 2 == $this->item->{$publishedFieldName};

                // Create dropdown items
                $action = $archived ? 'unarchive' : 'archive';
                JHtml::_('actionsdropdown.'.$action, 'cb'.$this->rowid, $prefix);
            }

            if ($config['trash']) {
                $trashed = -2 == $this->item->{$publishedFieldName};

                $action = $trashed ? 'untrash' : 'trash';
                JHtml::_('actionsdropdown.'.$action, 'cb'.$this->rowid, $prefix);
            }

            // Render dropdown list
            if ($config['archived'] || $config['trash']) {
                $html .= JHtml::_('actionsdropdown.render', $this->item->title);
            }
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Get the field configuration
     *
     * @return array
     */
    protected function getConfig()
    {
        // If no custom options were defined let's figure out which ones of the
        // defaults we shall use...
        $config = [
            'published'		 => 1,
            'unpublished'	 => 1,
            'archived'		 => 0,
            'trash'			 => 0,
            'all'			 => 0,
        ];

        $stack = [];

        if (isset($this->element['show_published'])) {
            $config['published'] = XTF0FStringUtils::toBool($this->element['show_published']);
        }

        if (isset($this->element['show_unpublished'])) {
            $config['unpublished'] = XTF0FStringUtils::toBool($this->element['show_unpublished']);
        }

        if (isset($this->element['show_archived'])) {
            $config['archived'] = XTF0FStringUtils::toBool($this->element['show_archived']);
        }

        if (isset($this->element['show_trash'])) {
            $config['trash'] = XTF0FStringUtils::toBool($this->element['show_trash']);
        }

        if (isset($this->element['show_all'])) {
            $config['all'] = XTF0FStringUtils::toBool($this->element['show_all']);
        }

        return $config;
    }

    /**
     * Method to get the field options.
     *
     * @since 2.0
     *
     * @return array the field option objects
     */
    protected function getOptions()
    {
        return null;
    }

    /**
     * Method to get a
     *
     * @param string $enabledFieldName Name of the enabled/published field
     *
     * @return XTF0FFormFieldPublished Field
     */
    protected function getPublishedField($enabledFieldName)
    {
        $attributes = [
            'name' => $enabledFieldName,
            'type' => 'published',
        ];

        if ($this->element['publish_up']) {
            $attributes['publish_up'] = (string) $this->element['publish_up'];
        }

        if ($this->element['publish_down']) {
            $attributes['publish_down'] = (string) $this->element['publish_down'];
        }

        foreach ($attributes as $name => $value) {
            if (null !== $value) {
                $renderedAttributes[] = $name.'="'.$value.'"';
            }
        }

        $publishedXml = new SimpleXMLElement('<field '.implode(' ', $renderedAttributes).' />');

        $xtf0FFormFieldPublished = new XTF0FFormFieldPublished($this->form);

        // Pass required objects to the field
        $xtf0FFormFieldPublished->item = $this->item;
        $xtf0FFormFieldPublished->rowid = $this->rowid;
        $xtf0FFormFieldPublished->setup($publishedXml, $this->item->{$enabledFieldName});

        return $xtf0FFormFieldPublished;
    }
}

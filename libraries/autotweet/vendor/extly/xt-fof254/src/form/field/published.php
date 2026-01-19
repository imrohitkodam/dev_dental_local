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
class XTF0FFormFieldPublished extends JFormFieldList implements XTF0FFormField
{
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
        $class = $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';

        return '<span id="'.$this->id.'" '.$class.'>'.
            htmlspecialchars(XTF0FFormFieldList::getOptionName($this->getOptions(), $this->value), \ENT_COMPAT, 'UTF-8').
            '</span>';
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

        // Initialise
        $prefix = '';
        $checkbox = 'cb';
        $publish_up = null;
        $publish_down = null;
        $enabled = true;

        // Get options
        if ($this->element['prefix']) {
            $prefix = (string) $this->element['prefix'];
        }

        if ($this->element['checkbox']) {
            $checkbox = (string) $this->element['checkbox'];
        }

        if ($this->element['publish_up']) {
            $publish_up = (string) $this->element['publish_up'];
        }

        if ($this->element['publish_down']) {
            $publish_down = (string) $this->element['publish_down'];
        }

        // @todo Enforce ACL checks to determine if the field should be enabled or not
        // Get the HTML
        return JHtml::_('jgrid.published', $this->value, $this->rowid, $prefix, $enabled, $checkbox, $publish_up, $publish_down);
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
        $options = parent::getOptions();

        if (!empty($options)) {
            return $options;
        }

        // If no custom options were defined let's figure out which ones of the
        // defaults we shall use...

        $config = [
            'published'		 => 1,
            'unpublished'	 => 1,
            'archived'		 => 0,
            'trash'			 => 0,
            'all'			 => 0,
        ];

        $configMap = [
            'show_published'	=> ['published', 1],
            'show_unpublished'	=> ['unpublished', 1],
            'show_archived'		=> ['archived', 0],
            'show_trash'		=> ['trash', 0],
            'show_all'			=> ['all', 0],
        ];

        foreach ($configMap as $attribute => $preferences) {
            [$configKey, $default] = $preferences;

            switch (strtolower($this->element[$attribute])) {
                case 'true':
                case '1':
                case 'yes':
                    $config[$configKey] = true;

                    // no break
                case 'false':
                case '0':
                case 'no':
                    $config[$configKey] = false;

                    // no break
                default:
                    $config[$configKey] = $default;
            }
        }

        if ($config['published']) {
            $stack[] = JHtml::_('select.option', '1', JText::_('JPUBLISHED'));
        }

        if ($config['unpublished']) {
            $stack[] = JHtml::_('select.option', '0', JText::_('JUNPUBLISHED'));
        }

        if ($config['archived']) {
            $stack[] = JHtml::_('select.option', '2', JText::_('JARCHIVED'));
        }

        if ($config['trash']) {
            $stack[] = JHtml::_('select.option', '-2', JText::_('JTRASHED'));
        }

        if ($config['all']) {
            $stack[] = JHtml::_('select.option', '*', JText::_('JALL'));
        }

        return $stack;
    }
}

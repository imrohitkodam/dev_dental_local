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
 * Form Field class for XTF0F
 * Renders the row ordering interface checkbox in browse views
 *
 * @since    2.0
 */
class XTF0FFormFieldOrdering extends JFormField implements XTF0FFormField
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

        $class = $this->element['class'] ?? 'input-mini';
        $icon = $this->element['icon'] ?? 'icon-menu';

        $html = '';

        $view = $this->form->getView();

        $ordering = 'ordering' == $view->getLists()->order;

        if (!$view->hasAjaxOrderingSupport()) {
            // Ye olde Joomla! 2.5 method
            $disabled = $ordering ? '' : 'disabled="disabled"';
            $html .= '<span>';
            $html .= $view->pagination->orderUpIcon($this->rowid, true, 'orderup', 'Move Up', $ordering);
            $html .= '</span><span>';
            $html .= $view->pagination->orderDownIcon($this->rowid, $view->pagination->total, true, 'orderdown', 'Move Down', $ordering);
            $html .= '</span>';
            $html .= '<input type="text" name="order[]" size="5" value="'.$this->value.'" '.$disabled;
            $html .= 'class="text-area-order" style="text-align: center" />';
        } elseif ($view->getPerms()->editstate) {
            // The modern drag'n'drop method
            $disableClassName = '';
            $disabledLabel = '';
            $hasAjaxOrderingSupport = $view->hasAjaxOrderingSupport();
            if (!$hasAjaxOrderingSupport['saveOrder']) {
                $disabledLabel = JText::_('JORDERINGDISABLED');
                $disableClassName = 'inactive tip-top';
            }

            $orderClass = $ordering ? 'order-enabled' : 'order-disabled';
            $html .= '<div class="'.$orderClass.'">';
            $html .= '<span class="sortable-handler '.$disableClassName.'" title="'.$disabledLabel.'" rel="tooltip">';
            $html .= '<i class="'.$icon.'"></i>';
            $html .= '</span>';
            if ($ordering) {
                $html .= '<input type="text" name="order[]" size="5" class="'.$class.' text-area-order" value="'.$this->value.'" />';
            }

            $html .= '</div>';
        } else {
            $html .= '<span class="sortable-handler inactive" >';
            $html .= '<i class="'.$icon.'"></i>';
            $html .= '</span>';
        }

        return $html;
    }

    /**
     * Method to get the field input markup for this field type.
     *
     * @since 2.0
     *
     * @return string the field input markup
     */
    protected function getInput()
    {
        $html = [];
        $attr = '';

        // Initialize some field attributes.
        $attr .= empty($this->class) ? '' : ' class="'.$this->class.'"';
        $attr .= $this->disabled ? ' disabled' : '';
        $attr .= empty($this->size) ? '' : ' size="'.$this->size.'"';

        // Initialize JavaScript field attributes.
        $attr .= empty($this->onchange) ? '' : ' onchange="'.$this->onchange.'"';

        $this->item = $this->form->getModel()->getItem();

        $keyfield = $this->item->getKeyName();
        $itemId = $this->item->$keyfield;

        $xtf0FDatabaseQuery = $this->getQuery();

        // Create a read-only list (no name) with a hidden input to store the value.
        if ($this->readonly) {
            $html[] = JHtml::_('list.ordering', '', $xtf0FDatabaseQuery, trim($attr), $this->value, $itemId ? 0 : 1);
            $html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
        } else {
            // Create a regular list.
            $html[] = JHtml::_('list.ordering', $this->name, $xtf0FDatabaseQuery, trim($attr), $this->value, $itemId ? 0 : 1);
        }

        return implode('', $html);
    }

    /**
     * Builds the query for the ordering list.
     *
     * @since 2.3.2
     *
     * @return XTF0FDatabaseQuery The query for the ordering form field
     */
    protected function getQuery()
    {
        $ordering = $this->name;
        $title = $this->element['ordertitle'] ? (string) $this->element['ordertitle'] : $this->item->getColumnAlias('title');

        $xtf0FDatabaseDriver = XTF0FPlatform::getInstance()->getDbo();
        $xtf0FDatabaseQuery = $xtf0FDatabaseDriver->getQuery(true);
        $xtf0FDatabaseQuery->select([$xtf0FDatabaseDriver->quoteName($ordering, 'value'), $xtf0FDatabaseDriver->quoteName($title, 'text')])
                ->from($xtf0FDatabaseDriver->quoteName($this->item->getTableName()))
                ->order($ordering);

        return $xtf0FDatabaseQuery;
    }
}

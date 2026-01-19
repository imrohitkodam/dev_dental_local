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
 * Relation list
 *
 * @since    2.0
 */
class XTF0FFormFieldRelation extends XTF0FFormFieldList
{
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
        return $this->getRepeatable();
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
        $class = $this->element['class'] ? (string) $this->element['class'] : $this->id;
        $relationclass = $this->element['relationclass'] ? (string) $this->element['relationclass'] : '';
        $value_field = $this->element['value_field'] ? (string) $this->element['value_field'] : 'title';
        $translate = $this->element['translate'] ? (string) $this->element['translate'] : false;
        $link_url = $this->element['url'] ? (string) $this->element['url'] : false;

        if (!($link_url && $this->item instanceof XTF0FTable)) {
            $link_url = false;
        }

        if ($this->element['empty_replacement']) {
            $empty_replacement = (string) $this->element['empty_replacement'];
        }

        $relationName = XTF0FInflector::pluralize($this->name);
        $relations = $this->item->getRelations()->getMultiple($relationName);

        foreach ($relations as $relation) {
            $html = '<span class="'.$relationclass.'">';

            if ($link_url) {
                $keyfield = $relation->getKeyName();
                $this->_relationId = $relation->$keyfield;

                $url = $this->parseFieldTags($link_url);
                $html .= '<a href="'.$url.'">';
            }

            $value = $relation->get($relation->getColumnAlias($value_field));

            // Get the (optionally formatted) value
            if ($empty_replacement !== '' && $empty_replacement !== '0' && empty($value)) {
                $value = JText::_($empty_replacement);
            }

            if (true == $translate) {
                $html .= JText::_($value);
            } else {
                $html .= $value;
            }

            if ($link_url) {
                $html .= '</a>';
            }

            $html .= '</span>';

            $rels[] = $html;
        }

        $html = '<span class="'.$class.'">';
        $html .= implode(', ', $rels);
        $html .= '</span>';

        return $html;
    }

    /**
     * Method to get the field options.
     *
     * @return array the field option objects
     */
    protected function getOptions()
    {
        $options = [];
        $this->value = [];

        $value_field = $this->element['value_field'] ? (string) $this->element['value_field'] : 'title';

        $input = $this->getJoomlaInput();
        $component = ucfirst(str_replace('com_', '', $input->getString('option')));
        $view = ucfirst($input->getString('view'));
        $relation = XTF0FInflector::pluralize((string) $this->element['name']);

        $xtf0FModel = XTF0FModel::getTmpInstance(ucfirst($relation), $component.'Model');
        $table = $xtf0FModel->getTable();

        $key = $table->getKeyName();
        $value = $table->getColumnAlias($value_field);

        foreach ($xtf0FModel->getItemList(true) as $option) {
            $options[] = JHtml::_('select.option', $option->$key, $option->$value);
        }

        if ($id = XTF0FModel::getAnInstance($view)->getId()) {
            $table = XTF0FTable::getInstance($view, $component.'Table');
            $table->load($id);

            $relations = $table->getRelations()->getMultiple($relation);

            foreach ($relations as $relation) {
                $this->value[] = $relation->getId();
            }
        }

        return $options;
    }

    /**
     * Replace string with tags that reference fields
     *
     * @param string $text Text to process
     *
     * @return string Text with tags replace
     */
    protected function parseFieldTags($text)
    {
        $ret = $text;

        // Replace [ITEM:ID] in the URL with the item's key value (usually:
        // the auto-incrementing numeric ID)
        $keyfield = $this->item->getKeyName();
        $replace = $this->item->$keyfield;
        $ret = str_replace('[ITEM:ID]', $replace, $ret);

        // Replace the [ITEMID] in the URL with the current Itemid parameter
        $ret = str_replace('[ITEMID]', JFactory::getApplication()->input->getInt('Itemid', 0), $ret);

        // Replace the [RELATION:ID] in the URL with the relation's key value
        $ret = str_replace('[RELATION:ID]', $this->_relationId, $ret);

        // Replace other field variables in the URL
        $fields = $this->item->getTableFields();

        foreach ($fields as $field) {
            $fieldname = $field->Field;

            if (empty($fieldname)) {
                $fieldname = $field->column_name;
            }

            $search = '[ITEM:'.strtoupper($fieldname).']';
            $replace = $this->item->$fieldname;
            $ret = str_replace($search, $replace, $ret);
        }

        return $ret;
    }

    private static function getJoomlaInput()
    {
        if (version_compare(JVERSION, '4', '<')) {
            // Joomla 3 code
            jimport('joomla.filter.input');

            $input = JFactory::getApplication()->input;
            $data = $input->serialize();
            $jinput = new \Joomla\CMS\Input\Input([]);
            $jinput->unserialize($data);

            return $jinput;
        }

        $input = Joomla\CMS\Factory::getApplication()->input;
        $data = $input->getArray();
        $jinput = new \Joomla\CMS\Input\Input($data);

        return $jinput;
    }
}

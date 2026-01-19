<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class PPThemesHelperFloatLabel extends PPThemesHelperAbstract
{
	/**
	 * Generates a country input with a floating label
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function country($label, $name, $value, $id = '', $attributes = '', $disabled = false)
	{
		if (!$id) {
			$id = $name;
		}

		$label = JText::_($label);

		$theme = PP::themes();
		$theme->set('label', $label);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);
		$theme->set('disabled', $disabled);

		$output = $theme->output('site/helpers/floatlabel/country');

		return $output;
	}

	/**
	 * Generates a list input with a floating label
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function lists($label, $name, $value, $id = '', $attributes = '', $options = array())
	{
		if (!$id) {
			$id = $name;
		}

		$label = JText::_($label);

		$theme = PP::themes();
		$theme->set('label', $label);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);
		$theme->set('options', $options);

		$output = $theme->output('site/helpers/floatlabel/lists');

		return $output;
	}

	/**
	 * Generates a checkbox input
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function checkbox($label, $name, $value, $id = '', $attributes = [], $options = [])
	{
		if (!$id) {
			$id = $name;
		}

		if (!$options) {
			$options = [];

			$no = new stdClass();
			$no->title = JText::_('No');
			$no->value = 0;
			$options[] = $no;

			$yes = new stdClass();
			$yes->title = JText::_('Yes');
			$yes->value = 1;

			$options[] = $yes;
		}

		$required = FH::normalize($attributes, 'required', false);

		$label = JText::_($label);
		$label = $required ? '*' . $label : $label;

		$attributes = $this->formatAttributes($attributes);

		$theme = PP::themes();
		$theme->set('label', $label);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);
		$theme->set('options', $options);

		$output = $theme->output('site/helpers/floatlabel/checkbox');

		return $output;
	}

	/**
	 * Generates a radio input
	 *
	 * @since	5.0.1
	 * @access	public
	 */
	public function radio($label, $name, $value, $id = '', $attributes = [], $options = [])
	{
		if (!$id) {
			$id = $name;
		}

		if (!$options) {
			$options = [];

			$no = new stdClass();
			$no->title = JText::_('No');
			$no->value = 0;
			$options[] = $no;

			$yes = new stdClass();
			$yes->title = JText::_('Yes');
			$yes->value = 1;

			$options[] = $yes;
		}

		$required = FH::normalize($attributes, 'required', false);

		$label = JText::_($label);
		$label = $required ? '*' . $label : $label;

		$attributes = $this->formatAttributes($attributes);

		$theme = PP::themes();
		$theme->set('label', $label);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);
		$theme->set('options', $options);

		$output = $theme->output('site/helpers/floatlabel/radio');

		return $output;
	}

	/**
	 * Generates a text input with a floating label
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function password($label, $name, $value, $id = '', $attributes = '')
	{
		if (!$id) {
			$id = $name;
		}

		$label = JText::_($label);

		$theme = PP::themes();
		$theme->set('label', $label);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);

		$output = $theme->output('site/helpers/floatlabel/password');

		return $output;
	}

	/**
	 * Generates a text input with a floating label
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function text($label, $name, $value, $id = '', $attributes = '', $options = array(), $readOnly = false)
	{
		if (!$id) {
			$id = $name;
		}

		$label = JText::_($label);
		$theme = PP::themes();
		$theme->set('label', $label);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);
		$theme->set('options', $options);
		$theme->set('readOnly', $readOnly);

		$output = $theme->output('site/helpers/floatlabel/text');

		return $output;
	}

	/**
	 * Generates a text input with a floating label
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function textarea($label, $name, $value, $id = '', $attributes = '')
	{
		if (!$id) {
			$id = $name;
		}

		$label = JText::_($label);

		$theme = PP::themes();
		$theme->set('label', $label);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);

		$output = $theme->output('site/helpers/floatlabel/textarea');

		return $output;
	}

	/**
	 * As there is no way to generate a nice toggler option with floatlabel, we'll use single checkbox instead
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function toggler($label, $name, $value, $id = '', $attributes = '', $options = array())
	{
		if (!$id) {
			$id = $name;
		}

		if (!$options) {
			$options = array();
			$no = new stdClass();
			$no->title = JText::_('No');
			$no->value = 0;
			$options[] = $no;

			$yes = new stdClass();
			$yes->title = JText::_('Yes');
			$yes->value = 1;
			$options[] = $yes;
		}

		$label = JText::_($label);
		$attributes = $this->formatAttributes($attributes);

		$theme = PP::themes();
		$theme->set('label', $label);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);
		$theme->set('options', $options);

		$output = $theme->output('site/helpers/floatlabel/toggler');

		return $output;
	}

	/**
	 * Generates a telephone input with a floating label
	 *
	 * @since	4.2.6
	 * @access	public
	 */
	public function telephone($label, $name, $value, $id = '', $attributes = '', $options = array(), $readOnly = false)
	{
		if (!$id) {
			$id = $name;
		}

		$label = JText::_($label);

		$theme = PP::themes();
		$theme->set('label', $label);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);
		$theme->set('options', $options);
		$theme->set('readOnly', $readOnly);

		$output = $theme->output('site/helpers/floatlabel/telephone');

		return $output;
	}

	/**
	 * Generates a file input with a floating label
	 *
	 * @since	4.2.8
	 * @access	public
	 */
	public function file($label, $name, $value, $id = '', $attributes = array(), $options = array())
	{
		if (!$id) {
			$id = $name;
		}

		// Merge with default class
		$attributes['class'] = PP::normalize($attributes, 'class', '');
		$attributes['class'] .= ' o-form-custom-file__input';

		$required = PP::normalize($attributes, 'required', false);

		$user = PP::user();
		$options['user'] = $user;
		$options['allowInput'] = true;
		$options['label'] = $label;
		$options['required'] = $required;

		$theme = PP::themes();
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);
		$theme->set('options', $options);
		$theme->set('user', $user);

		$output = $theme->output('site/helpers/floatlabel/file');

		return $output;
	}

	/**
	 * Generates a language input with a floating label
	 *
	 * @since	4.2.6
	 * @access	public
	 */
	public function language($label, $name, $value, $id = '', $attributes = '', $disabled = false)
	{
		if (!$id) {
			$id = $name;
		}

		$label = JText::_($label);

		$theme = PP::themes();
		$theme->set('label', $label);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);
		$theme->set('disabled', $disabled);

		$output = $theme->output('site/helpers/floatlabel/language');

		return $output;
	}

	/**
	 * Formats a list of attributes
	 *
	 * @since	5.0.1
	 * @access	public
	 */
	private function formatAttributes($data)
	{
		if (!$data) {
			return '';
		}

		// If attributes is already a string, we shouldn't need to format anything
		if (!is_array($data) && is_string($data)) {
			return $data;
		}

		$attributes = '';

		foreach ($data as $key => $value) {
			$attributes .= ' ' . $key . '="' . $value . '"';
		}

		return $attributes;
	}
}
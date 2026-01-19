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

class PPThemesHelperFloatlabelDashboard extends PPThemesHelperAbstract
{
	/**
	 * Generates a text input with a floating label
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function standard($label, $name, $value, $id = '', $attributes = '', $options = [], $readOnly = false)
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

		$output = $theme->output('site/helpers/floatlabel/dashboard/standard');

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

		$output = $theme->output('site/helpers/floatlabel/dashboard/password');

		return $output;
	}

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

		$output = $theme->output('site/helpers/floatlabel/dashboard/country');

		return $output;
	}

	/**
	 * Generates a text input with a floating label
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function text($label, $name, $value, $id = '', $attributes = '', $options = [], $readOnly = false)
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

		$output = $theme->output('site/helpers/floatlabel/dashboard/text');

		return $output;
	}
}
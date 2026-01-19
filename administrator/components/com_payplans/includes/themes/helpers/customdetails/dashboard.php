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

class PPThemesHelperCustomdetailsDashboard extends PPThemesHelperAbstract
{
	/**
	 * A centralized function for getting namespace of the file
	 *
	 * @since	4.2.8
	 * @access	public
	 */
	public function getNamespace($prefix)
	{
		$namespace = 'site/helpers/customdetails/dashboard/' . $prefix;

		return $namespace;
	}

	/**
	 * Generates a country input
	 *
	 * @since	4.2.8
	 * @access	public
	 */
	public function country($field, $type, $obj)
	{
		$value = $field->value;

		$themes = PP::themes();
		$themes->set('field', $field);
		$themes->set('type', $type);
		$themes->set('value', $value);
		$themes->set('name', $type . '[' . $field->name . ']');

		$namespace = $this->getNamespace('country');
		$output = $themes->output($namespace);

		return $output;
	}

	/**
	 * Generates a list input
	 *
	 * @since	4.2.8
	 * @access	public
	 */
	public function lists($field, $type, $obj)
	{
		$value = $field->value;

		$themes = PP::themes();
		$themes->set('field', $field);
		$themes->set('type', $type);
		$themes->set('value', $value);
		$themes->set('name', $type . '[' . $field->name . ']');

		$namespace = $this->getNamespace('lists');
		$output = $themes->output($namespace);

		return $output;
	}

	/**
	 * Generates a checkbox input
	 *
	 * @since	4.2.8
	 * @access	public
	 */
	public function checkbox($field, $type, $obj)
	{
		$themes = PP::themes();
		$themes->set('field', $field);
		$themes->set('type', $type);
		$themes->set('name', $type . '[' . $field->name . '][]');

		$namespace = $this->getNamespace('checkbox');
		$output = $themes->output($namespace);

		return $output;
	}

	/**
	 * Generates a text input
	 *
	 * @since	4.2.8
	 * @access	public
	 */
	public function password($field, $type, $obj)
	{
		$themes = PP::themes();
		$themes->set('field', $field);
		$themes->set('type', $type);

		$namespace = $this->getNamespace('password');
		$output = $themes->output($namespace);

		return $output;
	}

	/**
	 * Generates a text input
	 *
	 * @since	4.2.8
	 * @access	public
	 */
	public function text($field, $type, $obj)
	{
		$value = $field->value;

		$themes = PP::themes();
		$themes->set('field', $field);
		$themes->set('type', $type);
		$themes->set('value', $value);

		$namespace = $this->getNamespace('text');
		$output = $themes->output($namespace);

		return $output;
	}

	/**
	 * Generates a text input
	 *
	 * @since	4.2.8
	 * @access	public
	 */
	public function textarea($field, $type, $obj)
	{
		$value = $field->value;

		$themes = PP::themes();
		$themes->set('field', $field);
		$themes->set('type', $type);
		$themes->set('value', $value);

		$namespace = $this->getNamespace('textarea');
		$output = $themes->output($namespace);

		return $output;
	}

	/**
	 * As there is no way to generate a nice toggler option, we'll use dropdown instead
	 *
	 * @since	4.2.8
	 * @access	public
	 */
	public function toggler($field, $type, $obj)
	{
		$themes = PP::themes();
		$themes->set('field', $field);
		$themes->set('type', $type);

		$namespace = $this->getNamespace('toggler');
		$output = $themes->output($namespace);

		return $output;
	}

	/**
	 * Generates a telephone input
	 *
	 * @since	4.2.6
	 * @access	public
	 */
	public function telephone($field, $type, $obj)
	{
		$themes = PP::themes();
		$themes->set('field', $field);
		$themes->set('type', $type);

		$namespace = $this->getNamespace('telephone');
		$output = $themes->output($namespace);

		return $output;
	}

	/**
	 * Generates a file input
	 *
	 * @since	4.2.8
	 * @access	public
	 */
	public function file($field, $type, $obj, $options = [])
	{
		$group = null;

		if ($obj instanceof PPUser) {
			$group = PP_CUSTOM_DETAILS_TYPE_USER;
		}

		if ($obj instanceof PPSubscription) {
			$group = PP_CUSTOM_DETAILS_TYPE_SUBSCRIPTION;
		}

		if (!$group) {
			return;
		}

		$allowInput = PP::normalize($options, 'allowInput', true);
		$files = PP::getCustomDetailFiles($group, $obj->getId());
		$name = $type . '[' . $field->name . '][]';

		$themes = PP::themes();
		$themes->set('name', $name);
		$themes->set('field', $field);
		$themes->set('type', $type);
		$themes->set('obj', $obj);
		$themes->set('files', $files);
		$themes->set('group', $group);
		$themes->set('allowInput', $allowInput);

		$namespace = $this->getNamespace('file');
		$output = $themes->output($namespace);

		return $output;
	}

	/**
	 * Generates a radio input
	 *
	 * @since	5.0.1
	 * @access	public
	 */
	public function radio($field, $type, $obj)
	{
		$default = FH::normalize($field->attributes, 'default', null);
		$value = $field->value;

		if ($value === '' && $default) {
			$value = $default;
		}

		$themes = PP::themes();
		$themes->set('field', $field);
		$themes->set('type', $type);
		$themes->set('value', $value);

		$namespace = $this->getNamespace('radio');
		$output = $themes->output($namespace);

		return $output;
	}
}
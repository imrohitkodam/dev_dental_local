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

class PPThemesHelperSettings extends PPThemesHelperAbstract
{
	/**
	 * Renders a currency settings
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function currency($name, $title, $desc = '', $options = [])
	{
		if (empty($desc)) {
			$desc = $title . '_DESC';
		}

		$theme = PP::themes();
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);

		$contents = $theme->output('admin/helpers/settings/currency');

		return $contents;
	}

	/**
	 * Renders a discount settings
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function discounts($name, $title, $desc = '', $options = [])
	{
		if (empty($desc)) {
			$desc = $title . '_DESC';
		}

		$visible = isset($options['visible']) ? $options['visible'] : true;
		$wrapperAttributes = isset($options['wrapperAttributes']) ? $options['wrapperAttributes'] : '';

		$theme = PP::themes();
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('visible', $visible);
		$theme->set('wrapperAttributes', $wrapperAttributes);

		$contents = $theme->output('admin/helpers/settings/discounts');

		return $contents;
	}

	/**
	 * Renders the plan settings
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function plans($name, $title, $desc = '', $options = [])
	{
		if (empty($desc)) {
			$desc = $title . '_DESC';
		}

		$theme = PP::themes();
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);

		$contents = $theme->output('admin/helpers/settings/plans');

		return $contents;
	}

	/**
	 * Renders a currency settings
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function rewriter($visible = true, $options = [])
	{
		$wrapperAttributes = \FH::normalize($options, 'wrapperAttributes', '');

		$theme = PP::themes();
		$theme->set('visible', $visible);
		$theme->set('wrapperAttributes', $wrapperAttributes);
		
		$contents = $theme->output('admin/helpers/settings/rewriter');

		return $contents;
	}

	/**
	 * Renders a textbox for settings
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function textbox($name, $title, $desc = '', $options = array(), $instructions = '', $class = '')
	{
		$theme = PP::themes();
		
		if (empty($desc)) {
			$desc = $title . '_DESC';
		}

		$size = isset($options['size']) ? $options['size'] : '';
		$postfix = isset($options['postfix']) ? $options['postfix'] : '';
		$prefix = isset($options['prefix']) ? $options['prefix'] : '';
		$attributes = isset($options['attributes']) ? $options['attributes'] : '';
		$type = isset($options['type']) ? $options['type'] : 'text';

		$theme->set('attributes', $attributes);
		$theme->set('type', $type);
		$theme->set('size', $size);
		$theme->set('class', $class);
		$theme->set('instructions', $instructions);
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('prefix', $prefix);
		$theme->set('postfix', $postfix);

		$contents = $theme->output('admin/helpers/settings/textbox');

		return $contents;
	}

	/**
	 * Renders a toggle button
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function toggle($name, $title, $desc = '', $attributes = '', $note = '', $wrapperAttributes = '', $wrapperClass = '')
	{
		$theme = PP::themes();

		if (empty($desc)) {
			$desc = $title . '_DESC';
		}
		
		if ($note) {
			$note = JText::_($note);
		}

		if (is_array($wrapperAttributes)) {
			$wrapperAttributes = implode(' ', $wrapperAttributes);
		}

		$theme->set('note', $note);
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('wrapperClass', $wrapperClass);
		$theme->set('attributes', $attributes);
		$theme->set('wrapperAttributes', $wrapperAttributes);

		$contents = $theme->output('admin/helpers/settings/toggle');

		return $contents;
	}
}

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

require_once(__DIR__ . '/abstract.php');

class PPFormRendererApps extends PPFormRendererAbstract
{
	private $sections = [];
	private $data = null;

	public function __construct($sections, $data)
	{
		$this->sections = $sections;
		$this->data = $data;
	}

	/**
	 * Renders the form's output
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function render()
	{
		if (!$this->sections) {
			return false;
		}

		// Get the items in the section
		foreach ($this->sections as &$section) {
			$section->title = isset($section->title) ? $section->title : 'COM_PP_APP_PARAMETERS';
			$section->desc = isset($section->desc) ? $section->desc : '';

			foreach ($section->items as &$field) {
				$title = $field->title;

				// The bare minimum requires the name of the field
				if (!isset($field->name)) {
					throw new Exception('Invalid name for field');
				}

				$field->id = $this->getValidKey($field->name);
				$field->title = JText::_($title);
				$field->tooltip = !isset($field->tooltip) ? $title . '_DESC' : $field->tooltip;
				$field->tooltip = JText::_($field->tooltip);
				$field->value = $this->data->get($field->name, $field->default);
				$field->attributes = FH::normalize($field, 'attributes', '');
				$field->dependents = FH::normalize($field, 'dependents', []);
				$field->options = FH::normalize($field, 'options', []);

				$multiple = FH::normalize($field, 'multiple', false);
				$allowAll = FH::normalize($field, 'allowAll', false);

				if ($multiple) {
					$field->options['multiple'] = true;
				}

				if ($allowAll) {
					$field->options['allowAll'] = true;
				}
			}
		}

		$theme = PP::themes();
		$theme->set('sections', $this->sections);

		$contents = $theme->output('admin/forms/renderer/apps');

		return $contents;
	}
}
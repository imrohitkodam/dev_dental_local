<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tjlms
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * Supports a modal article picker.
 *
 * @since  1.6
 */
class JFormFieldModal_Course extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since   1.6
	 */
	protected $type = 'Modal_Course';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string	The field input markup.
	 *
	 * @since   1.6
	 */
	protected function getInput()
	{
		$allowEdit		= ((string) $this->element['edit'] == 'true') ? true : false;
		$allowClear		= ((string) $this->element['clear'] != 'false') ? true : false;

		// Load language
		JFactory::getLanguage()->load('com_tjlms', JPATH_ADMINISTRATOR);

		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modal');

		// Build the script.
		$script = array();

		// Select button script
		$script[] = '	function jSelectCourse_' . $this->id . '(course_id, title,  object) {';
		$script[] = '		document.getElementById("' . $this->id . '_id").value = course_id;';
		$script[] = '		document.getElementById("' . $this->id . '_name").value = title;';

		$script[] = '		jModalClose();';
		$script[] = '	}';

		// Clear button script
		static $scriptClear;

		if ($allowClear && !$scriptClear)
		{
			$scriptClear = true;

			$script[] = '	function jClearCourse(id) {';
			$script[] = '		document.getElementById(id + "_course_id").value = "";';
			$script[] = '		document.getElementById(id + "_name").value = "' .
				htmlspecialchars(JText::_('COM_TJLMS_SELECT_AN_COURSE', true), ENT_COMPAT, 'UTF-8') . '";';
			$script[] = '		jQuery("#"+id + "_clear").addClass("hidden");';

			$script[] = '		return false;';
			$script[] = '	}';
		}

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Setup variables for display.
		$html	= array();
		$link	= 'index.php?option=com_tjlms&view=courses&layout=modal&tmpl=component&filter[state]=1&function=jSelectCourse_' . $this->id;

		/*if (isset($this->element['language']))
		{
			$link .= '&amp;forcedLanguage=' . $this->element['language'];
		}*/

		if ((int) $this->value > 0)
		{
			$db	= JFactory::getDbo();
			$query = $db->getQuery(true)
				->select($db->quoteName('title'))
				->from($db->quoteName('#__tjlms_courses'))
				->where($db->quoteName('id') . ' = ' . (int) $this->value);
			$db->setQuery($query);

			try
			{
				$title = $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				JError::raiseWarning(500, $e->getMessage());
			}
		}

		if (empty($title))
		{
			$title = JText::_('COM_TJLMS_SELECT_AN_COURSE');
		}

		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

		// The active article id field.
		if (0 == (int) $this->value)
		{
			$value = '';
		}
		else
		{
			$value = (int) $this->value;
		}

		// The current course display field.
		$html[] = '<span class="input-append">';
		$html[] = '<input type="text" class="input-medium" id="' . $this->id . '_name" value="' . $title . '" disabled="disabled" size="35" />';
		$html[] = '<a class="modal btn hasTooltip" title="' . JHtml::tooltipText('COM_TJLMS_CHANGE_COURSE') . '" ';
		$html[] = 'href="' . $link . '&amp;' . JSession::getFormToken() . '=1" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">';
		$html[] = '<i class="icon-file"></i> ' . JText::_('JSELECT') . '</a>';

		// Clear course button
		if ($allowClear)
		{
			$html[] = '<button id="' . $this->id . '_clear" class="btn' . ($value ? '' : ' hidden') . '" onclick="return jClearArticle(\'' .
				$this->id . '\')"><span class="icon-remove"></span>' . JText::_('JCLEAR') . '</button>';
		}

		$html[] = '</span>';

		// The class='required' for client side validation
		$class = '';

		if ($this->required)
		{
			$class = ' class="required modal-value"';
		}

		$html[] = '<input type="hidden" id="' . $this->id . '_id"' . $class . ' name="' . $this->name . '" value="' . $value . '" />';

		return implode("\n", $html);
	}

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   3.4
	 */
	protected function getLabel()
	{
		return str_replace($this->id, $this->id . '_id', parent::getLabel());
	}
}

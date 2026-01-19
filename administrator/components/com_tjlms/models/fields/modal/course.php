<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tjlms
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

/**
 * Supports a modal article picker.
 *
 * @since  1.6
 */
class JFormFieldModal_Course extends FormField
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
		Factory::getLanguage()->load('com_tjlms', JPATH_ADMINISTRATOR);
		$app  = Factory::getApplication();
		
		// Load the modal behavior script.
		if (JVERSION >= '4.0.0')
		{
			HTMLHelper::_('bootstrap.renderModal', 'a.modal');
		}
		else
		{
			HTMLHelper::_('behavior.modal', 'a.modal');
		}
		
		// Build the script.
		$script = array();

		// Select button script
		$script[] = '	function jSelectCourse_' . $this->id . '(course_id, title,  object) {';
		$script[] = '		document.getElementById("' . $this->id . '_id").value = course_id;';
		$script[] = '		document.getElementById("' . $this->id . '_name").value = title;';

		$script[] = '		window.parent.Joomla.Modal.getCurrent().close()';
		$script[] = '	}';

		// Clear button script
		static $scriptClear;

		if ($allowClear && !$scriptClear)
		{
			$scriptClear = true;

			$script[] = '	function jClearCourse(id) {';
			$script[] = '		document.getElementById(id + "_course_id").value = "";';
			$script[] = '		document.getElementById(id + "_name").value = "' .
				htmlspecialchars(Text::_('COM_TJLMS_SELECT_AN_COURSE', true), ENT_COMPAT, 'UTF-8') . '";';
			$script[] = '		jQuery("#"+id + "_clear").addClass("hidden");';

			$script[] = '		return false;';
			$script[] = '	}';
		}

		// Add the script to the document head.
		Factory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Setup variables for display.
		$html	= array();
		

		/*if (isset($this->element['language']))
		{
			$link .= '&amp;forcedLanguage=' . $this->element['language'];
		}*/

		if ((int) $this->value > 0)
		{
			$db	= Factory::getDbo();
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
				$app->enqueueMessage($e->getMessage(), 'warning');
				$app->setHeader('status', 500, true);
			}
		}

		if (empty($title))
		{
			$title = Text::_('COM_TJLMS_SELECT_AN_COURSE');
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
		$link	= 'index.php?option=com_tjlms&view=courses&layout=modal&tmpl=component&filter[state]=1&function=jSelectCourse_' . $this->id;
		$html[] = '<span class="input-append">';
		$html[] = '<input type="text" class="input-medium" id="' . $this->id . '_name" value="' . $title . '" disabled="disabled" size="35" />';	
        $html[] = '<button type="button" data-bs-target="#menuTypeModals" class="btn btn-primary" data-bs-toggle="modal">'
            . '<span class="icon-list icon-white" aria-hidden="true"></span> '
            . Text::_('JSELECT') . '</button></span>';
		$html[] = HTMLHelper::_(
            'bootstrap.renderModal',
            'menuTypeModals',
            [
                'url'        => $link,
                'title'      => Text::_('COM_TJLMS_FIELD_SELECT_COURSE_LABEL'),
                'width'      => '800px',
                'height'     => '300px',
                'modalWidth' => 80,
                'bodyHeight' => 70,
                'footer'     => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">'
                        . Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>'
            ]
        );
		// Clear course button
		if ($allowClear)
		{
			$html[] = '<button id="' . $this->id . '_clear" class="btn' . ($value ? '' : ' hidden') . '" onclick="return jClearArticle(\'' .
				$this->id . '\')"><span class="icon-remove"></span>' . Text::_('JCLEAR') . '</button>';
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

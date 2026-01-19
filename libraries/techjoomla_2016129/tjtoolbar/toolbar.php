<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Toolbar
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.html.toolbar');

if (JVERSION < '3.0')
{
	define('TJTOOLBAR_ICON_ADDNEW', " icon-plus-sign icon-white");
	define('TJTOOLBAR_ICON_EDIT', " icon-edit icon-white");
	define('TJTOOLBAR_ICON_DELETE', " icon-trash icon-white");
	define('TJTOOLBAR_ICON_PUBLISH', " icon-ok-sign icon-white");
	define('TJTOOLBAR_ICON_UNPUBLISH', " icon-minus-sign icon-white");
}
else
{
	define('TJTOOLBAR_ICON_ADDNEW', " icon-plus-2");
	define('TJTOOLBAR_ICON_EDIT', " icon-apply");
	define('TJTOOLBAR_ICON_DELETE', " icon-trash");
	define('TJTOOLBAR_ICON_PUBLISH', " icon-checkmark");
	define('TJTOOLBAR_ICON_UNPUBLISH', " icon-unpublish");
}

/**
 * ToolBar handler
 *
 * @package     Joomla.Libraries
 * @subpackage  Toolbar
 * @since       1.5
 */
class TJToolbar extends JToolbar
{
	/**
	 * Toolbar cssClass
	 *
	 * @var    string
	 */
	protected $_toolbarPositionClass = array();

	/**
	 * Constructor
	 *
	 * @param   string  $name  The toolbar name.
	 *
	 * @since   1.0
	 */
	public function __construct($name = 'toolbar', $_toolbarPositionClass = '')
	{
		$this->_name = $name;
		$this->_toolbarPositionClass = $_toolbarPositionClass;

		// Set base path to find buttons.
		$this->_buttonPath[] = __DIR__ . '/button';
	}

	/**
	 * Returns the global TJToolbar object, only creating it if it
	 * doesn't already exist.
	 *
	 * @param   string  $name  The name of the toolbar.
	 *
	 * @return  TJToolbar  The TJToolbar object.
	 *
	 * @since   1.0
	 */
	public static function getInstance($name = 'tjtoolbar', $cssClass = '')
	{
		if (empty(self::$instances[$name]))
		{
			self::$instances[$name] = new TJToolbar($name, $cssClass);
		}

		return self::$instances[$name];
	}

	/**
	 * Render a tool bar.
	 *
	 * @return  string  HTML for the toolbar.
	 *
	 * @since   1.0
	 */
	public function render()
	{
		$html = array();

		$html[] = '
		<style>
			.btn-toolbar .btn-wrapper {
				display: inline-block;
				margin: 0 0 5px 5px;
			}
		</style>';

		$html[] = '<div class="row-fluid">';
			$html[] = '<div class="span12">';
				$html[] = '<div class="btn-toolbar ' . $this->_toolbarPositionClass . '" id="' . $this->_name . '">';

					// Render each button in the toolbar.
					foreach ($this->_bar as $button)
					{
						$html[] = $this->renderButton($button);
					}

				$html[] = '</div>';
			$html[] = '</div>';
		$html[] = '</div>';

		return implode('', $html);
	}

	/**
	 * Render a button.
	 *
	 * @param   object  &$node  A toolbar node.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function renderButton(&$node)
	{
		$task     = $node[0];
		$text     = $node[1];
		$class    = $node[2];
		$btnClass = $node[3];

		// Add button 'onclick' Javascript
		$spiltTask = explode('.', $task);

		switch ($spiltTask[1])
		{
			default:
			case 'addNew':
				$task = "Joomla.submitbutton('" . $task . "')";

				if (empty($class))
				{
					$class = TJTOOLBAR_ICON_ADDNEW;
				}
			break;

			case 'edit':
				$task = "if (document.adminForm.boxchecked.value==0){alert('" . JText::_('TJTOOLBAR_NO_SELECT_MSG') . "'); } else{Joomla.submitbutton('" . $task . "')}";

				if (empty($class))
				{
					$class = TJTOOLBAR_ICON_EDIT;
				}
			break;

			case 'publish':
				$task = "if (document.adminForm.boxchecked.value==0) { alert('" . JText::_('TJTOOLBAR_NO_SELECT_MSG') . "'); } else { Joomla.submitbutton('" . $task . "') }";

				if (empty($class))
				{
					$class = TJTOOLBAR_ICON_PUBLISH;
				}
			break;

			case 'unpublish':
				$task = "if (document.adminForm.boxchecked.value==0) { alert('" . JText::_('TJTOOLBAR_NO_SELECT_MSG') . "'); } else { Joomla.submitbutton('" . $task . "') }";

				if (empty($class))
				{
					$class = TJTOOLBAR_ICON_UNPUBLISH;
				}
			break;

			case 'delete':
				$task = "if (document.adminForm.boxchecked.value==0) { alert('" . JText::_('TJTOOLBAR_NO_SELECT_MSG') . "'); } else { Joomla.submitbutton('" . $task . "')}";

				if (empty($class))
				{
					$class = TJTOOLBAR_ICON_DELETE;
				}
			break;
		}

		// Apply JText
		$text = JText::_($text);

		// Generate button HTML
		$btnHtml = ' <div class="btn-wrapper" id="tjtoolbar-' . $spiltTask[1] . '"> <button type="button" onclick="' . $task .'" class="' . $btnClass . '"> <span class="' . trim($class) . '"></span> ' . $text . ' </button> </div>';

		return $btnHtml;
	}
}

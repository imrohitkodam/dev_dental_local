<?php
/**
* @package      StackIdeas
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* StackIdeas Toolbar is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
use Foundry\Libraries\Themes;

defined('_JEXEC') or die('Unauthorized Access');

class ToolbarThemes
{
	private $template = null;
	private $tpl = null;

	public $fd = null;

	public function __construct()
	{
		// Ensure that foundry exists and is enabled on the site.
		if (!FDT::foundryExists()) {
			return JFactory::getApplication()->enqueueMessage('To utilize <b>StackIdeas Toolbar</b>, please ensure that the plugin <b>Foundry by StackIdeas</b> is enabled on the site.', 'error');
		}
		
		$adapter = FDT::getAdapter(FDT::getMainComponent());

		$this->fd = FDT::fd();
	}

	/**
	 * Responsible to include and load class of the requested theme file.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function html($namespace)
	{
		$path = FDT_INCLUDES;
		$className = 'Toolbar';
		$method = 'render';

		static $cache = [];

		$parts = explode('.', strtolower($namespace));

		if (count($parts) > 1) {
			$method = array_pop($parts);
		}

		if (!isset($cache[$namespace])) {
			$path .= '/' . strtolower(implode('/', $parts)) . '/' . strtolower(implode('/', $parts));
			$path = $path . '.php';

			if (!file_exists($path)) {
				throw new Exception('Failed to include file ' . $path . ' library for StackIdeas Toolbar.');
			}

			include_once($path);

			$className .= ucfirst(implode('', $parts));

			if (!class_exists($className)) {
				throw new Exception('Failed to load class ' . $className . ' library for StackIdeas Toolbar');
			}

			$cache[$namespace] = new $className();
		}

		$args = func_get_args();

		// Arguments to send to the method
		$args = array_splice($args, 1);

		if (!method_exists($cache[$namespace], $method)) {
			throw new Exception('Call to undefined method ->' . $method . '() in ' . $className . ' library for StackIdeas Toolbar');
		}

		return call_user_func_array(array($cache[$namespace], $method), $args);
	}

	/**
	 * Responsible to render the output of the requested theme file.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function output($tpl, $args = [], $debug = false)
	{
		$this->setTemplate($tpl);

		return $this->parse($args);
	}

	/**
	 * Formatting the path (html/script) of the requested theme file.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function setTemplate($tpl)
	{
		// Storing this for future use.
		$this->tpl = $tpl;

		$this->template = new stdClass();
		$this->template->file = FDT_THEMES . '/' . $tpl . '.php';

		$templateOverride = $this->getCurrentTemplatePath() . '/' . $tpl . '.php';

		if (file_exists($templateOverride)) {
			$this->template->file = $templateOverride;
		}
	}

	/**
	 * Parsing the template file of the requested theme file.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function parse($args = [])
	{
		// Make sure the requested theme file exists on the site.
		if (!file_exists($this->template->file)) {
			throw new Exception("Unable to locate theme file " . $this->tpl . " for StackIdeas Toolbar.");
		}

		ob_start();
		if (is_array($args)) {
			extract($args);
		}
		include($this->template->file);
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	/**
	 * Retrieves the current site template
	 *
	 * @since	1.0.1
	 * @access	public
	 */
	public function getCurrentTemplate()
	{
		$db = JFactory::getDbo();

		$query = 'SELECT `template` FROM `#__template_styles`';
		$query .= ' WHERE `home` = ' . $db->Quote(1);
		$query .= ' AND `client_id` =' . $db->Quote(0);

		$db->setQuery($query);

		$template = $db->loadResult();


		return $template;
	}

	/**
	 * Retrieves the current site template's path
	 *
	 * @since	1.0.1
	 * @access	public
	 */
	public function getCurrentTemplatePath()
	{
		// Get the custom.css override path for the current Joomla template
		$template = $this->getCurrentTemplate();

		$path = JPATH_ROOT . '/templates/' . $template . '/html/mod_stackideas_toolbar';

		return $path;
	}
}

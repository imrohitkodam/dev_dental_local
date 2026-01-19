<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');

class plgSystemEasyblogComposer extends JPlugin
{
	function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		if (!$this->exists()) {
			return;
		}

		$this->doc = JFactory::getDocument();
		$this->app = JFactory::getApplication();
		$this->input = EB::request();
	}

	/**
	 * Check if component exists
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function exists()
	{
		$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

		if (!JFile::exists($file)) {
			return false;
		}

		require_once($file);

		return true;
	}

	/**
	 * Method to process return url to be use inside the composer
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function onBeforeRender()
	{
		if (!$this->exists()) {
			return false;
		}

		if ($this->doc->getType() != 'html') {
			return;
		}

		$setUrl = true;

		// Capture current url and store it in user session for composer use.
		$currentUrl = EBR::current();

		// Get current extension and view
		$extension = $this->input->get('option', '', 'default');
		$view = $this->input->get('view', '', 'default');

		// Check for composer view
		if ($view && $extension == 'com_easyblog') {

			// Only set the url if url is not from composer
			if ($view === 'composer' || $view === 'templates') {
				$setUrl = false;
			}
		}

		if ($setUrl) {
			$composer = EB::composer();
			$composer->setReturnUrl($currentUrl);
		}
	}
}